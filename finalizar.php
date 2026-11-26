
<?php
session_start();
require_once "config/conexao.php";

// Somente usuários logados podem finalizar
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Verificar carrinho
if (empty($_SESSION['carrinho'])) {
    header("Location: carrinho.php");
    exit;
}

$mensagem = "";
$sucesso = false;
$total = 0;
$itens = [];

// Buscar produtos e calcular o total
foreach ($_SESSION['carrinho'] as $id => $quantidade) {
    $consulta = $conexao->prepare(
        "SELECT * FROM produtos WHERE id = ?"
    );
    $consulta->execute([$id]);
    $produto = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$produto ||
        $quantidade < 1 ||
        $quantidade > $produto['estoque']) {
        $mensagem = "Um produto está indisponível ou sem estoque suficiente.";
        break;
    }

    $produto['quantidade'] = (int)$quantidade;
    $produto['subtotal'] = $produto['preco'] * $quantidade;

    $itens[] = $produto;
    $total += $produto['subtotal'];
}

// Finalizar pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $mensagem === '') {

    $pagamento = $_POST['pagamento'] ?? '';

    if (!in_array($pagamento, ['PIX', 'Boleto', 'Cartão'], true)) {
        $mensagem = "Selecione uma forma de pagamento válida.";
    } else {

        try {
            $conexao->beginTransaction();

            // Conferir estoque novamente antes de vender
            $itensConfirmados = [];
            $totalConfirmado = 0;

            foreach ($_SESSION['carrinho'] as $id => $quantidade) {
                $consulta = $conexao->prepare(
                    "SELECT * FROM produtos WHERE id = ? FOR UPDATE"
                );
                $consulta->execute([$id]);
                $produto = $consulta->fetch(PDO::FETCH_ASSOC);

                if (!$produto ||
                    !is_numeric($quantidade) ||
                    (int)$quantidade < 1 ||
                    (int)$quantidade > (int)$produto['estoque']) {
                    throw new Exception(
                        "Estoque insuficiente para finalizar a compra."
                    );
                }

                $produto['quantidade'] = (int)$quantidade;
                $itensConfirmados[] = $produto;

                $totalConfirmado +=
                    $produto['preco'] * $produto['quantidade'];
            }

            // Criar pedido
            $sql = "INSERT INTO pedidos
                    (usuario_id, forma_pagamento, total)
                    VALUES (?, ?, ?)";

            $consulta = $conexao->prepare($sql);
            $consulta->execute([
                $_SESSION['usuario_id'],
                $pagamento,
                $totalConfirmado
            ]);

            $pedidoId = $conexao->lastInsertId();

            foreach ($itensConfirmados as $produto) {

                // Registrar item do pedido
                $sql = "INSERT INTO itens_pedido
                        (pedido_id, produto_id, quantidade, preco_unitario)
                        VALUES (?, ?, ?, ?)";

                $consulta = $conexao->prepare($sql);
                $consulta->execute([
                    $pedidoId,
                    $produto['id'],
                    $produto['quantidade'],
                    $produto['preco']
                ]);

                // Diminuir estoque
                $sql = "UPDATE produtos
                        SET estoque = estoque - ?
                        WHERE id = ?";

                $consulta = $conexao->prepare($sql);
                $consulta->execute([
                    $produto['quantidade'],
                    $produto['id']
                ]);
            }

            $conexao->commit();

            // Limpar carrinho
            $_SESSION['carrinho'] = [];
            $sucesso = true;

        } catch (Exception $erro) {
            if ($conexao->inTransaction()) {
                $conexao->rollBack();
            }

            $mensagem = $erro->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Finalizar Compra - Infinity Mobile</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

<!-- MENU -->
<nav class="navbar navbar-dark">
    <div class="container">
        <a href="index.php" class="navbar-brand">
            ∞ Infinity Mobile
        </a>

        <a href="carrinho.php" class="nav-link">
            Carrinho
        </a>
    </div>
</nav>

<div class="container my-5">

    <?php if ($sucesso): ?>

        <div class="bg-white rounded shadow-sm p-5 text-center">
            <h2 class="text-success">
                Compra realizada com sucesso!
            </h2>

            <p class="mt-3">
                Seu pedido foi registrado na Infinity Mobile.
            </p>

            <p>
                Número do pedido:
                <strong>#<?= (int)$pedidoId ?></strong>
            </p>

            <p class="text-muted">
                Pagamento simulado para fins escolares.
                Nenhuma cobrança real foi realizada.
            </p>

            <a href="index.php" class="btn btn-primary mt-3">
                Voltar para a loja
            </a>
        </div>

    <?php else: ?>

        <h2 class="fw-bold mb-4">Finalizar Compra</h2>

        <div class="row g-4">

            <div class="col-md-7">
                <div class="bg-white rounded shadow-sm p-4">

                    <h4>Olá,
                        <?= htmlspecialchars($_SESSION['usuario_nome']) ?>!
                    </h4>

                    <p>Confira seu pedido e escolha o pagamento.</p>

                    <?php if ($mensagem): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($mensagem) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">

                        <label class="form-label fw-bold">
                            Forma de pagamento
                        </label>

                        <select name="pagamento"
                                class="form-select mb-4"
                                required>
                            <option value="">
                                Selecione uma opção
                            </option>
                            <option value="PIX">PIX</option>
                            <option value="Boleto">Boleto</option>
                            <option value="Cartão">Cartão</option>
                        </select>

                        <button type="submit"
                                class="btn btn-primary w-100"
                                <?= $mensagem ? 'disabled' : '' ?>>
                            Confirmar Compra
                        </button>

                    </form>

                    <a href="carrinho.php"
                       class="btn btn-outline-secondary w-100 mt-3">
                        Voltar ao carrinho
                    </a>

                </div>
            </div>

            <div class="col-md-5">
                <div class="bg-white rounded shadow-sm p-4">

                    <h4 class="mb-3">Resumo do Pedido</h4>

                    <?php foreach ($itens as $item): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span>
                                <?= htmlspecialchars($item['nome']) ?>
                                (<?= $item['quantidade'] ?>x)
                            </span>

                            <span>
                                R$ <?= number_format(
                                    $item['subtotal'], 2, ',', '.'
                                ) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>

                    <hr>

                    <h4 class="text-end preco">
                        Total: R$ <?= number_format(
                            $total, 2, ',', '.'
                        ) ?>
                    </h4>

                </div>
            </div>

        </div>

    <?php endif; ?>

</div>

<footer>
    <p class="mb-0">
        © 2026 Infinity Mobile - Projeto Escolar
    </p>
</footer>
<script src="js/script.js"></script>

</body>
</html>

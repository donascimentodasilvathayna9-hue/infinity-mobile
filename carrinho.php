
<?php
session_start();
require_once "config/conexao.php";

// Criar o carrinho
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

$mensagem = "";

// Ações do carrinho
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $acao = $_POST['acao'] ?? '';
    $id = filter_input(
        INPUT_POST,
        'produto_id',
        FILTER_VALIDATE_INT
    );

    if ($id && $id > 0) {

        // Buscar produto no banco
        $sql = "SELECT id, estoque FROM produtos WHERE id = ?";
        $consulta = $conexao->prepare($sql);
        $consulta->execute([$id]);
        $produto = $consulta->fetch(PDO::FETCH_ASSOC);

        if ($acao === 'remover') {

            unset($_SESSION['carrinho'][$id]);

        } elseif ($produto) {

            $estoque = (int)$produto['estoque'];
            $atual = (int)(
                $_SESSION['carrinho'][$id] ?? 0
            );

            if ($acao === 'adicionar') {

                if ($atual < $estoque) {
                    $_SESSION['carrinho'][$id] = $atual + 1;
                } else {
                    $mensagem = "Estoque insuficiente!";
                }

            } elseif ($acao === 'atualizar') {

                $quantidade = filter_input(
                    INPUT_POST,
                    'quantidade',
                    FILTER_VALIDATE_INT
                );

                if ($quantidade !== false &&
                    $quantidade !== null &&
                    $quantidade >= 1 &&
                    $quantidade <= $estoque) {

                    $_SESSION['carrinho'][$id] = $quantidade;

                } else {
                    $mensagem = "Quantidade inválida ou sem estoque!";
                }
            }
        }
    }

    if ($mensagem === "") {
        header("Location: carrinho.php");
        exit;
    }
}

// Buscar informações dos produtos
$itens = [];
$total = 0;

foreach ($_SESSION['carrinho'] as $id => $quantidade) {

    $sql = "SELECT * FROM produtos WHERE id = ?";
    $consulta = $conexao->prepare($sql);
    $consulta->execute([$id]);

    $produto = $consulta->fetch(PDO::FETCH_ASSOC);

    if ($produto) {
        $produto['quantidade'] = $quantidade;
        $produto['subtotal'] =
            $produto['preco'] * $quantidade;

        $itens[] = $produto;
        $total += $produto['subtotal'];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Carrinho - Infinity Mobile</title>

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

        <div class="d-flex gap-3">
            <a href="index.php" class="nav-link">Início</a>
            <a href="carrinho.php" class="nav-link">Carrinho</a>
        </div>
    </div>
</nav>

<div class="container my-5">

    <h2 class="fw-bold mb-4">Meu Carrinho</h2>

    <?php if ($mensagem): ?>
        <div class="alert alert-warning">
            <?= htmlspecialchars($mensagem) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($itens)): ?>

        <div class="text-center bg-white p-5 rounded shadow-sm">
            <h4>Seu carrinho está vazio!</h4>
            <p>Escolha um iPhone para começar.</p>

            <a href="index.php" class="btn btn-primary">
                Ver iPhones
            </a>
        </div>

    <?php else: ?>

        <div class="table-responsive">
            <table class="table table-bordered bg-white align-middle">

                <thead class="table-light">
                    <tr>
                        <th>Produto</th>
                        <th>Preço</th>
                        <th>Quantidade</th>
                        <th>Subtotal</th>
                        <th>Ação</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($itens as $item): ?>

                        <tr>
                            <td>
                                <img
                                    src="img/<?= htmlspecialchars($item['imagem']) ?>"
                                    width="65"
                                    alt="<?= htmlspecialchars($item['nome']) ?>"
                                >

                                <?= htmlspecialchars($item['nome']) ?>
                            </td>

                            <td>
                                R$ <?= number_format(
                                    $item['preco'], 2, ',', '.'
                                ) ?>
                            </td>

                            <td>
                                <form method="POST"
                                      class="d-flex gap-2">

                                    <input type="hidden"
                                           name="acao"
                                           value="atualizar">

                                    <input type="hidden"
                                           name="produto_id"
                                           value="<?= $item['id'] ?>">

                                    <input type="number"
                                           name="quantidade"
                                           min="1"
                                           max="<?= $item['estoque'] ?>"
                                           value="<?= $item['quantidade'] ?>"
                                           class="form-control"
                                           style="width: 85px;">

                                    <button class="btn btn-primary btn-sm">
                                        Atualizar
                                    </button>
                                </form>
                            </td>

                            <td>
                                R$ <?= number_format(
                                    $item['subtotal'], 2, ',', '.'
                                ) ?>
                            </td>

                            <td>
                                <form method="POST">
                                    <input type="hidden"
                                           name="acao"
                                           value="remover">

                                    <input type="hidden"
                                           name="produto_id"
                                           value="<?= $item['id'] ?>">

                                    <button class="btn btn-danger btn-sm">
                                        Remover
                                    </button>
                                </form>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                </tbody>
            </table>
        </div>

        <div class="bg-white p-4 rounded shadow-sm text-end">

            <h3 class="fw-bold">
                Total: R$ <?= number_format(
                    $total, 2, ',', '.'
                ) ?>
            </h3>

            <a href="index.php"
               class="btn btn-outline-secondary mt-3">
                Continuar comprando
            </a>

            <a href="finalizar.php"
               class="btn btn-primary mt-3">
                Finalizar compra
            </a>

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

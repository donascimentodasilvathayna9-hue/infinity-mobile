
<?php
session_start();
require_once "../config/conexao.php";

// Permitir somente administradores
if (!isset($_SESSION['usuario_id']) ||
    ($_SESSION['usuario_tipo'] ?? '') !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Buscar pedidos e clientes
$sql = "SELECT pedidos.*, usuarios.nome AS cliente
        FROM pedidos
        INNER JOIN usuarios ON pedidos.usuario_id = usuarios.id
        ORDER BY pedidos.id DESC";

$consulta = $conexao->query($sql);
$pedidos = $consulta->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pedidos - Infinity Mobile</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet" href="../css/estilo.css">
</head>

<body>

<nav class="navbar navbar-dark">
    <div class="container">
        <a href="index.php" class="navbar-brand">
            ∞ Infinity Mobile | Admin
        </a>

        <a href="index.php" class="nav-link">
            Voltar ao painel
        </a>
    </div>
</nav>

<div class="container my-5">

    <h2 class="fw-bold mb-4">Pedidos Realizados</h2>

    <?php if (empty($pedidos)): ?>

        <div class="alert alert-info">
            Nenhum pedido realizado até o momento.
        </div>

    <?php else: ?>

        <div class="table-responsive">

            <table class="table table-bordered bg-white align-middle">

                <thead class="table-light">
                    <tr>
                        <th>Pedido</th>
                        <th>Cliente</th>
                        <th>Data</th>
                        <th>Produtos</th>
                        <th>Pagamento</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($pedidos as $pedido): ?>

                        <?php
                        $sql = "SELECT itens_pedido.*, produtos.nome
                                FROM itens_pedido
                                INNER JOIN produtos
                                ON itens_pedido.produto_id = produtos.id
                                WHERE pedido_id = ?";

                        $consulta = $conexao->prepare($sql);
                        $consulta->execute([$pedido['id']]);
                        $itens = $consulta->fetchAll(PDO::FETCH_ASSOC);
                        ?>

                        <tr>

                            <td>#<?= $pedido['id'] ?></td>

                            <td>
                                <?= htmlspecialchars($pedido['cliente']) ?>
                            </td>

                            <td>
                                <?= date('d/m/Y H:i',
                                    strtotime($pedido['data_pedido'])) ?>
                            </td>

                            <td>
                                <?php foreach ($itens as $item): ?>
                                    <div>
                                        <?= htmlspecialchars($item['nome']) ?>
                                        (<?= $item['quantidade'] ?>x)
                                    </div>
                                <?php endforeach; ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($pedido['forma_pagamento']) ?>
                            </td>

                            <td class="fw-bold">
                                R$ <?= number_format(
                                    $pedido['total'], 2, ',', '.'
                                ) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

    <a href="index.php" class="btn btn-primary mt-3">
        Voltar ao painel
    </a>

</div>

<footer>
    <p class="mb-0">
        © 2026 Infinity Mobile - Projeto Escolar
    </p>
</footer>

</body>
</html>


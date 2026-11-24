
<?php
session_start();
require_once "../config/conexao.php";

// Permitir acesso somente ao administrador
if (
    !isset($_SESSION['usuario_id']) ||
    ($_SESSION['usuario_tipo'] ?? '') !== 'admin'
) {
    header("Location: ../login.php");
    exit;
}

// Contar produtos
$consulta = $conexao->query(
    "SELECT COUNT(*) FROM produtos"
);
$totalProdutos = $consulta->fetchColumn();

// Contar pedidos
$consulta = $conexao->query(
    "SELECT COUNT(*) FROM pedidos"
);
$totalPedidos = $consulta->fetchColumn();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Painel Administrativo - Infinity Mobile</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet" href="../css/estilo.css">
</head>

<body>

<!-- MENU -->
<nav class="navbar navbar-dark">
    <div class="container">
        <a href="index.php" class="navbar-brand">
            ∞ Infinity Mobile | Admin
        </a>

        <div class="d-flex gap-3">
            <a href="../index.php" class="nav-link">Ver loja</a>
            <a href="../logout.php" class="nav-link">Sair</a>
        </div>
    </div>
</nav>

<!-- PAINEL -->
<div class="container my-5">

    <h2 class="fw-bold mb-2">
        Painel Administrativo
    </h2>

    <p class="text-muted mb-4">
        Bem-vinda,
        <?= htmlspecialchars($_SESSION['usuario_nome']) ?>!
    </p>

    <div class="row g-4">

        <!-- PRODUTOS -->
        <div class="col-md-6">
            <div class="card shadow-sm p-4">

                <h4>Gerenciar Produtos</h4>

                <p class="text-muted">
                    Cadastre, edite e exclua os iPhones da loja.
                </p>

                <h2 class="preco">
                    <?= $totalProdutos ?> produtos
                </h2>

                <a href="produtos.php"
                   class="btn btn-primary mt-3">
                    Gerenciar Produtos
                </a>

            </div>
        </div>

        <!-- PEDIDOS -->
        <div class="col-md-6">
            <div class="card shadow-sm p-4">

                <h4>Pedidos Realizados</h4>

                <p class="text-muted">
                    Consulte as compras feitas pelos clientes.
                </p>

                <h2 class="preco">
                    <?= $totalPedidos ?> pedidos
                </h2>

                <a href="pedidos.php"
                   class="btn btn-primary mt-3">
                    Visualizar Pedidos
                </a>

            </div>
        </div>

    </div>

</div>

<footer>
    <p class="mb-0">
        © 2026 Infinity Mobile - Projeto Escolar
    </p>
</footer>

</body>
</html>


<?php
session_start();
require_once "config/conexao.php";

// Buscar categorias
$consultaCategorias = $conexao->query(
    "SELECT * FROM categorias ORDER BY id"
);
$categorias = $consultaCategorias->fetchAll(PDO::FETCH_ASSOC);

// Receber busca e categoria
$busca = trim($_GET['busca'] ?? '');
$categoria = $_GET['categoria'] ?? '';

// Buscar produtos com filtros
$sql = "SELECT produtos.*, categorias.nome AS categoria_nome
        FROM produtos
        INNER JOIN categorias
        ON produtos.categoria_id = categorias.id
        WHERE 1=1";

$parametros = [];

if ($busca !== '') {
    $sql .= " AND produtos.nome LIKE :busca";
    $parametros[':busca'] = "%$busca%";
}

if (ctype_digit((string)$categoria) && (int)$categoria > 0) {
    $sql .= " AND produtos.categoria_id = :categoria";
    $parametros[':categoria'] = (int)$categoria;
}

$sql .= " ORDER BY produtos.id";

$consulta = $conexao->prepare($sql);
$consulta->execute($parametros);
$produtos = $consulta->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Infinity Mobile - Loja de iPhones</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

<!-- MENU -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            ∞ Infinity Mobile
        </a>

        <div class="d-flex gap-3 align-items-center">
            <a href="index.php" class="nav-link">Início</a>

            <?php if (isset($_SESSION['usuario_id'])): ?>
                <a href="carrinho.php" class="nav-link">Carrinho</a>
                <a href="logout.php" class="nav-link">Sair</a>
            <?php else: ?>
                <a href="login.php" class="nav-link">Entrar</a>
                <a href="cadastro.php" class="nav-link">Cadastrar</a>
                <a href="carrinho.php" class="nav-link">Carrinho</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- BANNER -->
<section class="banner">
    <div class="container">
        <h1>Infinity Mobile</h1>
        <p>Seu próximo iPhone está aqui.</p>
        <p>Encontre o modelo ideal para você!</p>
    </div>
</section>

<!-- VITRINE -->
<main class="container my-5">

    <h2 class="text-center mb-4">Nossos iPhones</h2>

    <!-- FILTROS -->
    <form method="GET" class="row g-3 mb-4">

        <div class="col-md-5">
            <input
                type="text"
                name="busca"
                class="form-control"
                placeholder="Buscar iPhone..."
                value="<?= htmlspecialchars($busca) ?>"
            >
        </div>

        <div class="col-md-5">
            <select name="categoria" class="form-select">
                <option value="">Todas as categorias</option>

                <?php foreach ($categorias as $cat): ?>
                    <option
                        value="<?= $cat['id'] ?>"
                        <?= (string)$categoria === (string)$cat['id']
                            ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($cat['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary w-100">
                Buscar
            </button>
        </div>

    </form>

    <!-- LISTA DE PRODUTOS -->
    <div class="row g-4">

        <?php foreach ($produtos as $produto): ?>

            <div class="col-md-4">

                <div class="card shadow-sm">

                    <img
                        src="img/<?= htmlspecialchars($produto['imagem']) ?>"
                        class="card-img-top"
                        alt="<?= htmlspecialchars($produto['nome']) ?>"
                    >

                    <div class="card-body">

                        <small class="text-muted">
                            <?= htmlspecialchars($produto['categoria_nome']) ?>
                        </small>

                        <h5 class="card-title mt-2">
                            <?= htmlspecialchars($produto['nome']) ?>
                        </h5>

                        <p class="preco">
                            R$ <?= number_format(
                                $produto['preco'], 2, ',', '.'
                            ) ?>
                        </p>

                        <p>
                            <?php if ($produto['estoque'] > 0): ?>
                                <span class="text-success">
                                    Em estoque
                                </span>
                            <?php else: ?>
                                <span class="text-danger">
                                    Esgotado
                                </span>
                            <?php endif; ?>
                        </p>

                        <a
                            href="produto.php?id=<?= $produto['id'] ?>"
                            class="btn btn-primary w-100"
                        >
                            Ver detalhes
                        </a>

                    </div>
                </div>

            </div>

        <?php endforeach; ?>

    </div>

    <?php if (empty($produtos)): ?>
        <p class="text-center mt-4">
            Nenhum iPhone encontrado.
        </p>
    <?php endif; ?>

</main>

<!-- RODAPÉ -->
<footer>
    <p class="mb-0">
        © 2026 Infinity Mobile - Projeto Escolar
    </p>
</footer>
<script src="js/script.js"></script>

</body>
</html>



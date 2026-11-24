
<?php
session_start();
require_once "config/conexao.php";

// Identificar o produto selecionado
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: index.php");
    exit;
}

// Buscar o produto no banco de dados
$sql = "SELECT produtos.*, categorias.nome AS categoria_nome
        FROM produtos
        INNER JOIN categorias
        ON produtos.categoria_id = categorias.id
        WHERE produtos.id = ?";

$consulta = $conexao->prepare($sql);
$consulta->execute([$id]);

$produto = $consulta->fetch(PDO::FETCH_ASSOC);

// Caso o produto não exista
if (!$produto) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= htmlspecialchars($produto['nome']) ?> - Infinity Mobile
    </title>

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

<!-- DETALHES DO IPHONE -->
<div class="container my-5">

    <div class="row align-items-center g-5">

        <!-- FOTO -->
        <div class="col-md-6 text-center">

            <div class="bg-white rounded p-4 shadow-sm">
                <img
                    src="img/<?= htmlspecialchars($produto['imagem']) ?>"
                    alt="<?= htmlspecialchars($produto['nome']) ?>"
                    class="img-fluid"
                    style="max-height: 400px; object-fit: contain;"
                >
            </div>

        </div>

        <!-- INFORMAÇÕES -->
        <div class="col-md-6">

            <p class="text-muted">
                <?= htmlspecialchars($produto['categoria_nome']) ?>
            </p>

            <h1 class="fw-bold">
                <?= htmlspecialchars($produto['nome']) ?>
            </h1>

            <p class="mt-3">
                <?= htmlspecialchars($produto['descricao'] ?? '') ?>
            </p>

            <h2 class="preco my-4">
                R$ <?= number_format(
                    $produto['preco'], 2, ',', '.'
                ) ?>
            </h2>

            <?php if ($produto['estoque'] > 0): ?>

                <p class="text-success">
                    Disponível em estoque:
                    <?= $produto['estoque'] ?> unidade(s)
                </p>

                <form action="carrinho.php" method="POST">

                    <input
                        type="hidden"
                        name="produto_id"
                        value="<?= $produto['id'] ?>"
                    >

                    <input
                        type="hidden"
                        name="acao"
                        value="adicionar"
                    >

                    <button
                        type="submit"
                        class="btn btn-primary btn-lg w-100"
                    >
                        Adicionar ao carrinho
                    </button>

                </form>

            <?php else: ?>

                <p class="text-danger fw-bold">
                    Produto esgotado
                </p>

                <button class="btn btn-secondary w-100" disabled>
                    Indisponível
                </button>

            <?php endif; ?>

            <a href="index.php"
               class="btn btn-outline-secondary mt-3 w-100">
                Voltar para a loja
            </a>

        </div>
    </div>
</div>

<!-- RODAPÉ -->
<footer>
    <p class="mb-0">
        © 2026 Infinity Mobile - Projeto Escolar
    </p>
</footer>

</body>
</html>




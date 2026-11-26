
<?php
session_start();
require_once "../config/conexao.php";

// Somente administradores
if (!isset($_SESSION['usuario_id']) ||
    ($_SESSION['usuario_tipo'] ?? '') !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$mensagem = "";
$erro = "";
$editando = null;

// Buscar categorias
$categorias = $conexao->query(
    "SELECT * FROM categorias ORDER BY id"
)->fetchAll(PDO::FETCH_ASSOC);

// Cadastrar ou editar produto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'salvar') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nome = trim($_POST['nome'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $categoria = filter_input(
            INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT
        );
        $preco = filter_input(
            INPUT_POST, 'preco', FILTER_VALIDATE_FLOAT
        );
        $estoque = filter_input(
            INPUT_POST, 'estoque', FILTER_VALIDATE_INT
        );
        $imagem = basename(trim($_POST['imagem'] ?? ''));

        if ($nome === '' || !$categoria ||
            $preco === false || $preco === null || $preco < 0 ||
            $estoque === false || $estoque === null || $estoque < 0 ||
            $imagem === '') {
            $erro = "Preencha todos os campos corretamente.";
        } else {
            if ($id) {
                $sql = "UPDATE produtos
                        SET nome = ?, descricao = ?, categoria_id = ?,
                            preco = ?, estoque = ?, imagem = ?
                        WHERE id = ?";

                $dados = [
                    $nome, $descricao, $categoria,
                    $preco, $estoque, $imagem, $id
                ];
            } else {
                $sql = "INSERT INTO produtos
                        (nome, descricao, categoria_id, preco, estoque, imagem)
                        VALUES (?, ?, ?, ?, ?, ?)";

                $dados = [
                    $nome, $descricao, $categoria,
                    $preco, $estoque, $imagem
                ];
            }

            try {
                $consulta = $conexao->prepare($sql);
                $consulta->execute($dados);

                header("Location: produtos.php?sucesso=1");
                exit;
            } catch (PDOException $e) {
                $erro = "Não foi possível salvar o produto.";
            }
        }
    }

    // Excluir produto
    if ($acao === 'excluir') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if ($id) {
            try {
                $consulta = $conexao->prepare(
                    "DELETE FROM produtos WHERE id = ?"
                );
                $consulta->execute([$id]);

                header("Location: produtos.php?excluido=1");
                exit;
            } catch (PDOException $e) {
                $erro = "Este produto possui pedidos registrados e não pode ser excluído.";
            }
        }
    }
}

// Carregar produto para edição
$idEditar = filter_input(INPUT_GET, 'editar', FILTER_VALIDATE_INT);

if ($idEditar) {
    $consulta = $conexao->prepare(
        "SELECT * FROM produtos WHERE id = ?"
    );
    $consulta->execute([$idEditar]);
    $editando = $consulta->fetch(PDO::FETCH_ASSOC);
}

// Listar produtos
$produtos = $conexao->query(
    "SELECT produtos.*, categorias.nome AS categoria_nome
     FROM produtos
     INNER JOIN categorias ON produtos.categoria_id = categorias.id
     ORDER BY produtos.id"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gerenciar Produtos - Infinity Mobile</title>

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

    <h2 class="fw-bold mb-4">Gerenciar Produtos</h2>

    <?php if (isset($_GET['sucesso'])): ?>
        <div class="alert alert-success">
            Produto salvo com sucesso!
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['excluido'])): ?>
        <div class="alert alert-success">
            Produto excluído com sucesso!
        </div>
    <?php endif; ?>

    <?php if ($erro): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($erro) ?>
        </div>
    <?php endif; ?>

    <!-- FORMULÁRIO -->
    <div class="bg-white p-4 rounded shadow-sm mb-5">

        <h4 class="mb-4">
            <?= $editando ? 'Editar iPhone' : 'Cadastrar iPhone' ?>
        </h4>

        <form method="POST">

            <input type="hidden" name="acao" value="salvar">

            <input type="hidden" name="id"
                   value="<?= $editando['id'] ?? '' ?>">

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Nome do iPhone</label>
                    <input type="text" name="nome"
                           class="form-control"
                           value="<?= htmlspecialchars($editando['nome'] ?? '') ?>"
                           required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Categoria</label>
                    <select name="categoria_id"
                            class="form-select" required>

                        <option value="">Selecione</option>

                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>"
                                <?= ($editando['categoria_id'] ?? '') == $cat['id']
                                    ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nome']) ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Descrição</label>
                    <textarea name="descricao"
                              class="form-control"
                              rows="3"><?= htmlspecialchars($editando['descricao'] ?? '') ?></textarea>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Preço (R$)</label>
                    <input type="number" name="preco"
                           class="form-control"
                           step="0.01" min="0"
                           value="<?= htmlspecialchars($editando['preco'] ?? '') ?>"
                           required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Estoque</label>
                    <input type="number" name="estoque"
                           class="form-control"
                           min="0"
                           value="<?= htmlspecialchars($editando['estoque'] ?? '') ?>"
                           required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Nome da imagem</label>
                    <input type="text" name="imagem"
                           class="form-control"
                           placeholder="iphone13.jpg"
                           value="<?= htmlspecialchars($editando['imagem'] ?? '') ?>"
                           required>
                </div>

            </div>

            <button type="submit" class="btn btn-primary mt-4">
                <?= $editando ? 'Salvar Alterações' : 'Cadastrar Produto' ?>
            </button>

            <?php if ($editando): ?>
                <a href="produtos.php"
                   class="btn btn-outline-secondary mt-4">
                    Cancelar
                </a>
            <?php endif; ?>

        </form>
    </div>

    <!-- LISTA DE PRODUTOS -->
    <h4 class="mb-3">iPhones Cadastrados</h4>

    <div class="table-responsive">
        <table class="table table-bordered bg-white align-middle">

            <thead class="table-light">
                <tr>
                    <th>Foto</th>
                    <th>Produto</th>
                    <th>Categoria</th>
                    <th>Preço</th>
                    <th>Estoque</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($produtos as $produto): ?>
                    <tr>
                        <td>
                            <img
                                src="../img/<?= htmlspecialchars($produto['imagem']) ?>"
                                width="55"
                                alt="<?= htmlspecialchars($produto['nome']) ?>"
                            >
                        </td>

                        <td>
                            <?= htmlspecialchars($produto['nome']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($produto['categoria_nome']) ?>
                        </td>

                        <td>
                            R$ <?= number_format($produto['preco'], 2, ',', '.') ?>
                        </td>

                        <td>
                            <?= $produto['estoque'] ?>
                        </td>

                        <td>
                            <div class="d-flex gap-2">

                                <a href="produtos.php?editar=<?= $produto['id'] ?>"
                                   class="btn btn-primary btn-sm">
                                    Editar
                                </a>

                               <form method="POST" class="form-excluir">
                                      

                                    <input type="hidden"
                                           name="acao" value="excluir">

                                    <input type="hidden"
                                           name="id"
                                           value="<?= $produto['id'] ?>">

                                    <button type="submit"
                                            class="btn btn-danger btn-sm">
                                        Excluir
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
    </div>

</div>

<footer>
    <p class="mb-0">
        © 2026 Infinity Mobile - Projeto Escolar
    </p>
</footer>
<script src="../js/script.js"></script>

</body>
</html>

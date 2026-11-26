
<?php
session_start();
require_once "config/conexao.php";

$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $consulta = $conexao->prepare($sql);
    $consulta->execute([$email]);

    $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        session_regenerate_id(true);

        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_tipo'] = $usuario['tipo'];

        if ($usuario['tipo'] === 'admin') {
            header("Location: admin/index.php");
        } else {
            header("Location: index.php");
        }
        exit;
    } else {
        $mensagem = "E-mail ou senha incorretos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Infinity Mobile</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<nav class="navbar navbar-dark">
    <div class="container">
        <a href="index.php" class="navbar-brand">∞ Infinity Mobile</a>
        <a href="index.php" class="nav-link">Início</a>
    </div>
</nav>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">

            <div class="bg-white p-4 rounded shadow-sm">
                <h2 class="text-center mb-4">Entrar na conta</h2>

                <?php if (isset($_GET['cadastro'])): ?>
                    <div class="alert alert-success">
                        Cadastro realizado! Faça seu login.
                    </div>
                <?php endif; ?>

                <?php if ($mensagem): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($mensagem) ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">E-mail</label>
                        <input type="email" name="email"
                               class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Senha</label>
                        <input type="password" name="senha"
                               class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        Entrar
                    </button>
                </form>

                <p class="text-center mt-3 mb-0">
                    Não tem conta?
                    <a href="cadastro.php">Cadastre-se</a>
                </p>
            </div>

        </div>
    </div>
</div>
<script src="js/script.js"></script>

</body>
</html>

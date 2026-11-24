
<?php
session_start();
require_once "config/conexao.php";

$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)
        || strlen($senha) < 6) {
        $mensagem = "Preencha os dados corretamente. A senha deve ter pelo menos 6 caracteres.";
    } else {
        $consulta = $conexao->prepare(
            "SELECT id FROM usuarios WHERE email = ?"
        );
        $consulta->execute([$email]);

        if ($consulta->fetch()) {
            $mensagem = "Este e-mail já está cadastrado!";
        } else {
            $senhaCriptografada = password_hash($senha, PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuarios (nome, email, senha, tipo)
                    VALUES (?, ?, ?, 'cliente')";

            $consulta = $conexao->prepare($sql);
            $consulta->execute([$nome, $email, $senhaCriptografada]);

            header("Location: login.php?cadastro=sucesso");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Infinity Mobile</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<nav class="navbar navbar-dark">
    <div class="container">
        <a href="index.php" class="navbar-brand">∞ Infinity Mobile</a>
        <a href="login.php" class="nav-link">Entrar</a>
    </div>
</nav>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">

            <div class="bg-white p-4 rounded shadow-sm">
                <h2 class="text-center mb-4">Criar conta</h2>

                <?php if ($mensagem): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($mensagem) ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nome completo</label>
                        <input type="text" name="nome"
                               class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">E-mail</label>
                        <input type="email" name="email"
                               class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Senha</label>
                        <input type="password" name="senha"
                               class="form-control" minlength="6" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        Cadastrar
                    </button>
                </form>

                <p class="text-center mt-3 mb-0">
                    Já tem conta?
                    <a href="login.php">Entrar</a>
                </p>
            </div>

        </div>
    </div>
</div>

</body>
</html>

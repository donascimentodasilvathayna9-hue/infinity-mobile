
<?php
// Carregar as configurações do arquivo .env
$env = parse_ini_file(__DIR__ . '/../.env');

if ($env === false) {
    die("Arquivo .env não encontrado!");
}

$host = $env['DB_HOST'];
$banco = $env['DB_NAME'];
$usuario = $env['DB_USER'];
$senha = $env['DB_PASS'];

try {
    $conexao = new PDO(
        "mysql:host=$host;dbname=$banco;charset=utf8mb4",
        $usuario,
        $senha
    );

    $conexao->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $erro) {
    error_log($erro->getMessage());
    die("Erro ao conectar ao banco de dados.");
}
?>

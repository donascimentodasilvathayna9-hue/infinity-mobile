
<?php
session_start();

// Encerrar a sessão do usuário
$_SESSION = [];
session_destroy();

header("Location: index.php");
exit;
?>

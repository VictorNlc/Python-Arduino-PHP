<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    session_destroy();
    header("Location: login.php");
    exit();
} else {
    // Redireciona para a página principal ou exibe uma mensagem de erro
    header("Location: index.php"); // Ou outra página que você definir
    exit();
}
?>
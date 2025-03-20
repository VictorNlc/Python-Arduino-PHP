<?php
include 'config.php';
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["tipo_usuario"] != "barbeiro") {
    header("Location: login.php");
    exit();
}

try {
    $conn = get_db_connection();

    if (isset($_GET['id'])) {
        $id_agendamento = $_GET['id'];

        $stmt_excluir = $conn->prepare("DELETE FROM agendamentos WHERE id = ?");
        $stmt_excluir->bind_param("i", $id_agendamento);

        if ($stmt_excluir->execute()) {
            header("Location: barbeiro.php?success=1");
            exit();
        } else {
            header("Location: barbeiro.php?error=1");
            exit();
        }

        $stmt_excluir->close();
    } else {
        header("Location: barbeiro.php?error=4");
        exit();
    }

    $conn->close();
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    header("Location: barbeiro.php?error=5");
    exit();
}
?>
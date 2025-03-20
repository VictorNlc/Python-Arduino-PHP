<?php
include 'config.php'; // Certifique-se de que config.php inclui get_db_connection()
session_start();
$error_message = "";
$success_message = "";

try {
    $conn = get_db_connection();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nome = $_POST["nome"];
        $email = $_POST["email"];
        $senha = $_POST["senha"];
        $confirmar_senha = $_POST["confirmar_senha"];

        if (empty($nome) || empty($email) || empty($senha) || empty($confirmar_senha)) {
            $error_message = "Todos os campos são obrigatórios.";
        } elseif ($senha != $confirmar_senha) {
            $error_message = "As senhas não coincidem.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Formato de email inválido.";
        } else {
            // Verificar a força da senha (opcional)
            if (strlen($senha) < 8) {
                $error_message = "A senha deve ter pelo menos 8 caracteres.";
            } else {
                $check_email_sql = "SELECT email FROM clientes WHERE email = ?";
                $stmt = $conn->prepare($check_email_sql);
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $error_message = "Email já existe. Por favor, faça login.";
                } else {
                    $hashed_password = password_hash($senha, PASSWORD_DEFAULT);

                    $stmt = $conn->prepare("INSERT INTO clientes (nome, email, senha) VALUES (?, ?, ?)");
                    $stmt->bind_param("sss", $nome, $email, $hashed_password);

                    if ($stmt->execute()) {
                        $success_message = "Novo registro criado com sucesso!";
                        $_SESSION['user_id'] = $stmt->insert_id;
                        $_SESSION['user_name'] = $nome;
                        $_SESSION['user_email'] = $email;
                        header("Location: /REWORK/agendarHorario.php"); // Redireciona para index.php
                        exit();
                    } else {
                        $error_message = "Erro durante o cadastro: " . $stmt->error;
                    }
                }
                $stmt->close();
            }
        }
    }
} catch (Exception $e) {
    $error_message = "Erro ao processar a solicitação: " . $e->getMessage();
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barbearia - Cadastro</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="reset.css">
</head>

<body>
    <header>
        <div class="logo">
            <img src="logo.png" alt="Cortes Command">
            <h1>Barbearia - Sistema de Agendamentos</h1>
        </div>
    </header>
    <main class="container">
        <section id="cadastro">
            <section class="formulario">
                <h2>Cadastrar Novo Cliente</h2>
                <?php if (!empty($error_message)) : ?>
                    <div class="alert alert-danger"><?= $error_message ?></div>
                <?php endif; ?>

                <?php if (!empty($success_message)) : ?>
                    <div class="alert alert-success"><?= $success_message ?></div>
                <?php endif; ?>
                <form action="cadastro.php" method="POST">
                    <div class="form-group">
                        <label for="nome">Nome Completo:</label>
                        <input type="text" id="nome" name="nome" placeholder="Seu Nome Completo" required>
                    </div>
                    <div class="form-group">
                        <label for="email">E-mail:</label>
                        <input type="email" id="email" name="email" placeholder="Seu E-mail" required>
                    </div>
                    <div class="form-group">
                        <label for="senha">Senha:</label>
                        <input type="password" id="senha" name="senha" placeholder="Sua Senha" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmar_senha">Confirmar Senha:</label>
                        <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Confirmar Senha" required>
                    </div>
                    <button type="submit">Cadastrar</button>
                </form>
            </section>
            <p>Já possui uma conta? <a href="login.php">Faça login</a></p>
        </section>
    </main>
</body>

</html>
<?php
include 'config.php';
session_start();
$error_message = "";

try {
    $conn = get_db_connection();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST["email"];
        $senha = $_POST["senha"];

        $sql = "SELECT id, nome, senha, tipo_usuario FROM clientes WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            if (password_verify($senha, $row["senha"])) {
                $_SESSION["user_id"] = $row["id"];
                $_SESSION["user_email"] = $email;
                $_SESSION["user_name"] = $row["nome"];
                $_SESSION["tipo_usuario"] = $row["tipo_usuario"]; // Armazena o tipo de usuário na sessão

                if ($row["tipo_usuario"] == "barbeiro") { // Verifica o tipo de usuário
                    header("Location: barbeiro.php"); // Redireciona para a página do barbeiro
                } else {
                    header("Location: agendarHorario.php"); // Redireciona para a página de agendamento
                }
                exit();
            } else {
                $error_message = "Email ou senha inválidos.";
            }
        } else {
            $error_message = "Email ou senha inválidos.";
        }
        $stmt->close();
    }
} catch (Exception $e) {
    $error_message = "Erro ao processar a solicitação.";
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
    <title>Barbearia - Login</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="reset.css">
</head>

<body>
    <header>
    <img src="logo.png" alt="Cortes Command">
        <h1>Barbearia - Sistema de Agendamentos</h1>
    </header>
    <main class="container">
        <section id="login">
            <section class="formulario">
                <h2>Login de Cliente</h2>
                <?php if (!empty($error_message)) : ?>
                    <div class="error-message"><?= $error_message ?></div>
                <?php endif; ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="form-group">
                        <label for="email">E-mail:</label>
                        <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required>
                    </div>
                    <div class="form-group">
                        <label for="senha">Senha:</label>
                        <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>
                    </div>
                    <button type="submit">Login</button>
                </form>
            </section>
            <p>Ainda não tem uma conta? <a href="cadastro.php">Cadastre-se</a></p>
        </section>
    </main>
</body>

</html>
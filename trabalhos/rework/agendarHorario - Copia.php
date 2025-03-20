<?php
include 'config.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$error_message = "";
$success_message = "";

function gerarOpcoesHorario($inicio, $fim, $intervalo, $data, $conn) {
    $inicio = new DateTime($inicio);
    $fim = new DateTime($fim);
    $opcoes = [];

    $horarios_agendados = [];
    $stmt_agendamentos = $conn->prepare("SELECT DATE_FORMAT(data_hora, '%H:%i') AS hora FROM agendamentos WHERE DATE(data_hora) = ?");
    $stmt_agendamentos->bind_param("s", $data);
    $stmt_agendamentos->execute();
    $result_agendamentos = $stmt_agendamentos->get_result();
    while ($row_agendamento = $result_agendamentos->fetch_assoc()) {
        $horarios_agendados[] = $row_agendamento['hora'];
    }
    $stmt_agendamentos->close();

    while ($inicio <= $fim) {
        $hora_atual = $inicio->format('H:i');
        if (!in_array($hora_atual, $horarios_agendados)) {
            $opcoes[] = '<option value="' . $hora_atual . '">' . $hora_atual . '</option>';
        }
        $inicio->modify('+' . $intervalo . ' minutes');
    }

    return implode('', $opcoes);
}

try {
    $conn = get_db_connection();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nome = $_POST["nome"];
        $email = $_POST["email"];
        $servico_id = $_POST["servico"];
        $data = $_POST["data"];
        $hora = $_POST["hora"];

        // Validação dos campos
        if (empty($nome) || empty($email) || empty($servico_id) || empty($data) || empty($hora)) {
            $error_message = "Todos os campos são obrigatórios.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Formato de email inválido.";
        } else {
            // Check if the service ID exists
            $stmt_check_service = $conn->prepare("SELECT 1 FROM servicos WHERE id =?");
            $stmt_check_service->bind_param("i", $servico_id);
            $stmt_check_service->execute();
            $stmt_check_service->store_result();

            if ($stmt_check_service->num_rows == 0) {
                $error_message = "Serviço inválido.";
            } else {
                $stmt_check_service->close();

                $check_booking_sql = "SELECT id FROM agendamentos WHERE data_hora =?";
                $stmt = $conn->prepare($check_booking_sql);
                $data_hora = $data . " " . $hora;
                $stmt->bind_param("s", $data_hora);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $error_message = "Este horário já está agendado. Por favor, escolha outro horário.";
                } else {
                    $stmt = $conn->prepare("INSERT INTO agendamentos (cliente_id, servico_id, data_hora) VALUES (?,?,?)");
                    $stmt->bind_param("iis", $_SESSION["user_id"], $servico_id, $data_hora);

                    if ($stmt->execute()) {
                        $success_message = "Agendamento realizado com sucesso!";
                    } else {
                        $error_message = "Erro ao agendar horário: " . $stmt->error;
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
    <title>Agendamento de Cortes</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="reset.css">
</head>

<body>
    <header>
        <div class="logo">
            <img src="logo.png" alt="Cortes Command">
            <h1>Bora dar um tapa no visu?</h1>
        </div>
    </header>
    <main>
        <section class="formulario">
            <h2>Agendar Horário</h2>

            <?php if (!empty($success_message)) : ?>
                <div class="success-message">
                    <?php echo $success_message; ?>
                </div>
            <?php elseif (!empty($error_message)) : ?>
                <div class="error-message">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form action="agendarhorario.php" method="POST">
                <input type="text" name="nome" placeholder="Nome" required>
                <input type="email" name="email" placeholder="Email" required>
                <select name="servico" required>
                    <option value="">Selecionar Serviço</option>
                    <?php
                    try {
                        $conn = get_db_connection();
                        $stmt = $conn->prepare("SELECT id, nome FROM servicos");
                        $stmt->execute();
                        $result = $stmt->get_result();
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . $row['nome'] . "</option>";
                        }
                        $stmt->close();
                        $conn->close();
                    } catch (Exception $e) {
                        echo "<option value=''>Erro ao carregar serviços</option>";
                    }
                    ?>
                </select>
                <div class="data-hora">
                    <input type="date" name="data" id="data" required min="<?php echo date('Y-m-d'); ?>" onchange="atualizarHorarios()">
                    <select name="hora" id="hora" required>
                        <option value="">Selecionar Horário</option>
                    </select>
                </div>
                <button type="submit">Agendar</button>
            </form>
        </section>
    </main>
    <script>
        function atualizarHorarios() {
            const dataSelecionada = document.getElementById('data').value;
            const selectHora = document.getElementById('hora');

            fetch('get_horarios.php?data=' + dataSelecionada)
                .then(response => response.text())
                .then(horarios => {
                    selectHora.innerHTML = horarios;
                });
        }

        window.addEventListener('DOMContentLoaded', atualizarHorarios);
    </script>
</body>

</html>
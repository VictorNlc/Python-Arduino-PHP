<?php
include 'config.php';
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["tipo_usuario"] != "barbeiro") {
    header("Location: login.php");
    exit();
}

try {
    $conn = get_db_connection();

    $agendamentos = [];
    $historico = [];

    // Agendamentos futuros
    $sql_agendamentos = "SELECT a.*, s.nome AS nome_servico, c.nome AS nome_cliente
                         FROM agendamentos a
                         INNER JOIN servicos s ON a.servico_id = s.id
                         INNER JOIN clientes c ON a.cliente_id = c.id
                         WHERE a.data_hora >= NOW()
                         ORDER BY a.data_hora ASC";

    $stmt_agendamentos = $conn->prepare($sql_agendamentos);
    $stmt_agendamentos->execute();
    $result_agendamentos = $stmt_agendamentos->get_result();

    if ($result_agendamentos) {
        while ($row_agendamento = $result_agendamentos->fetch_assoc()) {
            $agendamentos[] = $row_agendamento;
        }
    }
    $stmt_agendamentos->close();


    // Histórico de agendamentos
    $sql_historico = "SELECT a.*, s.nome AS nome_servico, c.nome AS nome_cliente
                     FROM agendamentos a
                     INNER JOIN servicos s ON a.servico_id = s.id
                     INNER JOIN clientes c ON a.cliente_id = c.id
                     WHERE a.data_hora < NOW()
                     ORDER BY a.data_hora DESC LIMIT 10";

    $stmt_historico = $conn->prepare($sql_historico);
    $stmt_historico->execute();
    $result_historico = $stmt_historico->get_result();

    if ($result_historico) {
        while ($row_historico = $result_historico->fetch_assoc()) {
            $historico[] = $row_historico;
        }
    }
    $stmt_historico->close();

    $conn->close();

} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    $error_message = "Ocorreu um erro ao carregar os dados.";
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link rel="stylesheet" href="barbeiro.css">
    <link rel="stylesheet" href="reset.css">
</head>

<body>
    <header>
    </header>

    <main class="container">
        <section id="home">
            <div class="header-content">
                <h2>Olá, Barbeiro!</h2>
                <form action="logout.php" method="post">
                    <button type="submit" class="logout-button">Sair</button>
                </form>
            </div>

            <?php if (isset($error_message)) : ?>
                <div class="error-message"><?= $error_message ?></div>
            <?php endif; ?>

            <?php if (isset($_GET['success'])) : ?>
                <div class="success-message">Agendamento excluído com sucesso!</div>
            <?php elseif (isset($_GET['error'])) : ?>
                <div class="error-message">Erro ao excluir agendamento.</div>
            <?php endif; ?>

            <h3>Agendamentos:</h3>
            <div id="agendamentos">
                <?php if (empty($agendamentos)) : ?>
                    <p>Não há agendamentos marcados.</p>
                <?php else : ?>
                    <ul>
                        <?php foreach ($agendamentos as $agendamento) : ?>
                            <li>
                                Cliente: <?php echo $agendamento['nome_cliente']; ?><br>
                                Data/Hora: <?php echo date('d/m/Y H:i', strtotime($agendamento['data_hora'])); ?><br>  Serviço: <?php echo $agendamento['nome_servico']; ?>
                                <a href="excluir_agendamento.php?id=<?php echo $agendamento['id']; ?>">Excluir</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <h3>Histórico de Agendamentos:</h3>
            <div id="historico">
                <?php if (empty($historico)) : ?>
                    <p>Não há histórico de agendamentos.</p>
                <?php else : ?>
                    <ul>
                        <?php foreach ($historico as $item) : ?>
                            <li>
                                Cliente: <?php echo $item['nome_cliente']; ?><br>
                                Data/Hora: <?php echo date('d/m/Y H:i', strtotime($item['data_hora'])); ?><br>  Serviço: <?php echo $item['nome_servico']; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <script src="script.js"></script>
</body>

</html>
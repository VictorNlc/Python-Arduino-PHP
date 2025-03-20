<?php
include 'config.php';

try {
    $conn = get_db_connection();
    $data = $_GET['data'];

    // Formato da data para o MySQL (YYYY-MM-DD)
    $data_mysql = date('Y-m-d', strtotime($data));  // Converte para o formato MySQL

    echo gerarOpcoesHorario("14:30", "20:00", 30, $data_mysql, $conn); // Usa $data_mysql
} catch (Exception $e) {
    echo "<option value=''>Erro ao carregar horários</option>";
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}

function gerarOpcoesHorario($inicio, $fim, $intervalo, $data, $conn) {
    $inicio = new DateTime($inicio);
    $fim = new DateTime($fim);
    $opcoes = [];

    $horarios_agendados = [];

    // Consulta otimizada para buscar apenas as horas no formato HH:MM
    $stmt_agendamentos = $conn->prepare("SELECT TIME_FORMAT(data_hora, '%H:%i') AS hora FROM agendamentos WHERE DATE(data_hora) = ?");
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
?>
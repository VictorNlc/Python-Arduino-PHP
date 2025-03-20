<?php
// Configuração do Banco de Dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "barbearia";

// Conectar ao banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Obter agendamentos do dia
$data_hoje = date("Y-m-d");
$sql = "SELECT c.nome, s.nome AS servico, a.data_hora 
        FROM agendamentos a
        JOIN clientes c ON a.cliente_id = c.id
        JOIN servicos s ON a.servico_id = s.id
        WHERE DATE(a.data_hora) = '$data_hoje'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $mensagem = "📅 *Agendamentos de hoje:*\n\n";
    
    while ($row = $result->fetch_assoc()) {
        $mensagem .= "- " . $row["nome"] . " | Serviço: " . $row["servico"] . " | " . date("H:i", strtotime($row["data_hora"])) . "\n";
    }

    // Enviar a mensagem no WhatsApp
    enviarWhatsApp($mensagem);
} else {
    echo "Nenhum agendamento para hoje.";
}

// Fechar conexão
$conn->close();

// Função para enviar mensagem pelo WhatsApp (UltraMsg)
function enviarWhatsApp($mensagem) {
    $instance_id = "instance108374";  // Substitua pelo ID da sua instância UltraMsg
    $token = "4cbhuekslxtqasz9";  // Substitua pelo seu token
    $numero_destino = "5551998328628";  // Seu número com DDD

    $data = [
        "token" => $token,
        "to" => $numero_destino,
        "body" => $mensagem
    ];

    $url = "https://api.ultramsg.com/instance108374/messages/chat"; // URL correta

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo "Erro no cURL: " . curl_error($ch);
    } else {
        echo "Resposta da API: " . $response;
    }

    curl_close($ch);
}
?>

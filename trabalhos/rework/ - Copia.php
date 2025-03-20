<?php
function testarWhatsApp() {
    $instance_id = "instance108374";  // Substitua pelo ID correto
    $token = "4cbhuekslxtqasz9";  // Substitua pelo seu token
    $numero_destino = "5551998328628";  // Número com DDD

    $data = [
        "token" => $token,
        "to" => $numero_destino,
        "body" => "🚀 Teste de envio do WhatsApp pelo UltraMsg!"
    ];

    $url = "https://api.ultramsg.com/instance108374/messages/chat"; // URL correta

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); // Convertendo para formato de requisição HTTP
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/x-www-form-urlencoded"
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo "Erro no cURL: " . curl_error($ch);
    } else {
        echo "Resposta da API: " . $response;
    }

    curl_close($ch);
}

testarWhatsApp();
?>

<?php
require_once __DIR__ . '/../vendor/autoload.php';

function compress($source, $destination)
{
    try {
        \Tinify\setKey("s4nx9Zcwjsvpz7vk9XzJv1TdvzqfKYZC");
        $sourceData = \Tinify\fromFile($source);
        $resized = $sourceData->resize([
            "method" => "scale",
            "width" => 800
        ]);

        $resized->toFile($destination);
        return true;
    } catch (\Tinify\Exception $e) {
        error_log("TinyPNG Error: " . $e->getMessage());
        return false;
    }
}

// Substitua sua função enviarNotificacaoOneSignal por esta versão melhorada:

function enviarNotificacaoOneSignal($titulo, $mensagem, $url = null)
{
    $appId = "574229ff-3df7-474b-8e1c-4d6d3bca5ade"; // Seu App ID está certo
    $restApiKey = "os_v2_app_k5bct7z565duxdq4jvwtxss232mo3oxmhazeb7mjnxyicwu6nz7dyav6t4ld2ntdse6tyho2srptruqenlh6kyasskdxk272bs34hpa"; // <--- AQUI ESTAVA O :)

    $fields = [
        'app_id' => $appId,
        'included_segments' => ['All'],
        'headings' => ["en" => $titulo],
        'contents' => ["en" => $mensagem]
    ];

    // Se passarmos um link, adiciona ele na notificação
    if ($url) {
        $fields['url'] = $url;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json; charset=utf-8',
        'Authorization: Basic ' . $restApiKey
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

function verificiarEventosExpirados($json)
{
    date_default_timezone_set('America/Sao_Paulo');
    if (!file_exists($json)) return;


    $conteudo = file_get_contents($json);
    $agenda = json_decode($conteudo, true);

    $houveAlteracao = false;
    $agora = new DateTime();

    $agendaAtualizada = [];

    foreach ($agenda as $evento) {
        $dataRef = !empty($evento['data_fim']) ? $evento['data_fim'] : $evento['data_inicio'];
        $horaRef = '23:59'; // Define 23h59 como o fim do dia
        if (!empty($evento['data_fim']) && !empty($evento['hora_fim'])) {
            $horaRef = $evento['hora_fim'];
        } elseif (empty(['data_fim']) && !empty($evento['hora_inicio'])) {
            $horaRef = $evento['hora_inicio'];
        }

        $dataEvento = DateTime::createFromFormat('d/m/Y H:i', $dataRef . '' . $horaRef);
        if (!$dataEvento) {
            $agendaAtualizada[] = $evento;
            continue; // Pula o resto do código e prossegue para o próximo índice
        }

        if ($dataEvento > $agora) {
            $agendaAtualizada[] = $evento;
        } else {
            // Não adiciona o evento na Array e passamos que foi alterado
            $houveAlteracao = true;
        }
    }

    if ($houveAlteracao) {
        file_put_contents($json, json_encode($agendaAtualizada, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}

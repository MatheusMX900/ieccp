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

function enviarNotificacaoOneSignal($titulo, $mensagem)
{
    $appId = "574229ff-3df7-474b-8e1c-4d6d3bca5ade";
    $restApiKey = " :) ";

    $fields = [
        'app_id' => $appId,
        'included_segments' => ['All'], // Manda para todos
        'headings' => ["en" => $titulo],
        'contents' => ["en" => $mensagem]
    ];

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
    return $response;
}

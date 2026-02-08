<?php
    // --- CONFIGURAÇÕES ---
    $apiKey = 'AIzaSyDTazfRh_P-I4P6H0nTRwqO6soZZwSf7jM';
    $channelId = 'UCTYtvExf1Wh-V7t9UAjYJtw';
    
    $cache = __DIR__ . 'data/live_status.json'; 

    date_default_timezone_set('America/Sao_Paulo');

    // --- LÓGICA DE HORÁRIO ---
    $hoje = date('w'); // 0 = Domingo
    $hora = (int)date('H');

    // Domingo entre 09h-12h OU 18h-20h
    $ehHorarioCulto = ($hoje == 0) && (
        ($hora >= 9 && $hora <= 12) ||
        ($hora >= 18 && $hora <= 20)
    );

    // Corrige a variável $force
    $force = isset($_GET['force']) && $_GET['force'] == 'true';

    // SE NÃO FOR HORÁRIO E NÃO FORÇAR, PARA TUDO AQUI
    if (!$ehHorarioCulto && !$force) {
        echo "Fora do horário de monitoramento. Nenhuma verificação feita.";
        exit;
    }

    // --- CONSULTA (USANDO CURL PARA LOCAWEB) ---
    $url = "https://www.googleapis.com/youtube/v3/search?part=snippet&channelId={$channelId}&type=video&eventType=live&key={$apiKey}";

    $status = [
        'is_live' => false,
        'video_id' => null,
        'titulo' => null,
        'ultima_verificacao' => date('Y-m-d H:i:s')
    ];

    // Função cURL (Mais robusta para Locaweb)
    function buscarUrl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $resultado = curl_exec($ch);
        curl_close($ch);
        return $resultado;
    }

    $response = buscarUrl($url);

    if ($response) {
        $data = json_decode($response, true);
        
        // Verifica erro da API
        if (isset($data['error'])) {
            die('Erro API: ' . $data['error']['message']);
        }

        if (isset($data['items']) && count($data['items']) > 0) {
            $video = $data['items'][0];
            $status['is_live'] = true;
            $status['video_id'] = $video['id']['videoId'];
            $status['titulo'] = $video['snippet']['title'];
        }
    }

    // Salva o arquivo
    file_put_contents($cache, json_encode($status));

    echo "Verificação concluida. Status: " . ($status['is_live'] ? "AO VIVO" : "OFFLINE");
?>
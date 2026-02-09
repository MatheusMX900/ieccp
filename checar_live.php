<?php
$apiKey = 'AIzaSyCXCBJnlpzr11e6YIcQSypF_b295OD5B9s';
$channelId = 'UCTYtvExf1Wh-V7t9UAjYJtw';

// CORREÇÃO 1: Caminho simplificado (salva na mesma pasta do script para evitar erro de pasta inexistente)
$arquivoCache = __DIR__ . '/live_status.json';

date_default_timezone_set('America/Sao_Paulo');

// --- VERIFICAÇÃO DE HORÁRIO ---
$hoje = date('w'); // 0 = Domingo

// CORREÇÃO 2: Definindo a variável $agora corretamente como string (ex: "09:30")
$agora = date('H:i'); 

// Intervalos de monitoramento
$manha = ($agora >= '08:40' && $agora <= '12:30');
$noite = ($agora >= '18:15' && $agora <= '21:00');

$ehDomingo = ($hoje == 0);
// Agora a lógica funciona porque $agora existe
$ehHorarioCulto = $ehDomingo && ($manha || $noite);

$force = isset($_GET['force']) && $_GET['force'] == 'true';

if (!$ehHorarioCulto && !$force) {
    echo "Fora do horário de monitoramento ($agora). SABOOOOOR";
    exit;
}

// --- LEITURA DO STATUS ATUAL ---
$statusAtual = ['is_live' => false, 'video_id' => null];
if (file_exists($arquivoCache)) {
    $conteudo = file_get_contents($arquivoCache);
    $statusAtual = json_decode($conteudo, true) ?? $statusAtual;
}

function buscarUrl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $resultado = curl_exec($ch);
    curl_close($ch);
    return $resultado;
}

// Prepara o novo status como OFFLINE por padrão
$novoStatus = [
    'is_live' => false,
    'video_id' => null,
    'titulo' => null,
    'ultima_verificacao' => date('d/m/Y H:i:s')
];

// --- LÓGICA INTELIGENTE ---

// CENÁRIO A: JÁ TÍNHAMOS UMA LIVE DETECTADA? (Modo Econômico)
if ($statusAtual['is_live'] && !empty($statusAtual['video_id'])) {
    
    $videoId = $statusAtual['video_id'];
    echo "Modo MONITORAMENTO (Verificando ID: $videoId)... <br>";
    
    // Checa apenas aquele vídeo específico (Gasta 1 unidade)
    $url = "https://www.googleapis.com/youtube/v3/videos?part=snippet&id={$videoId}&key={$apiKey}";
    
    $response = buscarUrl($url);
    $data = json_decode($response, true);

    if (isset($data['items']) && count($data['items']) > 0) {
        $video = $data['items'][0];
        // 'live', 'none' (acabou), ou 'upcoming'
        $broadcastStatus = $video['snippet']['liveBroadcastContent']; 
        
        if ($broadcastStatus === 'live') {
            // A live continua! Mantemos os dados.
            $novoStatus['is_live'] = true;
            $novoStatus['video_id'] = $videoId;
            $novoStatus['titulo'] = $video['snippet']['title'];
            echo "A live continua ativa! (Custo: 1 unidade)";
        } else {
            // Se cair aqui, $novoStatus continua false (definido lá em cima), 
            // então o site vai voltar a mostrar os horários.
            echo "A live acabou. O site voltará ao normal.";
        }
    } else {
        echo "Vídeo não encontrado ou removido. Resetando status.";
    }

} 
// CENÁRIO B: NÃO TINHA LIVE, VAMOS PROCURAR (Modo Busca)
else {
    
    echo "Modo BUSCA (Procurando novas lives)... <br>";
    
    // Busca no canal (Gasta 100 unidades)
    $url = "https://www.googleapis.com/youtube/v3/search?part=snippet&channelId={$channelId}&type=video&eventType=live&key={$apiKey}";
    
    $response = buscarUrl($url);
    $data = json_decode($response, true);
    
    if (isset($data['items']) && count($data['items']) > 0) {
        $video = $data['items'][0];
        $novoStatus['is_live'] = true;
        $novoStatus['video_id'] = $video['id']['videoId'];
        $novoStatus['titulo'] = $video['snippet']['title'];
        echo "Nova live encontrada! (Custo: 100 unidades)";
    } else {
        echo "Nenhuma live encontrada.";
    }
}

// Salva o JSON atualizado
file_put_contents($arquivoCache, json_encode($novoStatus));
?>
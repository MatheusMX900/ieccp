<?php
// ARQUIVO: includes/functions.php

// 1. LÊ OS ARQUIVOS JSON
function lerJson($arquivo)
{
    // Caminho relativo à pasta includes
    $caminho = __DIR__ . '/../data/' . $arquivo;
    if (file_exists($caminho)) {
        $conteudo = file_get_contents($caminho);
        return json_decode($conteudo, true) ?? [];
    }
    return [];
}

// 2. FORMATA IMAGENS (Caminho Absoluto)
function formatarImagem($img)
{
    if (empty($img)) return '/img/logo.png';
    // Remove ../ e domínios para garantir caminho limpo
    $limpa = str_replace(['../', 'https://ieccp.com.br/', 'http://ieccp.com.br/'], '', $img);
    // Adiciona a barra no início para ser absoluto
    return '/' . ltrim($limpa, '/');
}

// 3. GERA LINKS AMIGÁVEIS (SLUG)
function gerarSlug($string)
{
    $map = [
        'á' => 'a',
        'à' => 'a',
        'ã' => 'a',
        'â' => 'a',
        'ä' => 'a',
        'é' => 'e',
        'è' => 'e',
        'ê' => 'e',
        'ë' => 'e',
        'í' => 'i',
        'ì' => 'i',
        'î' => 'i',
        'ï' => 'i',
        'ó' => 'o',
        'ò' => 'o',
        'õ' => 'o',
        'ô' => 'o',
        'ö' => 'o',
        'ú' => 'u',
        'ù' => 'u',
        'û' => 'u',
        'ü' => 'u',
        'ç' => 'c',
        'ñ' => 'n',
        'Á' => 'a',
        'À' => 'a',
        'Ã' => 'a',
        'Â' => 'a',
        'Ä' => 'a',
        'É' => 'e',
        'È' => 'e',
        'Ê' => 'e',
        'Ë' => 'e',
        'Í' => 'i',
        'Ì' => 'i',
        'Î' => 'i',
        'Ï' => 'i',
        'Ó' => 'o',
        'Ò' => 'o',
        'Õ' => 'o',
        'Ô' => 'o',
        'Ö' => 'o',
        'Ú' => 'u',
        'Ù' => 'u',
        'Û' => 'u',
        'Ü' => 'u',
        'Ç' => 'c',
        'Ñ' => 'n'
    ];
    $slug = strtr($string, $map);
    $slug = mb_strtolower($slug, 'UTF-8');
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
    $slug = preg_replace('/[\s-]+/', '-', $slug);
    return trim($slug, '-');
}

// 4. RESUMO DE TEXTO
function resumirTexto($texto, $limite = 100)
{
    $textoLimpo = strip_tags($texto);
    if (mb_strlen($textoLimpo) <= $limite) return $textoLimpo;
    return mb_substr($textoLimpo, 0, $limite) . '...';
}

// 5. FORMATA DATA AGENDA
function formatarDataAgenda($evento)
{
    $inicio = $evento['data_inicio'] ?? $evento['data'] ?? '';
    $fim = $evento['data_fim'] ?? '';
    $hora = $evento['hora_inicio'] ?? '';

    if (!$inicio) return "";
    $dataCurta = substr($inicio, 0, 5);

    if ($fim && $fim !== $inicio) {
        $fimCurto = substr($fim, 0, 5);
        return "{$dataCurta} a {$fimCurto}";
    }
    if ($hora) {
        return "{$dataCurta} • {$hora}";
    }
    return $dataCurta;
}

<?php
require_once __DIR__ . '/../models/arquivo.php';
require_once __DIR__ . '/../services/helper.php';

$uri = Helper::getCurrentUri();
preg_match('/\/?storage\/(.*?)\/([^\/]+)$/', $uri, $matches);
$path = $matches[1];
$file = $matches[2];

$filePath = Helper::storagePath($path . '/' . $file);

$nome = $file;
if (key_exists('name', $_GET)) $nome = $_GET['name'];

// Verifica se o arquivo solicitado é válido
if (file_exists($filePath)) {
    // Define os cabeçalhos HTTP para informar o navegador sobre o tipo de conteúdo
    header('Content-Type: ' . mime_content_type($filePath));
    header('Content-Disposition: inline; filename="' . $nome . '"');

    // Lê o arquivo e envia seu conteúdo para o navegador
    readfile($filePath);
} else {
    // Se o arquivo não existir, exibe uma mensagem de erro
    header("HTTP/1.0 404 Not Found");
}
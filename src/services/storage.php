<?php
require_once '../src/bootstrap.php';
require_once __DIR__ . '/../src/models/arquivo.php';
require_once __DIR__ . '/../src/services/helper.php';

if (!key_exists('caminho', $_GET) || !key_exists('arquivo', $_GET)) {
    header("HTTP/1.0 404 Not Found");
    return;
}

$filePath = Helper::storagePath(Arquivo::pathResolve($_GET['caminho'], $_GET['arquivo']));

// em caso o usuário esteja tentando acessar algum arquivo fora da pasta storage
if (preg_match('/\.{2,}/', $filePath)) {
    header("HTTP/1.0 404 Not Found");
    return;
}

$nome = $_GET['arquivo'];
if (key_exists('nome', $_GET)) $nome = $_GET['nome'];

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
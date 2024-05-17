<?php
// Caminho para o arquivo .env
$envFilePath = __DIR__ . '/../../.env';

require_once __DIR__ . '/../../config.php';

// Verifica se o arquivo .env existe
if (file_exists($envFilePath)) {
    // Lê o conteúdo do arquivo .env
    $envFileContent = file_get_contents($envFilePath);

    // Divide o conteúdo em linhas
    $envLines = explode("\n", $envFileContent);

    // Itera sobre as linhas
    foreach ($envLines as $line) {
        // Remove espaços em branco e verifica se a linha não está vazia
        $line = trim($line);
        if (!empty($line)) {
            // Divide a linha em chave e valor usando o caractere '='
            list($key, $value) = explode('=', $line, 2);
            // Define a variável de ambiente correspondente
            putenv("$key=$value");
        }
    }
}
<?php
require_once 'models/config/entity.php';
require_once 'services/auth.php';
require_once 'services/dotenv.php';
require_once 'services/helper.php';
require_once 'services/log.php';
date_default_timezone_set('America/Sao_Paulo');

header("Content-Type: charset=UTF-8");

session_start();

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (getenv('env') === 'dev') echo $errstr . '<br>';
    Entity::rollBackTransaction();
    Log::new(Log::TYPE_ERROR)->setError($errno, $errstr, $errfile, $errline);
});
set_exception_handler(function(Throwable $exception) {
    if (getenv('env') === 'dev') echo $exception->getMessage() . '<br>';
    Entity::rollBackTransaction();
    Log::new(Log::TYPE_EXCEPTION)->setException($exception);
});

$uri = strtok($_SERVER["REQUEST_URI"], '?');
if ($uri !== Helper::uriLogin()) {
    Auth::verificarAutenticacao();
}

function apiRequest() {
    require_once 'services/api_handler.php';

    $apiHandler = new Api_handler();
    $apiHandler->handler();
}
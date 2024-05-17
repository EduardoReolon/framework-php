<?php
require_once '../src/bootstrap.php';
require_once __DIR__ . '/../src/views/config/view_main.php';
require_once __DIR__ . '/../src/views/demandas_view.php';
require_once __DIR__ . '/../src/views/demanda_view.php';
require_once __DIR__ . '/../src/views/familia_view.php';
require_once __DIR__ . '/../src/views/home_view.php';
require_once __DIR__ . '/../src/views/imoveis_view.php';
require_once __DIR__ . '/../src/views/login_view.php';

$uri = strtok($_SERVER["REQUEST_URI"], '?');
Log::new(Log::TYPE_CONTROL)->setMessage($_SERVER["REQUEST_METHOD"] . '-' . $uri);

function isCurrent(string $option): bool {
    global $uri;
    if (preg_match($option, $uri)) return true;
    return false;
}

$file = 'not_found.html';
if ($uri === Helper::uriLogin()) {
    new Login_view();
    return;
} else if (isCurrent('/^\/(sappr(\/)?(public)?)?$/')) {
    $file = 'home.php';
    new Home_view();
    return;
} else if (isCurrent('/^\/(sappr\/(public\/)?)?demandas$/')) {
    $file = 'demandas.php';
    new Demandas_view();
    return;
} else if (isCurrent('/^\/(sappr\/(public\/)?)?demanda\/[0-9]+$/')) {
    $file = 'demanda.php';
    new Demanda_view();
    return;
} else if (isCurrent('/^\/(sappr\/(public\/)?)?demanda\/[0-9]+\/familia\/[0-9]+$/')) {
    $file = 'familia.php';
    new Familia_view();
    return;
} else if (isCurrent('/^\/(sappr\/(public\/)?)?demanda\/[0-9]+\/imoveis$/')) {
    $file = 'imoveis.php';
    new Imoveis_view();
    return;
}

new View_main();
return;

?>
<?php
require_once '../src/bootstrap.php';
require_once __DIR__ . '/../src/models/arquivo.php';

if (!Auth::hasRole('admin')) {
    header("HTTP/1.0 404 Not Found");
    return;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $arquivo = new Arquivo();
    $arquivo->carregaArquivo('file', 'documentos');

    echo $arquivo->url();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Carregamento de Arquivo</title>
</head>
<body>
    <h2>Carregar Arquivo</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <input type="file" name="file" id="arquivo">
        <input type="submit" value="Carregar Arquivo" name="submit">
    </form>
</body>
</html>
<?php
require_once '../src/bootstrap.php';
require_once __DIR__ . '/../src/models/demanda.php';
require_once __DIR__ . '/../src/services/helper.php';

if (!Auth::hasRole('admin')) {
    header("HTTP/1.0 404 Not Found");
    return;
}

$demandaArr = Demanda::fetchSimpler([['id', '=', 3]]);
if (empty($demandaArr)) throw new Exception("Demanda não encontrada", 1);
$demanda = $demandaArr[0];
$demanda->hierarquizar();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Hierarquização</title>
</head>
<body>
    <table>
    <tr>
        <th>Id</th>
        <th>Nome</th>
        <th>Nascimento</th>
        <th>Pontuação</th>
        <th>Classificação</th>
    </tr>

    <?php
    // Exibindo informações sobre os produtos
    /** @var Familia $familia */
    foreach ($demanda->familias_hierarquizadas as $familia) {
        $nome = $familia->pretendente->get('nome');
        $nascimento = $familia->pretendente->data_nascimento->format('d/m/Y');
        $pontuacao_total = $familia->getPontuacaoTotal();
        $classificacao = $familia->getClassificacao();
        echo "<tr>";
        echo "<td>$familia->id</td>";
        echo "<td>$nome</td>";
        echo "<td>$nascimento</td>";
        echo "<td>$pontuacao_total</td>";
        echo "<td>$classificacao</td>";
        echo "</tr>";
    }
    ?>
</table>
</body>
</html>
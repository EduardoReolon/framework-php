<?php
require_once '../src/bootstrap.php';
require_once __DIR__ . '/../src/models/demanda.php';
require_once __DIR__ . '/../src/services/helper.php';
require_once __DIR__ . '/../src/services/calc_cota.php';

if (!Auth::hasRole('admin')) {
    header("HTTP/1.0 404 Not Found");
    return;
}

$demandaArr = Demanda::fetchSimpler([['id', '=', 3]]);
if (empty($demandaArr)) throw new Exception("Demanda não encontrada", 1);
$demanda = $demandaArr[0];
$demanda->loadFamilias(true, true);
$demanda->loadCotasDemanda();
Calc_cota::calcDisponibilidadesCotas($demanda, $demanda->cotas_demanda, array_slice($demanda->familias, -380));

/** @param Familia */
function listaCotas($familia): string {
    /** @param Cota_familia */
    $arrCotas_familia = array_values(array_filter($familia->cotas_familia, function ($cota_familia) {
        return $cota_familia->getSeEnquadra();
    }));

    /** @param Cota_familia */
    $arrStrs = array_map(function ($cota_familia) {
        return $cota_familia->cota_demanda->cota->nome;
    }, $arrCotas_familia);

    return implode(', ', $arrStrs);
};

?>
<!DOCTYPE html>
<html>
<head>
    <title>Cotas</title>
</head>
<body>
    <h1>Cotas</h1>
    <table>
    <tr>
        <th>Nome</th>
        <th>Quantidade</th>
        <th>Utilização fixa</th>
        <th>Disponibilidade máxima</th>
        <th>Distribuição OK</th>
    </tr>

    <?php
    // Exibindo informações sobre os produtos
    foreach ($demanda->cotas_demanda as $cota_demanda) {
        $nome = $cota_demanda->cota->nome;
        $qtde = $cota_demanda->quantidade;
        $utilizacao_fixa = $cota_demanda->utilizacao_fixa;
        $disponibilidade_maxima = $cota_demanda->disponibilidade_maxima;
        $distribuicao_ok = $cota_demanda->distribuicao_bem_sucedida;
        echo "<tr>";
        echo "<td>$nome</td>";
        echo "<td>$qtde</td>";
        echo "<td>$utilizacao_fixa</td>";
        echo "<td>$disponibilidade_maxima</td>";
        echo "<td>$distribuicao_ok</td>";
        echo "</tr>";
    }
    ?>
    </table>

    <h1>Familias em 1 cota</h1>
    <table>
    <tr>
        <th>id</th>
        <th>Nome</th>
        <th>Cotas</th>
    </tr>

    <?php
    // Exibindo informações sobre os produtos
    foreach ($demanda->familias_com_1_cota as $familia) {
        $id = $familia->id;
        $nome = $familia->pretendente->get('nome');
        $cotas = listaCotas($familia);
        echo "<tr>";
        echo "<td>$id</td>";
        echo "<td>$nome</td>";
        echo "<td>$cotas</td>";
        echo "</tr>";
    }
    ?>
    </table>

    <h1>Familias em 2+ cotas</h1>
    <table>
    <tr>
        <th>id</th>
        <th>Nome</th>
        <th>Cotas</th>
    </tr>

    <?php
    // Exibindo informações sobre os produtos
    foreach ($demanda->familias_2_mais_cotas as $familia) {
        $id = $familia->id;
        $nome = $familia->pretendente->get('nome');
        $cotas = listaCotas($familia);
        echo "<tr>";
        echo "<td>$id</td>";
        echo "<td>$nome</td>";
        echo "<td>$cotas</td>";
        echo "</tr>";
    }
    ?>
    </table>
</table>
</body>
</html>
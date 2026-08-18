
<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/ecommerce/Class/Conexion.php';

$cid = new Conexion();

$db = isset($_GET['db']) ? $_GET['db'] : 'central';

$cid_central = $cid->conectarSql($db);

$warehouse = $_POST['warehouse'] ?? null;
$cuenta = $_POST['cuenta'] ?? null;
$warehouses = isset($_POST['warehouses']) ? json_decode($_POST['warehouses'], true) : null;
$rubro = (isset($_POST['rubros']) ? json_decode($_POST['rubros']) : NULL);
$stockSeguridad = (isset($_POST['rubros'])) ? json_decode($_POST['cantidad']) : NULL;



if (isset($_POST['rubros'])) {
    $listaWarehouses = $warehouses ?? [['warehouse' => $warehouse, 'cuenta' => $cuenta]];

    foreach ($listaWarehouses as $wh) {
        $c = 0;
        while ($c < count($rubro)) {
            try {
                $sql2 = "EXEC GC_SP_ECOMMERCE_ASIGNAR_STOCK_SEGURIDAD_CLASIFICADOR ?, ?, ?, ?, 0";
                $stmt = sqlsrv_prepare($cid_central, $sql2, array($wh['cuenta'], $wh['warehouse'], $rubro[$c], $stockSeguridad[$c]));
                sqlsrv_execute($stmt);
                echo 'ok '.$wh['cuenta'].' '.$wh['warehouse'].' '.$rubro[$c].' '.$stockSeguridad[$c];
            } catch (Exception $e) {
                // $e->getMessage() contains the error message
                echo 'Error: ' . $e->getMessage();
            }
            $c++;
        }
    }
} else {
    try {
        $sql = "EXEC GC_SP_ECOMMERCE_ACTIVACION_STOCK_SEGURIDAD_POR_CLASIFICADOR ?, ?, 1";
        $stmt = sqlsrv_prepare($cid_central, $sql, array($cuenta, $warehouse));
        sqlsrv_execute($stmt);
    } catch (Exception $e) {
        // $e->getMessage() contains the error message
        echo 'Error: ' . $e->getMessage();
    }
}

$sql3 = "EXEC RO_SP_PIVOT_STOCK_SEGURIDAD_VTEX";

$stmt = sqlsrv_query($cid_central, $sql3);

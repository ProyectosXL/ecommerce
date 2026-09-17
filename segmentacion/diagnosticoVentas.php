<?php
/**
 * TEMPORAL - BORRAR DESPUES DE USAR
 *
 * Inspecciona el esquema real de la coleccion Mongo "Ventas" para saber si los
 * documentos traen un campo de sucursal/tienda (y de que tipo).
 *
 * Correr desde el navegador EN EL SERVIDOR DE PRODUCCION (esta es la unica maquina
 * con la extension mongodb de PHP y acceso al Mongo real).
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Class/conexion.php';

header('Content-Type: text/plain; charset=utf-8');
set_time_limit(300);

$TM = ['typeMap' => ['root' => 'array', 'document' => 'array', 'array' => 'array']];

$cid   = new Conexion();
$mongo = $cid->conectarMongoDb();

if ($mongo === null) {
    echo "No se pudo conectar a MongoDB. Revisar HOST_MONGO / DATABASE_MONGO en el .env\n";
    exit;
}

echo "=== Colecciones de la base ===\n";
foreach ($mongo->listCollections() as $c) {
    echo " - " . $c->getName() . "\n";
}

$col = $mongo->selectCollection('Ventas');
echo "\nTotal aprox. de documentos: " . $col->estimatedDocumentCount() . "\n";

echo "\n=== 1) Un documento completo (findOne) ===\n";
print_r($col->findOne([], $TM));

echo "\n=== 2) Claves de nivel raiz sobre una muestra de 2000 docs ===\n";
$keys = [];
$n    = 0;
foreach ($col->aggregate([['$sample' => ['size' => 2000]]], $TM) as $d) {
    $n++;
    foreach (array_keys($d) as $k) {
        $keys[$k] = ($keys[$k] ?? 0) + 1;
    }
}
arsort($keys);
echo "Docs muestreados: $n\n";
foreach ($keys as $k => $c) {
    printf("  %-28s %6d  (%5.1f%%)\n", $k, $c, $n ? $c * 100 / $n : 0);
}

echo "\n=== 3) Claves dentro de ARTICULOS ===\n";
$akeys = [];
$pipe  = [['$sample' => ['size' => 500]], ['$unwind' => '$ARTICULOS'], ['$limit' => 3000]];
foreach ($col->aggregate($pipe, $TM) as $d) {
    if (!isset($d['ARTICULOS']) || !is_array($d['ARTICULOS'])) continue;
    foreach (array_keys($d['ARTICULOS']) as $k) {
        $akeys[$k] = ($akeys[$k] ?? 0) + 1;
    }
}
arsort($akeys);
print_r($akeys);

echo "\n=== 4) Candidatos a sucursal / ticket ===\n";
echo "(anotar: nombre exacto del campo, TIPO del valor, y si trae ceros a la izquierda)\n\n";

$candidatos = [
    'NRO_SUCURSAL', 'NRO_SUCURS', 'SUCURSAL', 'DESC_SUCURSAL', 'LOCAL', 'COD_LOCAL',
    'TIENDA', 'COD_CLIENT', 'PUNTO_VENTA', 'PTO_VENTA', 'N_COMP', 'T_COMP',
    'NRO_COMP', 'ORIGEN', 'CANAL', 'PROVINCIA', 'LOCALIDAD',
];

foreach ($candidatos as $campo) {
    $existe = $col->countDocuments([$campo => ['$exists' => true]], ['limit' => 1]);

    if (!$existe) {
        echo "  $campo: NO EXISTE\n";
        continue;
    }

    try {
        $vals = $col->distinct($campo, [], ['maxTimeMS' => 20000]);
        echo "  $campo: EXISTE | " . count($vals) . " valores distintos | tipo del 1ro: "
            . gettype($vals[0] ?? null) . "\n"
            . "     muestra: " . json_encode(array_slice($vals, 0, 25), JSON_UNESCAPED_UNICODE) . "\n";
    } catch (\Throwable $e) {
        // Si el distinct sobre toda la coleccion hace timeout, acotar a los ultimos 30 dias.
        try {
            $desde = new MongoDB\BSON\UTCDateTime(strtotime('-30 days') * 1000);
            $vals  = $col->distinct($campo, ['FECHA' => ['$gte' => $desde]], ['maxTimeMS' => 20000]);
            echo "  $campo: EXISTE (ultimos 30 dias) | " . count($vals) . " valores distintos | tipo del 1ro: "
                . gettype($vals[0] ?? null) . "\n"
                . "     muestra: " . json_encode(array_slice($vals, 0, 25), JSON_UNESCAPED_UNICODE) . "\n";
        } catch (\Throwable $e2) {
            echo "  $campo: EXISTE (distinct timeout: " . $e2->getMessage() . ")\n";
        }
    }
}

echo "\n=== FIN - BORRAR ESTE ARCHIVO ===\n";

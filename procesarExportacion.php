<?php
/**
 * procesarExportacion.php
 * Corre como proceso CLI en background.
 * Uso: php procesarExportacion.php <ruta_al_params.json>
 */

// Evitar límite de tiempo en CLI
set_time_limit(0);
ini_set('memory_limit', '512M');

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit('Solo CLI');
}

// ── Leer parámetros ───────────────────────────────────────────────────────────
$paramsFile = $argv[1] ?? null;
if (!$paramsFile || !file_exists($paramsFile)) {
    exit('Archivo de parámetros no encontrado');
}

$params = json_decode(file_get_contents($paramsFile), true);
if (!$params) {
    exit('Parámetros inválidos');
}

$jobId       = $params['job_id'];
$tmpDir      = $params['tmp_dir'];
$csvFile     = $tmpDir . DIRECTORY_SEPARATOR . $jobId . '.csv';
$doneFile    = $tmpDir . DIRECTORY_SEPARATOR . $jobId . '.done';
$errorFile   = $tmpDir . DIRECTORY_SEPARATOR . $jobId . '.error';

// ── Bootstrap de la aplicación ────────────────────────────────────────────────
// Ajustar DOCUMENT_ROOT para que las clases funcionen en contexto CLI.
// Las clases usan $_SERVER['DOCUMENT_ROOT'] . '/ecommerce/...' por lo que
// DOCUMENT_ROOT debe apuntar al padre de la app (igual que en el servidor web).
$appRoot = __DIR__;
$_SERVER['DOCUMENT_ROOT'] = dirname($appRoot); // ej. c:\xampp\htdocs

require_once $appRoot . '/Class/Conexion.php';
require_once $appRoot . '/Class/Pedido.php';

// ── Preparar filtros ──────────────────────────────────────────────────────────
$desde       = $params['desde']        ?? date('Y-m-d');
$hasta       = $params['hasta']        ?? date('Y-m-d');
$tienda      = ($params['tienda']      !== '') ? $params['tienda']      . '%' : '%';
$warehouse   = ($params['warehouse']   !== '') ? $params['warehouse']   . '%' : '%';
$estado      = ($params['estado']      !== '') ? $params['estado']      : null;
$orden       = ($params['orden']       !== '') ? $params['orden']       . '%' : '%';
$metodoEnvio = $params['metodo_envio'] ?? '';
$busqueda    = $params['factura']      ?? '';

$pedidos = new Pedido();

// ── Abrir CSV ─────────────────────────────────────────────────────────────────
try {
    $fp = fopen($csvFile, 'w');
    if (!$fp) {
        file_put_contents($errorFile, 'No se pudo crear el archivo CSV');
        exit(1);
    }

    // BOM UTF-8
    fwrite($fp, "\xEF\xBB\xBF");

    // Cabecera
    fputcsv($fp, [
        'TIENDA', 'NRO ORDEN', 'FECHA PEDIDO', 'HORA', 'NRO PEDIDO',
        'NOMBRE', 'COD ARTICULO', 'DESC ARTICULO', 'CANTIDAD', 'IMPORTE',
        'NRO FACTURA', 'DEPOSITO', 'METODO ENVIO', 'TIENDA ENTREGA', 'ESTADO',
    ], ';');

    $porPagina = 500;
    $pagina    = 1;
    $total     = 0;

    while (true) {
        $arrayPedidos = $pedidos->traerPedidos(
            $desde, $hasta, $tienda, $warehouse,
            $estado, $orden, $pagina, $porPagina, $metodoEnvio
        );

        $rawCount = count($arrayPedidos);

        // Filtrar sin unidades
        $arrayPedidos = array_filter($arrayPedidos, function ($value) {
            return isset($value[0]->CANTIDAD_A_FACTURAR) && $value[0]->CANTIDAD_A_FACTURAR > 0;
        });

        // Filtro texto libre
        if ($busqueda !== '') {
            $bl = mb_strtolower($busqueda);
            $arrayPedidos = array_filter($arrayPedidos, function ($value) use ($bl) {
                $campos = [
                    $value[0]->NRO_ORDEN_ECOMMERCE ?? '',
                    $value[0]->NRO_PEDIDO          ?? '',
                    $value[0]->RAZON_SOCIAL        ?? '',
                    $value[0]->COD_ARTICULO        ?? '',
                    $value[0]->DESCRIPCION         ?? '',
                    $value[0]->NRO_COMP            ?? '',
                    $value[0]->WAREHOUSE           ?? '',
                    $value[0]->METODO_ENVIO        ?? '',
                    $value[0]->DESC_SUCURSAL       ?? '',
                ];
                foreach ($campos as $c) {
                    if (mb_strpos(mb_strtolower((string)$c), $bl) !== false) return true;
                }
                return false;
            });
        }

        foreach ($arrayPedidos as $value) {
            $v = $value[0];

            // Estado legible
            if (($v->CANCELADO ?? 0) == 1)        $estadoFila = 'CANCELADO';
            elseif (($v->ENTREGADO ?? 0) == 1)    $estadoFila = 'ENTREGADO';
            elseif (($v->DESPACHADO ?? 0) == 1)   $estadoFila = 'DESPACHADO';
            elseif (($v->CONTROLADO ?? 0) == 1)   $estadoFila = 'CONTROLADO';
            elseif (($v->FACTURADO ?? 0) == 1)    $estadoFila = 'FACTURADO';
            elseif (($v->PREPARADO ?? 0) == 1)    $estadoFila = 'PREPARADO';
            else                                   $estadoFila = 'SIN_CONTROLAR';

            $fechaPedido = ($v->FECHA_PEDIDO instanceof DateTime)
                ? $v->FECHA_PEDIDO->format('Y-m-d') : (string)($v->FECHA_PEDIDO ?? '');

            $nroOrden = (string)($v->NRO_ORDEN_ECOMMERCE ?? '');
            $nroOrdenExcel = $nroOrden !== '' ? '="' . $nroOrden . '"' : '';

            fputcsv($fp, [
                $v->ORIGEN                ?? '',
                $nroOrdenExcel,
                $fechaPedido,
                $v->HORA                  ?? '',
                $v->NRO_PEDIDO            ?? '',
                $v->RAZON_SOCIAL          ?? '',
                $v->COD_ARTICULO          ?? '',
                $v->DESCRIPCION           ?? '',
                $v->CANTIDAD_A_FACTURAR   ?? '',
                $v->IMPORTE_PAGO          ?? '',
                $v->NRO_COMP              ?? '',
                $v->WAREHOUSE             ?? '',
                $v->METODO_ENVIO          ?? '',
                $v->DESC_SUCURSAL         ?? '',
                $estadoFila,
            ], ';');

            $total++;
        }

        if ($rawCount < $porPagina) break;
        $pagina++;
        usleep(10000); // 10ms pausa entre páginas
    }

    fclose($fp);

    // Escribir archivo .done con total de registros
    file_put_contents($doneFile, json_encode(['total' => $total, 'ok' => true]));

    // Limpiar archivo de parámetros
    @unlink($paramsFile);

} catch (Throwable $e) {
    if (isset($fp) && is_resource($fp)) fclose($fp);
    file_put_contents($errorFile, $e->getMessage());
    exit(1);
}
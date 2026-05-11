<?php
/**
 * exportarPedidos.php
 * Genera y descarga el CSV de pedidos de forma síncrona.
 * Llamado desde un iframe oculto via POST desde index.php.
 */

set_time_limit(0);
ini_set('max_execution_time', 0);
ini_set('memory_limit', '512M');
ignore_user_abort(true); // continuar aunque Apache cierre la conexión

require_once $_SERVER['DOCUMENT_ROOT'] . '/ecommerce/Class/Conexion.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/ecommerce/Class/Pedido.php';

// ── Parámetros (acepta GET o POST) ──────────────────────────────────────────────────
function rp(string $k): string {
    return trim((string)($_REQUEST[$k] ?? ''));
}
$token        = preg_replace('/[^a-z0-9]/', '', strtolower(rp('export_token')));
$desde        = rp('desde')        ?: date('Y-m-d');
$hasta        = rp('hasta')        ?: date('Y-m-d');
$tiendaRaw    = rp('tienda');
$warehouseRaw = rp('warehouse');
$estadoRaw    = rp('estado');
$ordenRaw     = rp('orden');
$metodoEnvio  = rp('metodo_envio');
$busqueda     = rp('factura');

$tienda    = $tiendaRaw    !== '' ? $tiendaRaw    . '%' : '%';
$warehouse = $warehouseRaw !== '' ? $warehouseRaw . '%' : '%';
$estado    = $estadoRaw    !== '' ? $estadoRaw    : null;
$orden     = $ordenRaw     !== '' ? $ordenRaw     . '%' : '%';

$pedidos = new Pedido();

// ── Headers de descarga ───────────────────────────────────────────────────────
$filename = 'pedidos_' . date('Y-m-d_H-i-s') . '.csv';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

// Deshabilitar output buffering para que los headers salgan de inmediato
while (ob_get_level()) ob_end_clean();

// ── Escribir CSV a stdout ─────────────────────────────────────────────────────
$fp = fopen('php://output', 'w');

// BOM UTF-8 para Excel
fwrite($fp, "\xEF\xBB\xBF");

// Cabecera CSV
fputcsv($fp, [
    'TIENDA', 'NRO ORDEN', 'FECHA PEDIDO', 'HORA', 'NRO PEDIDO',
    'NOMBRE', 'COD ARTICULO', 'DESC ARTICULO', 'CANTIDAD', 'IMPORTE',
    'NRO FACTURA', 'DEPOSITO', 'METODO ENVIO', 'TIENDA ENTREGA', 'ESTADO',
], ';');

// Traer todos los registros en una sola consulta (sin paginación),
// eliminando los ~40 round-trips extra que causaban timeout en rangos largos.
$arrayPedidos = $pedidos->traerPedidos(
    $desde, $hasta, $tienda, $warehouse,
    $estado, $orden, 1, 999999, $metodoEnvio
);

// Filtrar sin unidades
$arrayPedidos = array_filter($arrayPedidos, function ($v) {
    return isset($v[0]->CANTIDAD_A_FACTURAR) && $v[0]->CANTIDAD_A_FACTURAR > 0;
});

// Filtro texto libre
if ($busqueda !== '') {
    $bl = mb_strtolower($busqueda);
    $arrayPedidos = array_filter($arrayPedidos, function ($v) use ($bl) {
        $campos = [
            $v[0]->NRO_ORDEN_ECOMMERCE ?? '',
            $v[0]->NRO_PEDIDO          ?? '',
            $v[0]->RAZON_SOCIAL        ?? '',
            $v[0]->COD_ARTICULO        ?? '',
            $v[0]->DESCRIPCION         ?? '',
            $v[0]->NRO_COMP            ?? '',
            $v[0]->WAREHOUSE           ?? '',
            $v[0]->METODO_ENVIO        ?? '',
            $v[0]->DESC_SUCURSAL       ?? '',
        ];
        foreach ($campos as $c) {
            if (mb_strpos(mb_strtolower((string)$c), $bl) !== false) return true;
        }
        return false;
    });
}

foreach ($arrayPedidos as $value) {
    $v = $value[0];

    if (($v->CANCELADO ?? 0) == 1)        $estadoFila = 'CANCELADO';
    elseif (($v->ENTREGADO ?? 0) == 1)    $estadoFila = 'ENTREGADO';
    elseif (($v->DESPACHADO ?? 0) == 1)   $estadoFila = 'DESPACHADO';
    elseif (($v->CONTROLADO ?? 0) == 1)   $estadoFila = 'CONTROLADO';
    elseif (($v->FACTURADO ?? 0) == 1)    $estadoFila = 'FACTURADO';
    elseif (($v->PREPARADO ?? 0) == 1)    $estadoFila = 'PREPARADO';
    else                                   $estadoFila = 'SIN_CONTROLAR';

    $fechaPedido = ($v->FECHA_PEDIDO instanceof DateTime)
        ? $v->FECHA_PEDIDO->format('Y-m-d') : (string)($v->FECHA_PEDIDO ?? '');

    $nroOrden      = (string)($v->NRO_ORDEN_ECOMMERCE ?? '');
    $nroOrdenExcel = $nroOrden !== '' ? '="' . $nroOrden . '"' : '';

    fputcsv($fp, [
        $v->ORIGEN              ?? '',
        $nroOrdenExcel,
        $fechaPedido,
        $v->HORA                ?? '',
        $v->NRO_PEDIDO          ?? '',
        $v->RAZON_SOCIAL        ?? '',
        $v->COD_ARTICULO        ?? '',
        $v->DESCRIPCION         ?? '',
        $v->CANTIDAD_A_FACTURAR ?? '',
        $v->IMPORTE_PAGO        ?? '',
        $v->NRO_COMP            ?? '',
        $v->WAREHOUSE           ?? '',
        $v->METODO_ENVIO        ?? '',
        $v->DESC_SUCURSAL       ?? '',
        $estadoFila,
    ], ';');
}

fclose($fp);
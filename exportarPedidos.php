<?php
/**
 * exportarPedidos.php
 * Descarga sincrónica en streaming del CSV de pedidos.
 * Usa paginación por DÍA (igual que getPedidos.php) para evitar
 * el problema de OFFSET acumulativo en SQL Server y el timeout del proxy.
 * Cada día se escribe y se flushea → el proxy nunca timeout.
 */

set_time_limit(0);
ini_set('max_execution_time', 0);
ini_set('memory_limit', '512M');

require_once $_SERVER['DOCUMENT_ROOT'] . '/ecommerce/Class/Conexion.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/ecommerce/Class/Pedido.php';

// ── Parámetros ────────────────────────────────────────────────────────────────
function rp(string $k): string {
    return trim((string)($_REQUEST[$k] ?? ''));
}

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

// ── Headers de descarga ───────────────────────────────────────────────────────
$filename = 'pedidos_' . date('Y-m-d_H-i-s') . '.csv';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('X-Accel-Buffering: no'); // desactiva buffering en Nginx/IIS ARR

// Vaciar todos los buffers de salida
while (ob_get_level()) ob_end_clean();

$pedidos = new Pedido();
$fp      = fopen('php://output', 'w');

// BOM UTF-8 para Excel
fwrite($fp, "\xEF\xBB\xBF");

// Cabecera CSV
fputcsv($fp, [
    'TIENDA', 'NRO ORDEN', 'FECHA PEDIDO', 'HORA', 'NRO PEDIDO',
    'NOMBRE', 'COD ARTICULO', 'DESC ARTICULO', 'CANTIDAD', 'IMPORTE',
    'NRO FACTURA', 'DEPOSITO', 'METODO ENVIO', 'TIENDA ENTREGA', 'ESTADO',
], ';');

flush();

// ── Iterar día a día (evita OFFSET acumulativo del SP) ────────────────────────
$fechaInicio = new DateTime($desde);
$fechaFin    = new DateTime($hasta);
$diaActual   = clone $fechaInicio;

while ($diaActual <= $fechaFin) {
    $desdeDia = $diaActual->format('Y-m-d');
    $hastaDia  = $diaActual->format('Y-m-d');

    $arrayPedidos = $pedidos->traerPedidos(
        $desdeDia, $hastaDia, $tienda, $warehouse,
        $estado, $orden, 1, 9999, $metodoEnvio
    );

    // Filtrar sin unidades
    $arrayPedidos = array_filter($arrayPedidos, function ($v) {
        return isset($v[0]->CANTIDAD_A_FACTURAR) && $v[0]->CANTIDAD_A_FACTURAR > 0;
    });

    // Filtro texto libre
    if ($busqueda !== '') {
        $bl = mb_strtolower($busqueda);
        $arrayPedidos = array_filter($arrayPedidos, function ($v) use ($bl) {
            foreach ([
                $v[0]->NRO_ORDEN_ECOMMERCE ?? '',
                $v[0]->NRO_PEDIDO          ?? '',
                $v[0]->RAZON_SOCIAL        ?? '',
                $v[0]->COD_ARTICULO        ?? '',
                $v[0]->DESCRIPCION         ?? '',
                $v[0]->NRO_COMP            ?? '',
                $v[0]->WAREHOUSE           ?? '',
                $v[0]->METODO_ENVIO        ?? '',
                $v[0]->DESC_SUCURSAL       ?? '',
            ] as $c) {
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

    // Flush después de cada día → el proxy recibe datos y no corta la conexión
    flush();

    $diaActual->modify('+1 day');
}

fclose($fp);

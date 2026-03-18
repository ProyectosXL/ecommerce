<?php
/**
 * exportarPedidos.php
 * Genera y descarga el CSV de pedidos de forma síncrona.
 * Llamado desde un iframe oculto via POST desde index.php.
 */

set_time_limit(0);
ini_set('memory_limit', '512M');

require_once $_SERVER['DOCUMENT_ROOT'] . '/ecommerce/Class/Conexion.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/ecommerce/Class/Pedido.php';

// ── Parámetros ────────────────────────────────────────────────────────────────
$token        = preg_replace('/[^a-z0-9]/', '', strtolower($_POST['export_token'] ?? ''));
$desde        = $_POST['desde']        ?? date('Y-m-d');
$hasta        = $_POST['hasta']        ?? date('Y-m-d');
$tiendaRaw    = $_POST['tienda']       ?? '';
$warehouseRaw = $_POST['warehouse']    ?? '';
$estadoRaw    = $_POST['estado']       ?? '';
$ordenRaw     = $_POST['orden']        ?? '';
$metodoEnvio  = $_POST['metodo_envio'] ?? '';
$busqueda     = $_POST['factura']      ?? '';

$tienda    = $tiendaRaw    !== '' ? $tiendaRaw    . '%' : '%';
$warehouse = $warehouseRaw !== '' ? $warehouseRaw . '%' : '%';
$estado    = $estadoRaw    !== '' ? $estadoRaw    : null;
$orden     = $ordenRaw     !== '' ? $ordenRaw     . '%' : '%';

$pedidos = new Pedido();

// ── Cookie: el JS padre la detecta cuando llega la respuesta ─────────────────
if ($token !== '') {
    setcookie('export_ready_' . $token, '1', time() + 120, '/');
}

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

// Cabecera
fputcsv($fp, [
    'TIENDA', 'NRO ORDEN', 'FECHA PEDIDO', 'HORA', 'NRO PEDIDO',
    'NOMBRE', 'COD ARTICULO', 'DESC ARTICULO', 'CANTIDAD', 'IMPORTE',
    'NRO FACTURA', 'DEPOSITO', 'METODO ENVIO', 'TIENDA ENTREGA', 'ESTADO',
], ';');

$porPagina = 500;
$pagina    = 1;

while (true) {
    $arrayPedidos = $pedidos->traerPedidos(
        $desde, $hasta, $tienda, $warehouse,
        $estado, $orden, $pagina, $porPagina, $metodoEnvio
    );

    $rawCount = count($arrayPedidos);

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

    if ($rawCount < $porPagina) break;
    $pagina++;
}

fclose($fp);

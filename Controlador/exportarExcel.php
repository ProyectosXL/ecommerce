<?php
/**
 * Exporta a Excel (CSV) todos los pedidos que coinciden con los filtros activos,
 * sin límite de paginación.
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/ecommerce/Class/Conexion.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/ecommerce/Class/Pedido.php';

$pedidos = new Pedido();

$hoy      = date('Y-m-d');
$tienda   = (!isset($_GET['tienda'])   || trim($_GET['tienda'])   === '') ? '%' : $_GET['tienda']   . '%';
$warehouse= (!isset($_GET['warehouse'])|| trim($_GET['warehouse'])=== '') ? '%' : $_GET['warehouse']. '%';
$desde    = (!isset($_GET['desde']))   ? $hoy : $_GET['desde'];
$hasta    = (!isset($_GET['hasta']))   ? $hoy : $_GET['hasta'];
$estado   = (isset($_GET['estado'])    && trim($_GET['estado'])   !== '') ? trim($_GET['estado'])    : null;
$orden    = (!isset($_GET['orden'])    || trim($_GET['orden'])    === '') ? '%' : $_GET['orden']    . '%';
$metodoEnvio = (isset($_GET['metodo_envio']) && trim($_GET['metodo_envio']) !== '') ? trim($_GET['metodo_envio']) : '';
$busqueda    = (isset($_GET['factura'])      && trim($_GET['factura'])      !== '') ? trim($_GET['factura'])      : '';

// Traer todos los registros (sin paginación) — un solo llamado al SP con límite alto
$arrayPedidos = $pedidos->traerPedidos($desde, $hasta, $tienda, $warehouse, $estado, $orden, 1, 9999, $metodoEnvio);

// Filtrar pedidos sin unidades
$arrayPedidos = array_filter($arrayPedidos, function ($value) {
    return isset($value[0]->CANTIDAD_A_FACTURAR) && $value[0]->CANTIDAD_A_FACTURAR > 0;
});

// Filtro de texto libre (igual que en index.php)
if ($busqueda !== '') {
    $busquedaLower = mb_strtolower($busqueda);
    $arrayPedidos  = array_filter($arrayPedidos, function ($value) use ($busquedaLower) {
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
        foreach ($campos as $campo) {
            if (mb_strpos(mb_strtolower((string) $campo), $busquedaLower) !== false) {
                return true;
            }
        }
        return false;
    });
}

// ---------- Cabeceras HTTP para descarga ----------
$filename = 'pedidos_' . $desde . '_' . $hasta . '.csv';

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');

// BOM UTF-8 para que Excel lo abra correctamente con tildes / ñ
fwrite($output, "\xEF\xBB\xBF");

// Encabezados de columna
fputcsv($output, [
    'TIENDA',
    'NRO ORDEN',
    'FECHA PEDIDO',
    'HORA',
    'NRO PEDIDO',
    'NOMBRE',
    'COD ARTICULO',
    'DESC ARTICULO',
    'CANTIDAD',
    'IMPORTE',
    'NRO FACTURA',
    'DEPOSITO',
    'METODO ENVIO',
    'TIENDA ENTREGA',
    'ESTADO',
], ';');

foreach ($arrayPedidos as $value) {
    $v = $value[0];

    // Construir un campo de estado legible
    $estado_fila = '';
    if (isset($v->CANCELADO) && $v->CANCELADO == 1) {
        $estado_fila = 'CANCELADO';
    } elseif (isset($v->ENTREGADO) && $v->ENTREGADO == 1) {
        $estado_fila = 'ENTREGADO';
    } elseif (isset($v->DESPACHADO) && $v->DESPACHADO == 1) {
        $estado_fila = 'DESPACHADO';
    } elseif (isset($v->CONTROLADO) && $v->CONTROLADO == 1) {
        $estado_fila = 'CONTROLADO';
    } elseif (isset($v->FACTURADO) && $v->FACTURADO == 1) {
        $estado_fila = 'FACTURADO';
    } elseif (isset($v->PREPARADO) && $v->PREPARADO == 1) {
        $estado_fila = 'PREPARADO';
    } else {
        $estado_fila = 'SIN_CONTROLAR';
    }

    fputcsv($output, [
        $v->ORIGEN                ?? '',
        $v->NRO_ORDEN_ECOMMERCE   ?? '',
        isset($v->FECHA_PEDIDO)   ? $v->FECHA_PEDIDO->format('Y-m-d') : '',
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
        $estado_fila,
    ], ';');
}

fclose($output);
exit;

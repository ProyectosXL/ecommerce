<?php
/**
 * getPedidos.php
 * Endpoint AJAX para carga progresiva de pedidos.
 * Devuelve JSON con las filas HTML y un flag hayMas.
 */

// Capturar cualquier error de PHP y devolverlo como JSON legible
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode([
        'html' => '', 'count' => 0, 'hayMas' => false,
        'error' => "PHP Error [$errno]: $errstr en $errfile:$errline",
    ], JSON_UNESCAPED_UNICODE);
    exit;
});
set_exception_handler(function($e) {
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode([
        'html' => '', 'count' => 0, 'hayMas' => false,
        'error' => 'Exception: ' . $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
    exit;
});

header('Content-Type: application/json; charset=UTF-8');

// Sin límite de tiempo ni memoria — misma estrategia que exportarPedidos.php
set_time_limit(0);
ini_set('max_execution_time', 0);
ini_set('memory_limit', '512M');

require_once __DIR__ . '/Class/Conexion.php';
require_once __DIR__ . '/Class/Pedido.php';

$pedidos = new Pedido();

// ── Parámetros ────────────────────────────────────────────────────────────────
$hoy         = date('Y-m-d');
$desde       = isset($_GET['desde'])        ? $_GET['desde']        : $hoy;
$hasta       = isset($_GET['hasta'])        ? $_GET['hasta']        : $hoy;
$tienda      = (isset($_GET['tienda'])      && trim($_GET['tienda'])      !== '') ? $_GET['tienda']      . '%' : '%';
$warehouse   = (isset($_GET['warehouse'])   && trim($_GET['warehouse'])   !== '') ? $_GET['warehouse']   . '%' : '%';
$estado      = (isset($_GET['estado'])      && trim($_GET['estado'])      !== '') ? $_GET['estado']      : null;
$orden       = (isset($_GET['orden'])       && trim($_GET['orden'])       !== '') ? $_GET['orden']       . '%' : '%';
$metodoEnvio = (isset($_GET['metodo_envio']) && trim($_GET['metodo_envio']) !== '') ? trim($_GET['metodo_envio']) : '';
$busqueda    = (isset($_GET['factura'])     && trim($_GET['factura'])     !== '') ? trim($_GET['factura'])     : '';

// ── Consulta única (sin paginación SQL para evitar lentitud por OFFSET profundo) ──
$arrayPedidos = $pedidos->traerPedidos(
    $desde, $hasta, $tienda, $warehouse,
    $estado, $orden, 1, 999999, $metodoEnvio
);

// Filtrar sin unidades
$arrayPedidos = array_values(array_filter($arrayPedidos, function ($value) {
    return isset($value[0]->CANTIDAD_A_FACTURAR) && $value[0]->CANTIDAD_A_FACTURAR > 0;
}));

// Filtro texto libre
if ($busqueda !== '') {
    $bl = mb_strtolower($busqueda);
    $arrayPedidos = array_values(array_filter($arrayPedidos, function ($value) use ($bl) {
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
    }));
}

$hayMas = false; // consulta única: no hay más páginas

// ── Generar HTML de filas ─────────────────────────────────────────────────────
$filas = [];
$pedido_viejo = '';

foreach ($arrayPedidos as $idx => $value) {
    $v = $value[0];

    // Estilos de fila
    $style = '';
    if (($v->NRO_COMP ?? '') == '' && ($v->CANCELADO ?? 0) == 0) {
        $style .= 'font-weight:bold; color:#FE2E2E;';
    }
    if ($pedido_viejo !== '' && $v->NRO_PEDIDO !== $pedido_viejo) {
        $style .= 'border-top: 1px solid black;';
    }
    $pedido_viejo = $v->NRO_PEDIDO;

    // Columnas calculadas
    $fechaPedido = ($v->FECHA_PEDIDO instanceof DateTime) ? $v->FECHA_PEDIDO->format('Y-m-d') : (string)($v->FECHA_PEDIDO ?? '');
    $importe     = '$ ' . number_format((float)($v->IMPORTE_PAGO ?? 0), 0, '', '.');
    $nroOrden    = htmlspecialchars((string)($v->NRO_ORDEN_ECOMMERCE ?? ''), ENT_QUOTES);
    $nroOrdenLink = ($v->ORIGEN ?? '') === 'VTEX'
        ? '<a href="https://xlshop.myvtex.com/admin/orders/' . $nroOrden . '" target="_blank">' . $nroOrden . '</a>'
        : $nroOrden;

    // Ícono PREPARADO
    $icoPreparado = ($v->PREPARADO ?? 0) == 1
        ? '<i title="Preparado ' . (($v->FECHA_PREPARADO instanceof DateTime) ? $v->FECHA_PREPARADO->format('Y-m-d H:i') : '') . '" data-toggle="tooltip" data-placement="left" class="bi bi-cart-check-fill" style="color:#20c997;font-size:20px;"></i>'
        : '<i class="fas fa-square" style="color:white;font-size:20px;"></i>';

    // Ícono FACTURADO / CANCELADO
    if (($v->CANCELADO ?? 0) == 1 && ($v->FACTURADO ?? 0) == 1 && !isset($v->NCR)) {
        $icoFact = '<i class="bi bi-clipboard-x-fill cancelado" data-toggle="tooltip" data-placement="left" title="Pedido cancelado sin NC" style="color:#6610f2;font-size:20px;padding:0;"></i>';
    } elseif (isset($v->NCR)) {
        $icoFact = '<i class="bi bi-clipboard-check-fill" data-toggle="tooltip" data-placement="left" title="Pedido cancelado NCR ' . htmlspecialchars((string)($v->NCR ?? ''), ENT_QUOTES) . '" style="color:#17a2b8;font-size:20px;padding:0;"></i>';
    } elseif (($v->FACTURADO ?? 0) == 1) {
        $fechaFact = ($v->FECHA_FACTURADO instanceof DateTime) ? $v->FECHA_FACTURADO->format('Y-m-d H:i') : '';
        $icoFact = '<i class="bi bi-file-earmark-text-fill" data-toggle="tooltip" data-placement="left" title="Facturado ' . $fechaFact . '" style="color:#6c757d;font-size:20px;"></i>';
    } else {
        $icoFact = '<i class="fas fa-square pendiente" style="color:white;font-size:20px;"></i>';
    }

    // Ícono CONTROLADO
    if (($v->CONTROLADO ?? 0) == 1) {
        $fechaCtrl = ($v->FECHA_CONTROLADO instanceof DateTime) ? $v->FECHA_CONTROLADO->format('Y-m-d') : 'sin fecha';
        $icoCtrl = '<i class="bi bi-clipboard2-check-fill" data-toggle="tooltip" data-placement="left" title="Controlado ' . $fechaCtrl . '" style="color:green;font-size:20px;"></i>';
    } else {
        $icoCtrl = '<i class="fas fa-square" style="color:white;font-size:20px;"></i>';
    }

    // Ícono DESPACHADO
    if (($v->DESPACHADO ?? 0) == 1) {
        $fechaDesp = ($v->FECHA_DESPACHO instanceof DateTime) ? $v->FECHA_DESPACHO->format('Y-m-d') : '';
        $icoDesp = '<i class="fas fa-truck" data-toggle="tooltip" data-placement="left" title="Despachado ' . $fechaDesp . '" style="color:#17a2b8;font-size:18px;padding-top:0.4rem;"></i>';
    } else {
        $icoDesp = '<i class="fas fa-square" style="color:white;font-size:20px;"></i>';
    }

    // Ícono RECIBIDO TIENDA
    if (($v->RECIBIDO_TIENDA ?? 0) == 1) {
        $fechaRec = ($v->FECHA_RECIBIDO_TIENDA instanceof DateTime) ? $v->FECHA_RECIBIDO_TIENDA->format('Y-m-d') : '';
        $icoRec = '<i class="fas fa-store" data-toggle="tooltip" data-placement="left" title="Recibido Tienda ' . $fechaRec . '" style="color:#17a2b8;font-size:18px;padding-top:0.4rem;"></i>';
    } else {
        $icoRec = '<i class="fas fa-square" style="color:white;font-size:20px;"></i>';
    }

    // Ícono ENTREGADO
    if (($v->ENTREGADO ?? 0) == 1) {
        $fechaEnt = ($v->FECHA_ENTREGADO instanceof DateTime) ? $v->FECHA_ENTREGADO->format('Y-m-d') : '';
        $icoEnt = '<i class="bi bi-box-seam-fill" data-toggle="tooltip" data-placement="left" title="Entregado ' . $fechaEnt . '" style="color:#007bff;font-size:18px;padding-top:0.4rem;"></i>';
    } else {
        $icoEnt = '<i class="fas fa-square" style="color:white;font-size:20px;"></i>';
    }

    // Ícono INCOMPLETO / CANCELADO
    if (isset($v->FALTANTE) && $v->FALTANTE == 1) {
        $icoInc = '<i title="Pedido incompleto" data-toggle="tooltip" data-placement="left" class="bi bi-cart-dash-fill incompleto" style="color:orange;font-size:20px;"></i>';
    } elseif (($v->CANCELADO ?? 0) == 1) {
        $icoInc = '<i class="bi bi-cart-x-fill" data-toggle="tooltip" data-placement="left" title="Cancelado" style="color:red;font-size:20px;padding:0;"></i>';
    } else {
        $icoInc = '<i class="fas fa-square" style="color:white;font-size:20px;"></i>';
    }

    $lugar = htmlspecialchars((string)($v->LUGAR_ENTREGA ?? ''), ENT_QUOTES);

    $fila  = '<tr id="tr" style="' . $style . '">';
    $fila .= '<td>' . htmlspecialchars((string)($v->ORIGEN ?? ''), ENT_QUOTES) . '</td>';
    $fila .= '<td>' . $nroOrdenLink . '</td>';
    $fila .= '<td>' . $fechaPedido . '</td>';
    $fila .= '<td>' . htmlspecialchars((string)($v->HORA ?? ''), ENT_QUOTES) . '</td>';
    $fila .= '<td>' . htmlspecialchars((string)($v->NRO_PEDIDO ?? ''), ENT_QUOTES) . '</td>';
    $fila .= '<td data-toggle="tooltip" data-placement="right" title="' . $lugar . '"><small>' . htmlspecialchars((string)($v->RAZON_SOCIAL ?? ''), ENT_QUOTES) . '</small></td>';
    $fila .= '<td>' . htmlspecialchars((string)($v->COD_ARTICULO ?? ''), ENT_QUOTES) . '</td>';
    $fila .= '<td><small>' . htmlspecialchars((string)($v->DESCRIPCION ?? ''), ENT_QUOTES) . '</small></td>';
    $fila .= '<td style="text-align:left;">' . htmlspecialchars((string)($v->CANTIDAD_A_FACTURAR ?? ''), ENT_QUOTES) . '</td>';
    $fila .= '<td>' . $importe . '</td>';
    $fila .= '<td style="text-align:center;"><small>' . htmlspecialchars((string)($v->NRO_COMP ?? ''), ENT_QUOTES) . '</small></td>';
    $fila .= '<td style="text-align:center;"><small>' . htmlspecialchars((string)($v->WAREHOUSE ?? ''), ENT_QUOTES) . '</small></td>';
    $fila .= '<td style="text-align:center;"><small>' . htmlspecialchars((string)($v->METODO_ENVIO ?? ''), ENT_QUOTES) . '</small></td>';
    $fila .= '<td style="text-align:center;"><small>' . htmlspecialchars((string)($v->DESC_SUCURSAL ?? ''), ENT_QUOTES) . '</small></td>';
    $fila .= '<td class="noExl">' . $icoPreparado . '</td>';
    $fila .= '<td id="cancelado" class="noExl">' . $icoFact . '</td>';
    $fila .= '<td class="noExl">' . $icoCtrl . '</td>';
    $fila .= '<td class="noExl">' . $icoDesp . '</td>';
    $fila .= '<td class="noExl">' . $icoRec . '</td>';
    $fila .= '<td class="noExl">' . $icoEnt . '</td>';
    $fila .= '<td id="incompleto" class="noExl">' . $icoInc . '</td>';
    $fila .= '</tr>';

    $filas[] = $fila;
}

echo json_encode([
    'html'   => implode('', $filas),
    'count'  => count($filas),
    'hayMas' => false,
], JSON_UNESCAPED_UNICODE);
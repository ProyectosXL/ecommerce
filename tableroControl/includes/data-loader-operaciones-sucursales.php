<?php
// includes/data-loader-operaciones-sucursales.php
// Devuelve HTML de la pestaña Operaciones Sucursales (o de una card individual con ?card=X)

header('Content-Type: application/json');
require_once __DIR__ . '/../../Class/Control.php';

$cardFilter = isset($_GET['card']) ? $_GET['card'] : null;

$mostrarNuevoPedidosIncompletos = (new DateTime())->diff(new DateTime('2025-11-18'))->days <= 7;

try {
    $control = new Control();

    if ($cardFilter) {
        $html = '';
        switch ($cardFilter) {
            case 'ordenesCierreVtex':
                $ordenesPendientesCierre = $control->traerOrdenesPendientesCierre();
                ob_start();
                include __DIR__ . '/../tabs/cards/card-ordenes-cierre-vtex.php';
                $html = ob_get_clean();
                break;
            case 'pedidosDespachados':
                $pedidosDespachados = $control->traerPedidosDespachados();
                ob_start();
                include __DIR__ . '/../tabs/cards/card-pedidos-despachados-suc.php';
                $html = ob_get_clean();
                break;
            case 'retiroStockCentral':
                $pedidosRecibidosNoEntregados = $control->traerPedidosRecibidosNoEntregados();
                ob_start();
                include __DIR__ . '/../tabs/cards/card-retiro-stock-central.php';
                $html = ob_get_clean();
                break;
            case 'retiroStockSucursal':
                $pedidosRetiroTienda = $control->traerPedidosRetiroTienda();
                ob_start();
                include __DIR__ . '/../tabs/cards/card-retiro-stock-sucursal.php';
                $html = ob_get_clean();
                break;
            case 'controlSucursales':
                $pedidosPendientesControlSucursales = $control->traerResumenPedidosPendientesControlSucursales();
                ob_start();
                include __DIR__ . '/../tabs/cards/card-control-sucursales.php';
                $html = ob_get_clean();
                break;
            case 'incompletosSucursales':
                $pedidosIncompletosSucursales = $control->traerPedidosIncompletosSucursales();
                ob_start();
                include __DIR__ . '/../tabs/cards/card-incompletos-sucursales.php';
                $html = ob_get_clean();
                break;
        }
        echo json_encode([
            'success'   => true,
            'html'      => $html,
            'timestamp' => date('d/m/Y H:i:s')
        ]);
    } else {
        $ordenesPendientesCierre            = $control->traerOrdenesPendientesCierre();
        $pedidosDespachados                 = $control->traerPedidosDespachados();
        $pedidosRecibidosNoEntregados       = $control->traerPedidosRecibidosNoEntregados();
        $pedidosRetiroTienda                = $control->traerPedidosRetiroTienda();
        $pedidosPendientesControlSucursales = $control->traerResumenPedidosPendientesControlSucursales();
        $pedidosIncompletosSucursales       = $control->traerPedidosIncompletosSucursales();

        $totalPendientes = 0;
        if ($ordenesPendientesCierre && !empty($ordenesPendientesCierre->CANT_ORDENES))
            $totalPendientes += $ordenesPendientesCierre->CANT_ORDENES;
        if ($pedidosDespachados && !empty($pedidosDespachados->CANT_PED_PEND))
            $totalPendientes += $pedidosDespachados->CANT_PED_PEND;
        if ($pedidosRecibidosNoEntregados && !empty($pedidosRecibidosNoEntregados->CANT_PED_PEND))
            $totalPendientes += $pedidosRecibidosNoEntregados->CANT_PED_PEND;
        if ($pedidosRetiroTienda && !empty($pedidosRetiroTienda->CANT_PED_RETIRO))
            $totalPendientes += $pedidosRetiroTienda->CANT_PED_RETIRO;
        if ($pedidosPendientesControlSucursales && !empty($pedidosPendientesControlSucursales->CANTIDAD_PEDIDOS))
            $totalPendientes += $pedidosPendientesControlSucursales->CANTIDAD_PEDIDOS;
        if ($pedidosIncompletosSucursales && !empty($pedidosIncompletosSucursales->CANT_PEDIDOS_INCOMPLETOS))
            $totalPendientes += $pedidosIncompletosSucursales->CANT_PEDIDOS_INCOMPLETOS;

        ob_start();
        ?>
        <div class="row">
            <?php include __DIR__ . '/../tabs/cards/card-ordenes-cierre-vtex.php'; ?>
            <?php include __DIR__ . '/../tabs/cards/card-pedidos-despachados-suc.php'; ?>
            <?php include __DIR__ . '/../tabs/cards/card-retiro-stock-central.php'; ?>
            <?php include __DIR__ . '/../tabs/cards/card-retiro-stock-sucursal.php'; ?>
        </div>
        <div class="row mt-4">
            <?php include __DIR__ . '/../tabs/cards/card-control-sucursales.php'; ?>
            <?php include __DIR__ . '/../tabs/cards/card-incompletos-sucursales.php'; ?>
        </div>
        <?php
        $html = ob_get_clean();

        echo json_encode([
            'success'   => true,
            'html'      => $html,
            'badges'    => ['operaciones-sucursales-tab' => $totalPendientes],
            'timestamp' => date('d/m/Y H:i:s')
        ]);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

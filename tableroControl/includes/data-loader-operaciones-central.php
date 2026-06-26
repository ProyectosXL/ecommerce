<?php
// includes/data-loader-operaciones-central.php
// Devuelve HTML de la pestaña Operaciones Central (o de una card individual con ?card=X)

header('Content-Type: application/json');
require_once __DIR__ . '/../../Class/Control.php';

$cardFilter = isset($_GET['card']) ? $_GET['card'] : null;

// Cálculo de flag "NUEVO" para pedidos incompletos
$mostrarNuevoPedidosIncompletos = (new DateTime())->diff(new DateTime('2025-11-18'))->days <= 7;

try {
    $control = new Control();

    if ($cardFilter) {
        $html = '';
        switch ($cardFilter) {
            case 'pedidosPreparar':
                $pedidosPendientesPreparar = $control->traerPedidosPendientesPreparar();
                ob_start();
                include __DIR__ . '/../tabs/cards/card-pedidos-preparar-central.php';
                $html = ob_get_clean();
                break;
            case 'flexCentral':
                $pedidosFlexCentral = $control->traerPedidosFlex();
                ob_start();
                include __DIR__ . '/../tabs/cards/card-flex-central.php';
                $html = ob_get_clean();
                break;
            case 'despachoNormal':
                $pedidosPendienteDespacho = $control->traerPedidosPendienteDespacho();
                ob_start();
                include __DIR__ . '/../tabs/cards/card-despacho-normal-central.php';
                $html = ob_get_clean();
                break;
            case 'remitosSinIngresar':
                $remitosSinIntegrar = $control->traerRemitosSinIntegrar();
                ob_start();
                include __DIR__ . '/../tabs/cards/card-remitos-sin-ingresar.php';
                $html = ob_get_clean();
                break;
            case 'controlCentral':
                $pedidosPendientesControlCentral = $control->traerResumenPedidosPendientesControlCentral();
                ob_start();
                include __DIR__ . '/../tabs/cards/card-control-central.php';
                $html = ob_get_clean();
                break;
            case 'incompletosCentral':
                $pedidosIncompletosCentral = $control->traerPedidosIncompletosCentral();
                ob_start();
                include __DIR__ . '/../tabs/cards/card-incompletos-central.php';
                $html = ob_get_clean();
                break;
            case 'sincronizadosSinStock':
                $pedidosSincronizadosSinStock = $control->traerPedidosSincronizadosSinStock();
                ob_start();
                include __DIR__ . '/../tabs/cards/card-sincronizados-sin-stock.php';
                $html = ob_get_clean();
                break;
        }
        echo json_encode([
            'success'   => true,
            'html'      => $html,
            'timestamp' => date('d/m/Y H:i:s')
        ]);
    } else {
        $pedidosPendientesPreparar       = $control->traerPedidosPendientesPreparar();
        $pedidosFlexCentral              = $control->traerPedidosFlex();
        $pedidosPendienteDespacho        = $control->traerPedidosPendienteDespacho();
        $remitosSinIntegrar              = $control->traerRemitosSinIntegrar();
        $pedidosPendientesControlCentral = $control->traerResumenPedidosPendientesControlCentral();
        $pedidosIncompletosCentral       = $control->traerPedidosIncompletosCentral();
        $pedidosSincronizadosSinStock    = $control->traerPedidosSincronizadosSinStock();

        $totalPendientes = 0;
        if ($pedidosPendientesPreparar && !empty($pedidosPendientesPreparar->CANT_PED_PEND))
            $totalPendientes += $pedidosPendientesPreparar->CANT_PED_PEND;
        if ($pedidosFlexCentral && !empty($pedidosFlexCentral->CANT_PED_PEND))
            $totalPendientes += $pedidosFlexCentral->CANT_PED_PEND;
        if ($pedidosPendienteDespacho && !empty($pedidosPendienteDespacho->CANT_PED_PEND))
            $totalPendientes += $pedidosPendienteDespacho->CANT_PED_PEND;
        if ($remitosSinIntegrar !== null && is_array($remitosSinIntegrar) && count($remitosSinIntegrar) > 0)
            $totalPendientes += count($remitosSinIntegrar);
        if ($pedidosPendientesControlCentral && !empty($pedidosPendientesControlCentral->CANTIDAD_PEDIDOS))
            $totalPendientes += $pedidosPendientesControlCentral->CANTIDAD_PEDIDOS;
        if ($pedidosIncompletosCentral && !empty($pedidosIncompletosCentral->CANT_PEDIDOS_INCOMPLETOS))
            $totalPendientes += $pedidosIncompletosCentral->CANT_PEDIDOS_INCOMPLETOS;
        if ($pedidosSincronizadosSinStock && !empty($pedidosSincronizadosSinStock->CANT_PEDIDOS))
            $totalPendientes += $pedidosSincronizadosSinStock->CANT_PEDIDOS;

        ob_start();
        ?>
        <div class="row">
            <?php include __DIR__ . '/../tabs/cards/card-pedidos-preparar-central.php'; ?>
            <?php include __DIR__ . '/../tabs/cards/card-flex-central.php'; ?>
            <?php include __DIR__ . '/../tabs/cards/card-despacho-normal-central.php'; ?>
            <?php include __DIR__ . '/../tabs/cards/card-remitos-sin-ingresar.php'; ?>
        </div>
        <div class="row mt-4">
            <?php include __DIR__ . '/../tabs/cards/card-control-central.php'; ?>
            <?php include __DIR__ . '/../tabs/cards/card-incompletos-central.php'; ?>
            <?php include __DIR__ . '/../tabs/cards/card-sincronizados-sin-stock.php'; ?>
        </div>
        <?php
        $html = ob_get_clean();

        echo json_encode([
            'success'   => true,
            'html'      => $html,
            'badges'    => ['operaciones-central-tab' => $totalPendientes],
            'timestamp' => date('d/m/Y H:i:s')
        ]);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

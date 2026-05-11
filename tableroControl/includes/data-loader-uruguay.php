<?php
// includes/data-loader-uruguay.php
// Devuelve HTML de la pestaña Uruguay (o de una card individual con ?card=X)

header('Content-Type: application/json');
require_once __DIR__ . '/../../Class/Control.php';

$cardFilter = isset($_GET['card']) ? $_GET['card'] : null;

$mostrarNuevoUruguay = (new DateTime())->diff(new DateTime('2025-11-18'))->days <= 7;

try {
    $control = new Control();

    if ($cardFilter) {
        $html = '';
        switch ($cardFilter) {
            case 'pedidosSinFacturarUy':
                $pedidosSinFacturarUruguay = $control->traerPedidosSinFactTiendasUruguay();
                ob_start();
                include __DIR__ . '/../tabs/cards/card-pedidos-sin-facturar-uy.php';
                $html = ob_get_clean();
                break;
            case 'facturasSinRemitoUy':
                $pedidosSinRemitoUruguay = $control->traerFacturasSinRemitoUruguay();
                ob_start();
                include __DIR__ . '/../tabs/cards/card-facturas-sin-remito-uy.php';
                $html = ob_get_clean();
                break;
            case 'ncDevolucionesUy':
                $ncDevolucionesUruguay = $control->traerNcPendDevolucionesUruguay();
                ob_start();
                include __DIR__ . '/../tabs/cards/card-nc-devoluciones-uy.php';
                $html = ob_get_clean();
                break;
            case 'ordenesSinIntegrarUy':
                $ordenesSinIntegrarUruguay = $control->traerOrdenesSinIntegrarUruguay();
                ob_start();
                include __DIR__ . '/../tabs/cards/card-ordenes-sin-integrar-uy.php';
                $html = ob_get_clean();
                break;
            case 'ordenesCierreUy':
                $ordenesPendientesCierreUruguay = $control->traerOrdenesPendientesCierreUruguay();
                ob_start();
                include __DIR__ . '/../tabs/cards/card-ordenes-cierre-uy.php';
                $html = ob_get_clean();
                break;
            case 'retiroTiendaUy':
                $pedidosRetiroTiendaUruguay = $control->traerPedidosRetiroTiendaUruguay();
                ob_start();
                include __DIR__ . '/../tabs/cards/card-retiro-tienda-uy.php';
                $html = ob_get_clean();
                break;
            case 'controlSucursalesUy':
                $pedidosPendientesControlSucursalesUruguay = $control->traerResumenPedidosPendientesControlSucursalesUruguay();
                ob_start();
                include __DIR__ . '/../tabs/cards/card-control-sucursales-uy.php';
                $html = ob_get_clean();
                break;
        }
        echo json_encode([
            'success'   => true,
            'html'      => $html,
            'timestamp' => date('d/m/Y H:i:s')
        ]);
    } else {
        $pedidosSinFacturarUruguay                  = $control->traerPedidosSinFactTiendasUruguay();
        $pedidosSinRemitoUruguay                    = $control->traerFacturasSinRemitoUruguay();
        $ncDevolucionesUruguay                      = $control->traerNcPendDevolucionesUruguay();
        $ordenesSinIntegrarUruguay                  = $control->traerOrdenesSinIntegrarUruguay();
        $ordenesPendientesCierreUruguay             = $control->traerOrdenesPendientesCierreUruguay();
        $pedidosRetiroTiendaUruguay                 = $control->traerPedidosRetiroTiendaUruguay();
        $pedidosPendientesControlSucursalesUruguay  = $control->traerResumenPedidosPendientesControlSucursalesUruguay();

        $totalPendientes = 0;
        if ($pedidosSinFacturarUruguay && !empty($pedidosSinFacturarUruguay->CANT_PED_SIN_FACT))
            $totalPendientes += $pedidosSinFacturarUruguay->CANT_PED_SIN_FACT;
        if ($pedidosSinRemitoUruguay && !empty($pedidosSinRemitoUruguay->CANT_FACTURAS))
            $totalPendientes += $pedidosSinRemitoUruguay->CANT_FACTURAS;
        if ($ncDevolucionesUruguay && !empty($ncDevolucionesUruguay->CANT_NC_DEV))
            $totalPendientes += $ncDevolucionesUruguay->CANT_NC_DEV;
        if ($ordenesSinIntegrarUruguay && !empty($ordenesSinIntegrarUruguay->CANT_ORDENES))
            $totalPendientes += $ordenesSinIntegrarUruguay->CANT_ORDENES;
        if ($ordenesPendientesCierreUruguay && !empty($ordenesPendientesCierreUruguay->CANT_ORDENES))
            $totalPendientes += $ordenesPendientesCierreUruguay->CANT_ORDENES;
        if ($pedidosRetiroTiendaUruguay && !empty($pedidosRetiroTiendaUruguay->CANT_PED_RETIRO))
            $totalPendientes += $pedidosRetiroTiendaUruguay->CANT_PED_RETIRO;
        if ($pedidosPendientesControlSucursalesUruguay && !empty($pedidosPendientesControlSucursalesUruguay->CANTIDAD_PEDIDOS))
            $totalPendientes += $pedidosPendientesControlSucursalesUruguay->CANTIDAD_PEDIDOS;

        ob_start();
        ?>
        <?php if ($mostrarNuevoUruguay): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-sparkles fa-2x me-3"></i>
                <div>
                    <h5 class="alert-heading mb-1">
                        <i class="fas fa-star"></i> ¡Nueva Pestaña Uruguay Disponible!
                    </h5>
                    <p class="mb-0">
                        Ahora puedes visualizar y gestionar todas las operaciones de Uruguay desde esta nueva sección del tablero.
                    </p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        <div class="row">
            <?php include __DIR__ . '/../tabs/cards/card-pedidos-sin-facturar-uy.php'; ?>
            <?php include __DIR__ . '/../tabs/cards/card-facturas-sin-remito-uy.php'; ?>
            <?php include __DIR__ . '/../tabs/cards/card-nc-devoluciones-uy.php'; ?>
            <?php include __DIR__ . '/../tabs/cards/card-ordenes-sin-integrar-uy.php'; ?>
        </div>
        <div class="row mt-4">
            <?php include __DIR__ . '/../tabs/cards/card-ordenes-cierre-uy.php'; ?>
            <?php include __DIR__ . '/../tabs/cards/card-retiro-tienda-uy.php'; ?>
            <?php include __DIR__ . '/../tabs/cards/card-control-sucursales-uy.php'; ?>
        </div>
        <?php
        $html = ob_get_clean();

        echo json_encode([
            'success'   => true,
            'html'      => $html,
            'badges'    => ['uruguay-tab' => $totalPendientes],
            'timestamp' => date('d/m/Y H:i:s')
        ]);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

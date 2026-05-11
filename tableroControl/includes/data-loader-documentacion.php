<?php
// includes/data-loader-documentacion.php
// Devuelve HTML de la pestaña Documentación (o de una card individual con ?card=X)

header('Content-Type: application/json');
require_once __DIR__ . '/../../Class/Control.php';

$cardFilter = isset($_GET['card']) ? $_GET['card'] : null;

try {
    $control = new Control();

    if ($cardFilter) {
        // Modo granular: refrescar solo una card
        $html = '';
        switch ($cardFilter) {
            case 'pedidosSinFacturar':
                $pedidosSinFacturar = $control->traerPedidosSinFactTiendas();
                ob_start();
                include __DIR__ . '/../tabs/cards/card-pedidos-sin-facturar.php';
                $html = ob_get_clean();
                break;
            case 'facturasSinRemito':
                $facturasSinRemito = $control->traerFacturasSinRemito();
                ob_start();
                include __DIR__ . '/../tabs/cards/card-facturas-sin-remito.php';
                $html = ob_get_clean();
                break;
            case 'ncPromociones':
                $ncPromociones = $control->traerNcPendPromociones();
                ob_start();
                include __DIR__ . '/../tabs/cards/card-nc-promociones.php';
                $html = ob_get_clean();
                break;
            case 'ncDevoluciones':
                $ncDevoluciones = $control->traerNcPendDevoluciones();
                ob_start();
                include __DIR__ . '/../tabs/cards/card-nc-devoluciones.php';
                $html = ob_get_clean();
                break;
        }
        echo json_encode([
            'success' => true,
            'html'    => $html,
            'timestamp' => date('d/m/Y H:i:s')
        ]);
    } else {
        // Modo completo: cargar toda la pestaña
        $pedidosSinFacturar = $control->traerPedidosSinFactTiendas();
        $facturasSinRemito  = $control->traerFacturasSinRemito();
        $ncPromociones      = $control->traerNcPendPromociones();
        $ncDevoluciones     = $control->traerNcPendDevoluciones();
        $ncrData            = $control->traerNcrRealizadas();

        // Calcular total pendientes para badge
        $totalPendientes = 0;
        if ($pedidosSinFacturar && !empty($pedidosSinFacturar->CANT_PED_SIN_FACT))
            $totalPendientes += $pedidosSinFacturar->CANT_PED_SIN_FACT;
        if ($facturasSinRemito && !empty($facturasSinRemito->CANT_FACTURAS))
            $totalPendientes += $facturasSinRemito->CANT_FACTURAS;
        if ($ncPromociones && !empty($ncPromociones->CANT_NC_PROMO))
            $totalPendientes += $ncPromociones->CANT_NC_PROMO;
        if ($ncDevoluciones && !empty($ncDevoluciones->CANT_NC_DEV))
            $totalPendientes += $ncDevoluciones->CANT_NC_DEV;

        // Capturar HTML de las cards
        ob_start();
        ?>
        <div class="row">
            <?php include __DIR__ . '/../tabs/cards/card-pedidos-sin-facturar.php'; ?>
            <?php include __DIR__ . '/../tabs/cards/card-facturas-sin-remito.php'; ?>
            <?php include __DIR__ . '/../tabs/cards/card-nc-promociones.php'; ?>
            <?php include __DIR__ . '/../tabs/cards/card-nc-devoluciones.php'; ?>
        </div>
        <?php
        $html = ob_get_clean();

        // Datos del gráfico NCR
        $chartLabels = [];
        $chartValues = [];
        if (!empty($ncrData)) {
            foreach ($ncrData as $row) {
                $chartLabels[] = $row->FECHA_EMIS->format('d/m/Y');
                $chartValues[] = (int)$row->CANT_NCR;
            }
        }

        echo json_encode([
            'success'   => true,
            'html'      => $html,
            'badges'    => ['documentacion-tab' => $totalPendientes],
            'timestamp' => date('d/m/Y H:i:s'),
            'chartData' => ['labels' => $chartLabels, 'values' => $chartValues]
        ]);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

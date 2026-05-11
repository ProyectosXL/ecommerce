<?php
// includes/data-loader-integraciones.php
// Devuelve HTML de la pestaña Integraciones (o de una card individual con ?card=X)

header('Content-Type: application/json');
require_once __DIR__ . '/../../Class/Control.php';

$cardFilter = isset($_GET['card']) ? $_GET['card'] : null;

try {
    $control = new Control();

    if ($cardFilter) {
        $html = '';
        switch ($cardFilter) {
            case 'ordenesSinIntegrar':
                $ordenesSinIntegrar = $control->traerOrdenesSinIntegrar();
                ob_start();
                include __DIR__ . '/../tabs/cards/card-ordenes-sin-integrar.php';
                $html = ob_get_clean();
                break;
            case 'productosMl':
                // Linked server deshabilitado: devolver card con sin datos
                $productosMlFull = null;
                ob_start();
                include __DIR__ . '/../tabs/cards/card-productos-ml.php';
                $html = ob_get_clean();
                break;
        }
        echo json_encode([
            'success'   => true,
            'html'      => $html,
            'timestamp' => date('d/m/Y H:i:s')
        ]);
    } else {
        $ordenesSinIntegrar = $control->traerOrdenesSinIntegrar();
        $productosMlFull    = null; // Linked server deshabilitado

        $totalPendientes = 0;
        if ($ordenesSinIntegrar && !empty($ordenesSinIntegrar->CANT_ORDENES))
            $totalPendientes += $ordenesSinIntegrar->CANT_ORDENES;

        ob_start();
        ?>
        <div class="row">
            <?php include __DIR__ . '/../tabs/cards/card-ordenes-sin-integrar.php'; ?>
            <?php include __DIR__ . '/../tabs/cards/card-productos-ml.php'; ?>
        </div>
        <?php
        $html = ob_get_clean();

        echo json_encode([
            'success'   => true,
            'html'      => $html,
            'badges'    => ['integraciones-tab' => $totalPendientes],
            'timestamp' => date('d/m/Y H:i:s')
        ]);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

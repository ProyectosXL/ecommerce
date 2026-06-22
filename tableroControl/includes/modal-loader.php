<?php
// includes/modal-loader.php
// Carga el contenido dinámico de un modal vía AJAX (?modal=loaderKey)
// Devuelve JSON: { success, html, extra, timestamp }
// html  → filas <tr> para el <tbody class="modal-lazy-tbody">
// extra → HTML opcional que va en <div class="modal-lazy-extra"> (ranking stats)

header('Content-Type: application/json');
require_once __DIR__ . '/../../Class/Control.php';

$modalKey = isset($_GET['modal']) ? $_GET['modal'] : null;

if (!$modalKey) {
    echo json_encode(['success' => false, 'error' => 'Parámetro modal requerido']);
    exit;
}

try {
    $control = new Control();
    $html    = '';
    $extra   = '';

    switch ($modalKey) {

        // ── Documentación ──────────────────────────────────────────────────────

        case 'facturasSinRemito':
            $detalleFacturas = $control->traerDetalleFacturasSinRemito();
            $fechaActual = new DateTime();
            ob_start();
            if (!empty($detalleFacturas)):
                foreach ($detalleFacturas as $detalle):
                    $diasTranscurridos = $fechaActual->diff(clone $detalle->FECHA_FACTURA)->days;
                    $excedeDias = $diasTranscurridos > 10;
                    ?>
                    <tr class="<?php echo $excedeDias ? 'text-danger' : ''; ?>">
                        <td><?php echo htmlspecialchars($detalle->SUCURSAL); ?></td>
                        <td><?php echo $detalle->FECHA_FACTURA->format('d/m/Y'); ?></td>
                        <td><?php echo htmlspecialchars($detalle->FACTURA); ?></td>
                        <td><?php echo htmlspecialchars($detalle->COD_ARTICU); ?></td>
                        <td><?php echo htmlspecialchars($detalle->DESC_CTA_ARTICULO); ?></td>
                        <td class="text-end"><?php echo number_format($detalle->CANTIDAD, 0); ?></td>
                        <td class="text-center">
                            <?php if ($excedeDias): ?>
                                <i class="fas fa-exclamation-circle text-danger" data-bs-toggle="tooltip" data-bs-placement="left" title="Excede los 10 días (<?php echo $diasTranscurridos; ?> días)"></i>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php
                endforeach;
            else: ?>
                <tr><td colspan="7" class="text-center">No hay datos para mostrar</td></tr>
            <?php endif;
            $html = ob_get_clean();
            break;

        case 'ncDevoluciones':
            $detalleNcDevoluciones = $control->traerDetalleNcPendDevoluciones();
            ob_start();
            if (!empty($detalleNcDevoluciones)):
                foreach ($detalleNcDevoluciones as $detalle): ?>
                    <tr>
                        <td><?php echo $detalle->FECHA_PEDI->format('d/m/Y'); ?></td>
                        <td><?php echo htmlspecialchars($detalle->NRO_PEDIDO); ?></td>
                        <td><?php echo htmlspecialchars($detalle->ORDER_ID_TIENDA); ?></td>
                        <td><?php echo htmlspecialchars($detalle->CLIENTE); ?></td>
                        <td><?php echo htmlspecialchars($detalle->COD_SUCURS); ?></td>
                        <td><?php echo htmlspecialchars($detalle->SUCURSAL); ?></td>
                        <td><?php echo htmlspecialchars($detalle->N_COMP); ?></td>
                        <td class="text-end">$<?php echo number_format($detalle->IMPORTE, 0); ?></td>
                    </tr>
                    <?php
                endforeach;
            else: ?>
                <tr><td colspan="8" class="text-center">No hay datos para mostrar</td></tr>
            <?php endif;
            $html = ob_get_clean();
            break;

        case 'ncPromociones':
            $detalleNcPromociones = $control->traerDetalleNcPendPromociones();
            ob_start();
            if (!empty($detalleNcPromociones)):
                foreach ($detalleNcPromociones as $detalle): ?>
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" class="nc-promo-check"
                                data-fecha="<?php echo $detalle->FECHA->format('Y-m-d'); ?>"
                                data-promo="<?php echo htmlspecialchars($detalle->COD_PROMOCION_TARJETA); ?>"
                                data-articu="<?php echo htmlspecialchars($detalle->COD_ARTICU); ?>"
                                data-nc="<?php echo $detalle->NC; ?>">
                        </td>
                        <td><?php echo $detalle->FECHA->format('d/m/Y'); ?></td>
                        <td><?php echo htmlspecialchars($detalle->COD_PROMOCION_TARJETA); ?></td>
                        <td><?php echo htmlspecialchars($detalle->DESC_PROMOCION_TARJETA); ?></td>
                        <td><?php echo htmlspecialchars($detalle->PORC_REINTEGRO); ?></td>
                        <td><?php echo htmlspecialchars($detalle->COD_ARTICU); ?></td>
                        <td class="text-end">$<?php echo number_format($detalle->NC, 0); ?></td>
                    </tr>
                    <?php
                endforeach;
            else: ?>
                <tr><td colspan="7" class="text-center">No hay datos para mostrar</td></tr>
            <?php endif;
            $html = ob_get_clean();
            break;

        case 'ncPromoHistorial':
            $historialNc = $control->traerHistorialNcProcesadas();
            // Agrupar por número de comprobante
            $grupos = [];
            foreach ($historialNc as $fila) {
                $key = $fila->NUM_NC;
                if (!isset($grupos[$key])) {
                    $grupos[$key] = [
                        'num_nc'   => $fila->NUM_NC,
                        'cantidad' => 0,
                        'total'    => 0,
                        'registro' => $fila->FECHA_REGISTRO,
                        'filas'    => []
                    ];
                }
                $grupos[$key]['cantidad']++;
                $grupos[$key]['total'] += (float)$fila->NC;
                if ($fila->FECHA_REGISTRO > $grupos[$key]['registro']) {
                    $grupos[$key]['registro'] = $fila->FECHA_REGISTRO;
                }
                $grupos[$key]['filas'][] = $fila;
            }
            ob_start();
            if (!empty($grupos)):
                $idx = 0;
                foreach ($grupos as $grupo):
                    $idx++;
                    $collapseId = 'grpNc' . $idx; ?>
                    <tr role="button" class="table-light" data-bs-toggle="collapse" data-bs-target="#<?php echo $collapseId; ?>" aria-expanded="false">
                        <td class="text-center"><i class="fas fa-chevron-right"></i></td>
                        <td><strong><?php echo htmlspecialchars($grupo['num_nc']); ?></strong></td>
                        <td class="text-center"><?php echo $grupo['cantidad']; ?></td>
                        <td class="text-end">$<?php echo number_format($grupo['total'], 0); ?></td>
                        <td><?php echo $grupo['registro']->format('d/m/Y H:i'); ?></td>
                    </tr>
                    <tr class="collapse" id="<?php echo $collapseId; ?>">
                        <td colspan="5" class="p-0">
                            <table class="table table-sm mb-0">
                                <thead class="table-secondary">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Cód. Promoción</th>
                                        <th>Cód. Artículo</th>
                                        <th class="text-end">Importe NC</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($grupo['filas'] as $f): ?>
                                        <tr>
                                            <td><?php echo $f->FECHA->format('d/m/Y'); ?></td>
                                            <td><?php echo htmlspecialchars($f->COD_PROMOCION_TARJETA); ?></td>
                                            <td><?php echo htmlspecialchars($f->COD_ARTICU); ?></td>
                                            <td class="text-end">$<?php echo number_format($f->NC, 0); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <?php
                endforeach;
            else: ?>
                <tr><td colspan="5" class="text-center">No hay NC procesadas registradas</td></tr>
            <?php endif;
            $html = ob_get_clean();
            break;

        case 'pedidosSinFacturar':
            $detallePedidosSinFact = $control->traerDetallePedidosSinFactTiendas();
            ob_start();
            if (!empty($detallePedidosSinFact)):
                foreach ($detallePedidosSinFact as $detalle): ?>
                    <tr>
                        <td><?php echo $detalle->FECHA_HORA->format('d/m/Y H:i'); ?></td>
                        <td><?php echo htmlspecialchars($detalle->CANAL); ?></td>
                        <td><?php echo htmlspecialchars($detalle->NRO_PEDIDO); ?></td>
                        <td><?php echo htmlspecialchars($detalle->ORDER_ID_TIENDA); ?></td>
                        <td><?php echo htmlspecialchars($detalle->CLIENTE); ?></td>
                        <td class="text-end">$<?php echo number_format($detalle->TOTAL_PEDI, 0); ?></td>
                    </tr>
                    <?php
                endforeach;
            else: ?>
                <tr><td colspan="6" class="text-center">No hay pedidos pendientes</td></tr>
            <?php endif;
            $html = ob_get_clean();
            break;

        // ── Integraciones ──────────────────────────────────────────────────────

        case 'ordenesSinIntegrar':
            $detalleOrdenes = $control->traerDetalleOrdenesSinIntegrar();
            ob_start();
            if (!empty($detalleOrdenes)):
                foreach ($detalleOrdenes as $detalle): ?>
                    <tr>
                        <td><?php echo $detalle->FECHA_ORDEN->format('d/m/Y H:i'); ?></td>
                        <td><?php echo htmlspecialchars($detalle->TIENDA); ?></td>
                        <td><?php echo htmlspecialchars($detalle->ORDER_NRO_TIENDA); ?></td>
                        <td class="text-end">$<?php echo number_format($detalle->TOTAL_ORDEN, 0); ?></td>
                    </tr>
                    <?php
                endforeach;
            else: ?>
                <tr><td colspan="4" class="text-center">No hay datos para mostrar</td></tr>
            <?php endif;
            $html = ob_get_clean();
            break;

        case 'productosMl':
            // Linked server deshabilitado
            ob_start(); ?>
            <tr><td colspan="6" class="text-center text-muted">Datos no disponibles (servidor vinculado deshabilitado)</td></tr>
            <?php
            $html = ob_get_clean();
            break;

        // ── Operaciones Central ────────────────────────────────────────────────

        case 'pedidosPreparar':
            $detallePendientesPreparar = $control->traerDetallePedidosPendientesPreparar();
            ob_start();
            if (!empty($detallePendientesPreparar)):
                foreach ($detallePendientesPreparar as $detalle): ?>
                    <tr>
                        <td><?php echo $detalle->FECHA_PEDI->format('d/m/Y H:i'); ?></td>
                        <td><?php echo $detalle->FECHA_SINCRONIZADO ? $detalle->FECHA_SINCRONIZADO->format('d/m/Y H:i') : 'N/A'; ?></td>
                        <td><?php echo htmlspecialchars($detalle->CANAL); ?></td>
                        <td><?php echo htmlspecialchars($detalle->NRO_PEDIDO); ?></td>
                        <td><?php echo htmlspecialchars($detalle->ORDER_ID_TIENDA); ?></td>
                        <td><?php echo htmlspecialchars($detalle->CLIENTE); ?></td>
                        <td><span class="badge bg-warning">Pendiente Asignación</span></td>
                        <td class="text-end">$<?php echo number_format($detalle->TOTAL_PEDI, 0); ?></td>
                    </tr>
                    <?php
                endforeach;
            else: ?>
                <tr><td colspan="8" class="text-center">No hay pedidos pendientes de preparar</td></tr>
            <?php endif;
            $html = ob_get_clean();
            break;

        case 'flexCentral':
            $detalleFlex = $control->traerDetallePedidosFlex();
            ob_start();
            if (!empty($detalleFlex)):
                foreach ($detalleFlex as $detalle): ?>
                    <tr>
                        <td><?php echo (isset($detalle->FECHA_SINCRONIZADO) && $detalle->FECHA_SINCRONIZADO instanceof DateTime) ? $detalle->FECHA_SINCRONIZADO->format('d/m/Y H:i') : '-'; ?></td>
                        <td><?php echo htmlspecialchars($detalle->CANAL); ?></td>
                        <td><?php echo htmlspecialchars($detalle->NRO_PEDIDO); ?></td>
                        <td><?php echo htmlspecialchars($detalle->ORDER_ID_TIENDA); ?></td>
                        <td><?php echo htmlspecialchars($detalle->CLIENTE); ?></td>
                        <td class="text-end">$<?php echo number_format($detalle->TOTAL_PEDI, 0); ?></td>
                    </tr>
                    <?php
                endforeach;
            else: ?>
                <tr><td colspan="6" class="text-center">No hay pedidos pendientes</td></tr>
            <?php endif;
            $html = ob_get_clean();
            break;

        case 'despachoNormal':
            $detallePedidos = $control->traerDetallePedidosPendienteDespacho();
            ob_start();
            if (!empty($detallePedidos)):
                foreach ($detallePedidos as $detalle): ?>
                    <tr>
                        <td><?php echo $detalle->FECHA_SINCRONIZADO ? $detalle->FECHA_SINCRONIZADO->format('d/m/Y H:i') : ''; ?></td>
                        <td><?php echo htmlspecialchars($detalle->CANAL); ?></td>
                        <td><?php echo htmlspecialchars($detalle->SUCURSAL_ENTREGA); ?></td>
                        <td><?php echo $detalle->FECHA_DESPACHO ? $detalle->FECHA_DESPACHO->format('d/m/Y') : ''; ?></td>
                        <td><?php echo htmlspecialchars($detalle->NRO_PEDIDO); ?></td>
                        <td><?php echo htmlspecialchars($detalle->ORDER_ID_TIENDA); ?></td>
                        <td><?php echo htmlspecialchars($detalle->CLIENTE); ?></td>
                        <td class="text-end">$<?php echo number_format($detalle->TOTAL_PEDI, 0); ?></td>
                    </tr>
                    <?php
                endforeach;
            else: ?>
                <tr><td colspan="8" class="text-center">No hay pedidos pendientes</td></tr>
            <?php endif;
            $html = ob_get_clean();
            break;

        case 'remitosSinIngresar':
            $remitosSinIntegrar = $control->traerRemitosSinIntegrar();
            ob_start();
            if (!empty($remitosSinIntegrar)):
                foreach ($remitosSinIntegrar as $remito): ?>
                    <tr>
                        <td><?php echo $remito->FECHA_MOV->format('d/m/Y'); ?></td>
                        <td><?php echo htmlspecialchars($remito->COD_PRO_CL); ?></td>
                        <td><?php echo htmlspecialchars($remito->N_COMP); ?></td>
                        <td class="text-end"><?php echo number_format($remito->CANTIDAD, 0); ?></td>
                    </tr>
                    <?php
                endforeach;
            else: ?>
                <tr><td colspan="4" class="text-center">No hay remitos sin integrar</td></tr>
            <?php endif;
            $html = ob_get_clean();
            break;

        case 'controlCentral':
            $detallePedidosControlCentral = $control->traerDetallePedidosPendientesControlCentral();
            ob_start();
            if (!empty($detallePedidosControlCentral)):
                foreach ($detallePedidosControlCentral as $detalle): ?>
                    <tr>
                        <td><?php echo $detalle->FECHA_SINCRONIZADO->format('d/m/Y H:i'); ?></td>
                        <td><?php echo htmlspecialchars($detalle->CANAL); ?></td>
                        <td><?php echo htmlspecialchars($detalle->NRO_PEDIDO); ?></td>
                        <td><?php echo htmlspecialchars($detalle->ORDER_ID); ?></td>
                        <td><?php echo htmlspecialchars($detalle->CLIENTE); ?></td>
                        <td><?php echo htmlspecialchars($detalle->SUCURSAL_PREPARA); ?></td>
                    </tr>
                    <?php
                endforeach;
            else: ?>
                <tr><td colspan="6" class="text-center">No hay pedidos pendientes</td></tr>
            <?php endif;
            $html = ob_get_clean();
            break;

        case 'incompletosCentral':
            $detalleIncompletos = $control->traerDetallePedidosIncompletosCentral();
            ob_start();
            if (!empty($detalleIncompletos)):
                foreach ($detalleIncompletos as $detalle): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($detalle->ORIGEN); ?></td>
                        <td><?php echo htmlspecialchars($detalle->NRO_ORDEN_ECOMMERCE); ?></td>
                        <td><?php echo htmlspecialchars($detalle->NRO_PEDIDO); ?></td>
                        <td><?php echo $detalle->FECHA_PEDID->format('d/m/Y'); ?></td>
                        <td><?php echo htmlspecialchars($detalle->CLIENTE); ?></td>
                        <td><?php echo htmlspecialchars($detalle->COD_ARTICU); ?></td>
                        <td><?php echo htmlspecialchars($detalle->DESCRIPCIO); ?></td>
                        <td class="text-end"><?php echo htmlspecialchars($detalle->CANT_PEDID); ?></td>
                        <td class="text-end"><?php echo htmlspecialchars($detalle->CANT_AUDITADO); ?></td>
                        <td><?php echo htmlspecialchars($detalle->METODO_ENVIO); ?></td>
                        <td><?php echo htmlspecialchars($detalle->TIENDA); ?></td>
                    </tr>
                    <?php
                endforeach;
            else: ?>
                <tr><td colspan="11" class="text-center">No hay pedidos incompletos</td></tr>
            <?php endif;
            $html = ob_get_clean();
            break;

        // ── Operaciones Sucursales ─────────────────────────────────────────────

        case 'ordenesCierreVtex':
            $detalleOrdenesCierre = $control->traerDetalleOrdenesPendientesCierre();
            ob_start();
            if (!empty($detalleOrdenesCierre)):
                foreach ($detalleOrdenesCierre as $detalle):
                    $excedeDias = $detalle->DIAS_ANTIGUEDAD > 10; ?>
                    <tr class="<?php echo $excedeDias ? 'text-danger' : ''; ?>">
                        <td><?php echo $detalle->FECHA->format('d/m/Y H:i'); ?></td>
                        <td><?php echo htmlspecialchars($detalle->ORDER_ID); ?></td>
                        <td><?php echo htmlspecialchars($detalle->CLIENTE); ?></td>
                        <td><?php echo htmlspecialchars($detalle->SUCURSAL); ?></td>
                        <td class="text-end"><?php echo number_format($detalle->DIAS_ANTIGUEDAD, 0); ?></td>
                        <td class="text-center">
                            <?php if ($excedeDias): ?>
                                <i class="fas fa-exclamation-circle text-danger" data-bs-toggle="tooltip" title="Excede los 10 días"></i>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php
                endforeach;
            else: ?>
                <tr><td colspan="6" class="text-center">No hay datos para mostrar</td></tr>
            <?php endif;
            $html = ob_get_clean();
            break;

        case 'pedidosDespachados':
            $detallePedidosDespachados = $control->traerDetallePedidosDespachados();
            ob_start();
            if (!empty($detallePedidosDespachados)):
                foreach ($detallePedidosDespachados as $detalle):
                    $excedeDias = $detalle->DIAS_PENDIENTE > 3; ?>
                    <tr class="<?php echo $excedeDias ? 'text-danger' : ''; ?>">
                        <td><?php echo $detalle->FECHA_PEDIDO ? $detalle->FECHA_PEDIDO->format('d/m/Y H:i') : ''; ?></td>
                        <td><?php echo htmlspecialchars($detalle->NRO_PEDIDO); ?></td>
                        <td><?php echo htmlspecialchars($detalle->ORDER_ID); ?></td>
                        <td><?php echo htmlspecialchars($detalle->SUCURSAL_ENTREGA); ?></td>
                        <td><?php echo $detalle->FECHA_DESPACHADO ? $detalle->FECHA_DESPACHADO->format('d/m/Y') : ''; ?></td>
                        <td><?php echo $detalle->DIAS_PENDIENTE; ?></td>
                        <td class="text-end">$<?php echo number_format($detalle->TOTAL_PEDI, 0); ?></td>
                        <td class="text-center">
                            <?php if ($excedeDias): ?>
                                <i class="fas fa-exclamation-circle text-danger" data-bs-toggle="tooltip" data-bs-placement="left" title="Excede los 3 días (<?php echo $detalle->DIAS_PENDIENTE; ?> días)"></i>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php
                endforeach;
            else: ?>
                <tr><td colspan="8" class="text-center">No hay datos para mostrar</td></tr>
            <?php endif;
            $html = ob_get_clean();
            break;

        case 'retiroStockCentral':
            $detallePedidosRecibidosNoEntregados = $control->traerDetallePedidosRecibidosNoEntregados();
            ob_start();
            if (!empty($detallePedidosRecibidosNoEntregados)):
                foreach ($detallePedidosRecibidosNoEntregados as $detalle):
                    $excedeDias = $detalle->DIAS_PENDIENTE > 7; ?>
                    <tr class="<?php echo $excedeDias ? 'text-danger' : ''; ?>">
                        <td><?php echo $detalle->FECHA_PEDIDO ? $detalle->FECHA_PEDIDO->format('d/m/Y H:i') : ''; ?></td>
                        <td><?php echo htmlspecialchars($detalle->NRO_PEDIDO); ?></td>
                        <td><?php echo htmlspecialchars($detalle->ORDER_ID); ?></td>
                        <td><?php echo htmlspecialchars($detalle->CLIENTE ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($detalle->SUCURSAL_ENTREGA ?? ''); ?></td>
                        <td><?php echo $detalle->FECHA_RECIBIDO_TIENDA ? $detalle->FECHA_RECIBIDO_TIENDA->format('d/m/Y') : ''; ?></td>
                        <td><?php echo $detalle->DIAS_PENDIENTE; ?></td>
                        <td class="text-end">$<?php echo number_format($detalle->TOTAL_PEDI, 0); ?></td>
                        <td class="text-center">
                            <?php if ($excedeDias): ?>
                                <i class="fas fa-exclamation-circle text-danger" data-bs-toggle="tooltip" data-bs-placement="left" title="Excede los 7 días (<?php echo $detalle->DIAS_PENDIENTE; ?> días)"></i>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php
                endforeach;
            else: ?>
                <tr><td colspan="9" class="text-center">No hay datos para mostrar</td></tr>
            <?php endif;
            $html = ob_get_clean();
            break;

        case 'retiroStockSucursal':
            $detallePedidosRetiroTienda = $control->traerDetallePedidosRetiroTienda();
            ob_start();
            if (!empty($detallePedidosRetiroTienda)):
                foreach ($detallePedidosRetiroTienda as $detalle):
                    $excedeDias = $detalle->DIAS_PENDIENTE > 5; ?>
                    <tr class="<?php echo $excedeDias ? 'text-danger' : ''; ?>">
                        <td><?php echo htmlspecialchars($detalle->SUCURSAL ?? ''); ?></td>
                        <td><?php echo $detalle->FECHA_HORA ? $detalle->FECHA_HORA->format('d/m/Y H:i') : ''; ?></td>
                        <td><?php echo htmlspecialchars($detalle->NRO_PEDIDO); ?></td>
                        <td><?php echo htmlspecialchars($detalle->ORDER_ID_TIENDA ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($detalle->CLIENTE ?? ''); ?></td>
                        <td><?php echo $detalle->DIAS_PENDIENTE; ?></td>
                        <td class="text-end">$<?php echo number_format($detalle->TOTAL_PEDI, 0); ?></td>
                        <td class="text-center">
                            <?php if ($excedeDias): ?>
                                <i class="fas fa-exclamation-circle text-danger" data-bs-toggle="tooltip" data-bs-placement="left" title="Excede los 5 días (<?php echo $detalle->DIAS_PENDIENTE; ?> días)"></i>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php
                endforeach;
            else: ?>
                <tr><td colspan="8" class="text-center">No hay datos para mostrar</td></tr>
            <?php endif;
            $html = ob_get_clean();
            break;

        case 'controlSucursales':
            $detallePedidosControlSucursales = $control->traerDetallePedidosPendientesControlSucursales();
            ob_start();
            if (!empty($detallePedidosControlSucursales)):
                foreach ($detallePedidosControlSucursales as $detalle): ?>
                    <tr>
                        <td><?php echo $detalle->FECHA_SINCRONIZADO->format('d/m/Y H:i'); ?></td>
                        <td><?php echo htmlspecialchars($detalle->CANAL); ?></td>
                        <td><?php echo htmlspecialchars($detalle->NRO_PEDIDO); ?></td>
                        <td><?php echo htmlspecialchars($detalle->ORDER_ID); ?></td>
                        <td><?php echo htmlspecialchars($detalle->CLIENTE); ?></td>
                        <td><?php echo htmlspecialchars($detalle->SUCURSAL_PREPARA); ?></td>
                    </tr>
                    <?php
                endforeach;
            else: ?>
                <tr><td colspan="6" class="text-center">No hay pedidos pendientes de sucursales</td></tr>
            <?php endif;
            $html = ob_get_clean();
            break;

        case 'incompletosSucursales':
            $detalleIncompletos = $control->traerDetallePedidosIncompletosSucursales();
            ob_start();
            if (!empty($detalleIncompletos)):
                foreach ($detalleIncompletos as $detalle): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($detalle->ORIGEN); ?></td>
                        <td><?php echo htmlspecialchars($detalle->NRO_ORDEN_ECOMMERCE); ?></td>
                        <td><?php echo htmlspecialchars($detalle->NRO_PEDIDO); ?></td>
                        <td><?php echo $detalle->FECHA_PEDID->format('d/m/Y'); ?></td>
                        <td><?php echo htmlspecialchars($detalle->CLIENTE); ?></td>
                        <td><?php echo htmlspecialchars($detalle->COD_ARTICU); ?></td>
                        <td><?php echo htmlspecialchars($detalle->DESCRIPCIO); ?></td>
                        <td class="text-end"><?php echo htmlspecialchars($detalle->CANT_PEDID); ?></td>
                        <td class="text-end"><?php echo htmlspecialchars($detalle->CANT_AUDITADO); ?></td>
                        <td><?php echo htmlspecialchars($detalle->DEPOSITO); ?></td>
                        <td><?php echo htmlspecialchars($detalle->METODO_ENVIO); ?></td>
                    </tr>
                    <?php
                endforeach;
            else: ?>
                <tr><td colspan="11" class="text-center">No hay pedidos incompletos</td></tr>
            <?php endif;
            $html = ob_get_clean();
            break;

        // ── Rankings ───────────────────────────────────────────────────────────

        case 'rankingControl':
            $rankingPedidosControl = $control->traerRankingPedidosPendientesControlPorSucursal();
            ob_start();
            if (!empty($rankingPedidosControl)):
                $posicion    = 1;
                $maxCantidad = $rankingPedidosControl[0]->CANTIDAD_PEDIDOS;
                foreach ($rankingPedidosControl as $ranking):
                    $progressWidth = ($ranking->CANTIDAD_PEDIDOS / $maxCantidad) * 100;
                    if ($posicion == 1)      $progressColor = 'bg-danger';
                    elseif ($posicion <= 3)  $progressColor = 'bg-warning';
                    else                     $progressColor = 'bg-success';
                    if ($posicion == 1)      $icono = '<i class="fas fa-crown text-warning"></i>';
                    elseif ($posicion == 2)  $icono = '<i class="fas fa-medal text-secondary"></i>';
                    elseif ($posicion == 3)  $icono = '<i class="fas fa-award text-warning"></i>';
                    else                     $icono = $posicion; ?>
                    <tr>
                        <td class="text-center fw-bold"><?php echo $icono; ?></td>
                        <td class="fw-semibold"><?php echo htmlspecialchars($ranking->SUCURSAL); ?></td>
                        <td class="text-center"><span class="badge bg-primary fs-6"><?php echo number_format($ranking->CANTIDAD_PEDIDOS, 0); ?></span></td>
                        <td class="text-center"><span class="fw-bold"><?php echo number_format($ranking->PORCENTAJE, 1); ?>%</span></td>
                        <td>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar <?php echo $progressColor; ?> progress-bar-striped"
                                     role="progressbar"
                                     style="width: <?php echo $progressWidth; ?>%;"
                                     aria-valuenow="<?php echo $ranking->CANTIDAD_PEDIDOS; ?>"
                                     aria-valuemin="0"
                                     aria-valuemax="<?php echo $maxCantidad; ?>">
                                    <?php if ($progressWidth > 20): echo number_format($ranking->PORCENTAJE, 1); ?>%<?php endif; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php $posicion++;
                endforeach;
            else: ?>
                <tr><td colspan="5" class="text-center">No hay datos para mostrar</td></tr>
            <?php endif;
            $html = ob_get_clean();

            // Stats cards
            if (!empty($rankingPedidosControl)):
                ob_start(); ?>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="card border-primary">
                            <div class="card-body text-center">
                                <h6 class="card-title text-primary">Total General</h6>
                                <h4 class="text-primary"><?php echo array_sum(array_column($rankingPedidosControl, 'CANTIDAD_PEDIDOS')); ?></h4>
                                <small class="text-muted">pedidos pendientes</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-warning">
                            <div class="card-body text-center">
                                <h6 class="card-title text-warning">Sucursales Afectadas</h6>
                                <h4 class="text-warning"><?php echo count($rankingPedidosControl); ?></h4>
                                <small class="text-muted">con pedidos pendientes</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-danger">
                            <div class="card-body text-center">
                                <h6 class="card-title text-danger">Mayor Concentración</h6>
                                <h4 class="text-danger"><?php echo number_format($rankingPedidosControl[0]->PORCENTAJE, 1); ?>%</h4>
                                <small class="text-muted"><?php echo htmlspecialchars($rankingPedidosControl[0]->SUCURSAL); ?></small>
                            </div>
                        </div>
                    </div>
                </div>
                <?php $extra = ob_get_clean();
            endif;
            break;

        case 'rankingControlSucursales':
            $detallePedidosControlSucursales = $control->traerDetallePedidosPendientesControlSucursales();
            $rankingSucursales = [];
            if (!empty($detallePedidosControlSucursales)):
                $contadorPorSucursal = [];
                foreach ($detallePedidosControlSucursales as $detalle):
                    $suc = $detalle->SUCURSAL_PREPARA;
                    $contadorPorSucursal[$suc] = ($contadorPorSucursal[$suc] ?? 0) + 1;
                endforeach;
                foreach ($contadorPorSucursal as $suc => $cant):
                    $rankingSucursales[] = (object)['SUCURSAL' => $suc, 'CANTIDAD_PEDIDOS' => $cant];
                endforeach;
                usort($rankingSucursales, fn($a, $b) => $b->CANTIDAD_PEDIDOS - $a->CANTIDAD_PEDIDOS);
                $totalPedidos = array_sum(array_column($rankingSucursales, 'CANTIDAD_PEDIDOS'));
                foreach ($rankingSucursales as $r):
                    $r->PORCENTAJE = $totalPedidos > 0 ? ($r->CANTIDAD_PEDIDOS / $totalPedidos) * 100 : 0;
                endforeach;
            endif;

            ob_start();
            if (!empty($rankingSucursales)):
                $posicion    = 1;
                $maxCantidad = $rankingSucursales[0]->CANTIDAD_PEDIDOS;
                foreach ($rankingSucursales as $ranking):
                    $progressWidth = ($ranking->CANTIDAD_PEDIDOS / $maxCantidad) * 100;
                    if ($posicion == 1)      $progressColor = 'bg-danger';
                    elseif ($posicion <= 3)  $progressColor = 'bg-warning';
                    else                     $progressColor = 'bg-success';
                    if ($posicion == 1)      $icono = '<i class="fas fa-crown text-warning"></i>';
                    elseif ($posicion == 2)  $icono = '<i class="fas fa-medal text-secondary"></i>';
                    elseif ($posicion == 3)  $icono = '<i class="fas fa-award text-warning"></i>';
                    else                     $icono = $posicion; ?>
                    <tr>
                        <td class="text-center fw-bold"><?php echo $icono; ?></td>
                        <td class="fw-semibold"><?php echo htmlspecialchars($ranking->SUCURSAL); ?></td>
                        <td class="text-center"><span class="badge bg-primary fs-6"><?php echo number_format($ranking->CANTIDAD_PEDIDOS, 0); ?></span></td>
                        <td class="text-center"><span class="fw-bold"><?php echo number_format($ranking->PORCENTAJE, 1); ?>%</span></td>
                        <td>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar <?php echo $progressColor; ?> progress-bar-striped"
                                     role="progressbar"
                                     style="width: <?php echo $progressWidth; ?>%;"
                                     aria-valuenow="<?php echo $ranking->CANTIDAD_PEDIDOS; ?>"
                                     aria-valuemin="0"
                                     aria-valuemax="<?php echo $maxCantidad; ?>">
                                    <?php if ($progressWidth > 20): echo number_format($ranking->PORCENTAJE, 1); ?>%<?php endif; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php $posicion++;
                endforeach;
            else: ?>
                <tr><td colspan="5" class="text-center">No hay datos para mostrar</td></tr>
            <?php endif;
            $html = ob_get_clean();

            // Stats cards
            if (!empty($rankingSucursales)):
                ob_start(); ?>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="card border-primary">
                            <div class="card-body text-center">
                                <h6 class="card-title text-primary">Total Sucursales</h6>
                                <h4 class="text-primary"><?php echo array_sum(array_column($rankingSucursales, 'CANTIDAD_PEDIDOS')); ?></h4>
                                <small class="text-muted">pedidos pendientes</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-warning">
                            <div class="card-body text-center">
                                <h6 class="card-title text-warning">Sucursales Afectadas</h6>
                                <h4 class="text-warning"><?php echo count($rankingSucursales); ?></h4>
                                <small class="text-muted">con pedidos pendientes</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-danger">
                            <div class="card-body text-center">
                                <h6 class="card-title text-danger">Mayor Concentración</h6>
                                <h4 class="text-danger"><?php echo number_format($rankingSucursales[0]->PORCENTAJE, 1); ?>%</h4>
                                <small class="text-muted"><?php echo htmlspecialchars($rankingSucursales[0]->SUCURSAL); ?></small>
                            </div>
                        </div>
                    </div>
                </div>
                <?php $extra = ob_get_clean();
            endif;
            break;

        // ── Uruguay ────────────────────────────────────────────────────────────

        case 'pedidosSinFacturarUy':
            $detallePedidosUruguay = $control->traerDetallePedidosSinFactTiendasUruguay();
            ob_start();
            if (!empty($detallePedidosUruguay)):
                foreach ($detallePedidosUruguay as $detalle): ?>
                    <tr>
                        <td><?php echo $detalle->FECHA_HORA->format('d/m/Y H:i'); ?></td>
                        <td><?php echo htmlspecialchars($detalle->CANAL); ?></td>
                        <td><?php echo htmlspecialchars($detalle->NRO_PEDIDO); ?></td>
                        <td><?php echo htmlspecialchars($detalle->ORDER_ID_TIENDA); ?></td>
                        <td><?php echo htmlspecialchars($detalle->CLIENTE); ?></td>
                        <td class="text-end">$<?php echo number_format($detalle->TOTAL_PEDI, 0); ?></td>
                    </tr>
                    <?php
                endforeach;
            else: ?>
                <tr><td colspan="6" class="text-center">No hay pedidos pendientes</td></tr>
            <?php endif;
            $html = ob_get_clean();
            break;

        case 'facturasSinRemitoUy':
            $detalleFacturasSinRemito = $control->traerDetalleFacturasSinRemitoUruguay();
            $fechaActual = new DateTime();
            ob_start();
            if (!empty($detalleFacturasSinRemito)):
                foreach ($detalleFacturasSinRemito as $detalle):
                    $diasTranscurridos = $fechaActual->diff(clone $detalle->FECHA_FACTURA)->days;
                    $excedeDias = $diasTranscurridos > 10; ?>
                    <tr class="<?php echo $excedeDias ? 'text-danger' : ''; ?>">
                        <td><?php echo htmlspecialchars($detalle->SUCURSAL); ?></td>
                        <td><?php echo $detalle->FECHA_FACTURA->format('d/m/Y'); ?></td>
                        <td><?php echo htmlspecialchars($detalle->FACTURA); ?></td>
                        <td><?php echo htmlspecialchars($detalle->COD_ARTICU); ?></td>
                        <td><?php echo htmlspecialchars($detalle->DESC_CTA_ARTICULO); ?></td>
                        <td class="text-end"><?php echo number_format($detalle->CANTIDAD, 0); ?></td>
                        <td class="text-center">
                            <?php if ($excedeDias): ?>
                                <i class="fas fa-exclamation-circle text-danger" data-bs-toggle="tooltip" data-bs-placement="left" title="Excede los 10 días (<?php echo $diasTranscurridos; ?> días)"></i>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php
                endforeach;
            else: ?>
                <tr><td colspan="7" class="text-center">No hay datos para mostrar</td></tr>
            <?php endif;
            $html = ob_get_clean();
            break;

        case 'ncDevolucionesUy':
            $detalleNcDevolucionesUruguay = $control->traerDetalleNcPendDevolucionesUruguay();
            ob_start();
            if (!empty($detalleNcDevolucionesUruguay)):
                foreach ($detalleNcDevolucionesUruguay as $detalle): ?>
                    <tr>
                        <td><?php echo $detalle->FECHA_PEDI->format('d/m/Y'); ?></td>
                        <td><?php echo htmlspecialchars($detalle->NRO_PEDIDO); ?></td>
                        <td><?php echo htmlspecialchars($detalle->ORDER_ID_TIENDA); ?></td>
                        <td><?php echo htmlspecialchars($detalle->CLIENTE); ?></td>
                        <td><?php echo htmlspecialchars($detalle->COD_SUCURS); ?></td>
                        <td><?php echo htmlspecialchars($detalle->SUCURSAL); ?></td>
                        <td><?php echo htmlspecialchars($detalle->N_COMP); ?></td>
                        <td class="text-end">$<?php echo number_format($detalle->IMPORTE, 0); ?></td>
                    </tr>
                    <?php
                endforeach;
            else: ?>
                <tr><td colspan="8" class="text-center">No hay datos para mostrar</td></tr>
            <?php endif;
            $html = ob_get_clean();
            break;

        case 'ordenesSinIntegrarUy':
            $detalleOrdenesSinIntegrarUruguay = $control->traerDetalleOrdenesSinIntegrarUruguay();
            ob_start();
            if (!empty($detalleOrdenesSinIntegrarUruguay)):
                foreach ($detalleOrdenesSinIntegrarUruguay as $detalle): ?>
                    <tr>
                        <td><?php echo $detalle->FECHA_ORDEN->format('d/m/Y H:i'); ?></td>
                        <td><?php echo htmlspecialchars($detalle->TIENDA); ?></td>
                        <td><?php echo htmlspecialchars($detalle->ORDER_NRO_TIENDA); ?></td>
                        <td class="text-end">$<?php echo number_format($detalle->TOTAL_ORDEN, 2); ?></td>
                    </tr>
                    <?php
                endforeach;
            else: ?>
                <tr><td colspan="4" class="text-center">No hay órdenes sin integrar</td></tr>
            <?php endif;
            $html = ob_get_clean();
            break;

        case 'ordenesCierreUy':
            $detalleOrdenesCierre = $control->traerDetalleOrdenesPendientesCierreUruguay();
            ob_start();
            if (!empty($detalleOrdenesCierre)):
                foreach ($detalleOrdenesCierre as $detalle): ?>
                    <tr>
                        <td><?php echo $detalle->FECHA->format('d/m/Y H:i'); ?></td>
                        <td><?php echo htmlspecialchars($detalle->ORDER_ID); ?></td>
                        <td><?php echo htmlspecialchars($detalle->CLIENTE); ?></td>
                        <td><?php echo htmlspecialchars($detalle->SUCURSAL); ?></td>
                        <td class="text-end"><?php echo htmlspecialchars($detalle->DIAS_ANTIGUEDAD); ?></td>
                    </tr>
                    <?php
                endforeach;
            else: ?>
                <tr><td colspan="5" class="text-center">No hay órdenes pendientes de cierre</td></tr>
            <?php endif;
            $html = ob_get_clean();
            break;

        case 'retiroTiendaUy':
            $detallePedidosRetiro = $control->traerDetallePedidosRetiroTiendaUruguay();
            ob_start();
            if (!empty($detallePedidosRetiro)):
                foreach ($detallePedidosRetiro as $detalle): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($detalle->NOMBRE_SUCURSAL); ?></td>
                        <td><?php echo $detalle->FECHA_HORA->format('d/m/Y H:i'); ?></td>
                        <td><?php echo htmlspecialchars($detalle->NRO_PEDIDO); ?></td>
                        <td><?php echo htmlspecialchars($detalle->ORDER_ID_TIENDA); ?></td>
                        <td><?php echo htmlspecialchars($detalle->CLIENTE); ?></td>
                        <td class="text-end">$<?php echo number_format($detalle->TOTAL_PEDI, 2); ?></td>
                        <td class="text-end"><?php echo htmlspecialchars($detalle->DIAS_PENDIENTE); ?></td>
                    </tr>
                    <?php
                endforeach;
            else: ?>
                <tr><td colspan="7" class="text-center">No hay pedidos pendientes de retiro</td></tr>
            <?php endif;
            $html = ob_get_clean();
            break;

        case 'controlSucursalesUy':
            $detallePedidosControl = $control->traerDetallePedidosPendientesControlSucursalesUruguay();
            ob_start();
            if (!empty($detallePedidosControl)):
                foreach ($detallePedidosControl as $detalle):
                    $badgeClass = $detalle->DIAS_PENDIENTE > 3 ? 'bg-danger' : 'bg-warning text-dark'; ?>
                    <tr>
                        <td><?php echo $detalle->FECHA_FACTURADO->format('d/m/Y H:i'); ?></td>
                        <td><?php echo htmlspecialchars($detalle->CANAL); ?></td>
                        <td><?php echo htmlspecialchars($detalle->NRO_PEDIDO); ?></td>
                        <td><?php echo htmlspecialchars($detalle->ORDER_ID); ?></td>
                        <td><?php echo htmlspecialchars($detalle->FACTURA); ?></td>
                        <td><?php echo htmlspecialchars($detalle->CLIENTE); ?></td>
                        <td><?php echo htmlspecialchars($detalle->NOMBRE_SUCURSAL); ?></td>
                        <td><span class="badge <?php echo $badgeClass; ?>"><?php echo $detalle->DIAS_PENDIENTE; ?> días</span></td>
                    </tr>
                    <?php
                endforeach;
            else: ?>
                <tr><td colspan="8" class="text-center">No hay pedidos pendientes de control</td></tr>
            <?php endif;
            $html = ob_get_clean();
            break;

        default:
            echo json_encode(['success' => false, 'error' => 'Modal no reconocido: ' . htmlspecialchars($modalKey)]);
            exit;
    }

    echo json_encode([
        'success'   => true,
        'html'      => $html,
        'extra'     => $extra,
        'timestamp' => date('d/m/Y H:i:s')
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}


<?php
// Usar NRO_PEDIDO limpio (sin espacios) para la búsqueda
$nroPedido = trim($pedido->NRO_PEDIDO);
$nroOrden = trim($pedido->NRO_ORDEN);

// Obtener devoluciones para el pedido actual
$resumenDevoluciones = $pedidos->obtenerResumenDevoluciones($nroPedido, $nroOrden);
$ncrPendientes = $pedidos->verificarNcrPendiente($nroPedido, $nroOrden);
$infoCancelacion = $pedidos->verificarCancelacion($nroPedido, $nroOrden);

// Solo mostrar la sección si hay devoluciones
if ($resumenDevoluciones->tiene_devoluciones):
?>

<!-- Sección de Devoluciones y Reintegros -->
<div class="card mt-4 border-danger">
    <div class="card-header bg-danger bg-opacity-10">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <i class="fas fa-times-circle me-2"></i>Pedido Cancelado
                <?php if (count($ncrPendientes) > 0): ?>
                    <span class="badge bg-danger ms-2 pulse-badge">
                        <i class="fas fa-exclamation-circle"></i> NCR Pendiente
                    </span>
                <?php elseif ($infoCancelacion && $infoCancelacion->tiene_ncr): ?>
                    <span class="badge bg-success ms-2">
                        <i class="fas fa-check-circle"></i> NCR Emitida
                    </span>
                <?php endif; ?>
            </h5>
            <?php if ($resumenDevoluciones->total_importe > 0): ?>
            <span class="badge bg-warning text-dark fs-6">
                <?php echo $resumenDevoluciones->total_articulos; ?> artículo(s) - 
                $ <?php echo number_format($resumenDevoluciones->total_importe, 2, ',', '.'); ?>
            </span>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <?php if (count($ncrPendientes) > 0): ?>
        <!-- Alerta de NCR Pendientes -->
        <div class="alert alert-danger d-flex align-items-start mb-4">
            <i class="fas fa-exclamation-triangle fs-4 me-3 mt-1"></i>
            <div class="flex-grow-1">
                <h6 class="alert-heading mb-2"><strong>Atención: Pedido Cancelado - NCR Pendiente de Emisión</strong></h6>
                <p class="mb-2">Este pedido ha sido cancelado y el reintegro al cliente ya fue realizado, pero falta emitir la Nota de Crédito (NCR):</p>
                <ul class="mb-0">
                    <?php foreach ($ncrPendientes as $pendiente): ?>
                    <li>
                        <strong><?php echo $pendiente->DESCRIPCIO; ?></strong>
                        <?php if (isset($pendiente->FACTURA) && !empty($pendiente->FACTURA)): ?>
                            - Factura: <span class="badge bg-info"><?php echo $pendiente->FACTURA; ?></span>
                        <?php endif; ?>
                        <?php if (isset($pendiente->FECHA)): ?>
                            <small class="text-muted">(Fecha de cancelación: <?php echo $pendiente->FECHA instanceof DateTime ? $pendiente->FECHA->format('d/m/Y') : $pendiente->FECHA; ?>)</small>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php elseif ($infoCancelacion && $infoCancelacion->tiene_ncr): ?>
        <!-- Información de NCR Emitida -->
        <div class="alert alert-info d-flex align-items-start mb-4">
            <i class="fas fa-info-circle fs-4 me-3 mt-1"></i>
            <div class="flex-grow-1">
                <h6 class="alert-heading mb-2"><strong>Pedido Cancelado - Proceso Completo</strong></h6>
                <p class="mb-2">Este pedido fue cancelado, el reintegro al cliente fue realizado y la Nota de Crédito ya fue emitida:</p>
                <ul class="mb-0">
                    <li>
                        NCR Emitida: <span class="badge bg-success"><?php echo $infoCancelacion->numero_ncr; ?></span>
                        <?php if ($infoCancelacion->factura): ?>
                            - Factura Original: <span class="badge bg-secondary"><?php echo $infoCancelacion->factura; ?></span>
                        <?php endif; ?>
                        <?php if ($infoCancelacion->fecha_ncr): ?>
                            <small class="text-muted">
                                (Fecha NCR: <?php echo $infoCancelacion->fecha_ncr instanceof DateTime ? $infoCancelacion->fecha_ncr->format('d/m/Y') : date('d/m/Y', strtotime($infoCancelacion->fecha_ncr)); ?>)
                            </small>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
        <?php endif; ?>
        
        <?php 
        // Verificar si hay información detallada de artículos o solo estado de pedido
        $tieneDetalleArticulos = false;
        foreach ($resumenDevoluciones->tipos as $tipo => $dataTipo) {
            foreach ($dataTipo->registros as $registro) {
                if ($registro->COD_ARTICU != 'PEDIDO_COMPLETO' && $registro->CANTIDAD > 0 && $registro->IMPORTE > 0) {
                    $tieneDetalleArticulos = true;
                    break 2;
                }
            }
        }
        
        // Solo mostrar tablas si hay información detallada
        if ($tieneDetalleArticulos):
            foreach ($resumenDevoluciones->tipos as $tipo => $dataTipo): 
        ?>
        <div class="devolucion-tipo-section mb-4">
            <div class="d-flex align-items-center mb-3">
                <?php if ($tipo == 'NCR'): ?>
                    <i class="fas fa-file-invoice me-2 text-danger fs-4"></i>
                    <h6 class="mb-0">Nota de Crédito Emitida</h6>
                <?php elseif (strpos($tipo, 'NCR Pendiente') !== false): ?>
                    <i class="fas fa-exclamation-triangle me-2 text-danger fs-4"></i>
                    <h6 class="mb-0"><?php echo $tipo; ?></h6>
                <?php else: ?>
                    <i class="fas fa-money-bill-wave me-2 text-warning fs-4"></i>
                    <h6 class="mb-0"><?php echo $tipo; ?></h6>
                <?php endif; ?>
                <span class="badge bg-secondary ms-3">
                    <?php echo $dataTipo->cantidad; ?> artículo(s)
                </span>
            </div>
            
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 12%">Fecha</th>
                            <th style="width: 13%">Número</th>
                            <th style="width: 35%">Artículo</th>
                            <th style="width: 10%" class="text-end">Cantidad</th>
                            <th style="width: 15%" class="text-end">Precio Unit.</th>
                            <th style="width: 15%" class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dataTipo->registros as $registro): ?>
                        <tr>
                            <td>
                                <?php 
                                echo $registro->FECHA instanceof DateTime ? 
                                    $registro->FECHA->format('d/m/Y') : 
                                    date('d/m/Y', strtotime($registro->FECHA));
                                ?>
                            </td>
                            <td>
                                <?php 
                                $badgeClass = 'bg-secondary';
                                if ($tipo == 'NCR' || $registro->ESTADO == 'NCR_EMITIDA') {
                                    $badgeClass = 'bg-success';
                                } elseif ($registro->ESTADO == 'NCR_PENDIENTE' || $registro->NUMERO == 'PENDIENTE') {
                                    $badgeClass = 'bg-danger';
                                }
                                ?>
                                <span class="badge <?php echo $badgeClass; ?>">
                                    <?php 
                                    if ($registro->NUMERO == 'PENDIENTE') {
                                        echo 'NCR PENDIENTE';
                                    } else {
                                        echo $registro->NUMERO;
                                    }
                                    ?>
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-primary"><?php echo $registro->DESCRIPCIO; ?></div>
                                <span class="text-muted small">Código: <?php echo $registro->COD_ARTICU; ?></span>
                            </td>
                            <td class="text-end">
                                <strong><?php echo $registro->CANTIDAD; ?></strong>
                            </td>
                            <td class="text-end">
                                $ <?php echo number_format($registro->IMPORTE, 2, ',', '.'); ?>
                            </td>
                            <td class="text-end">
                                <strong>$ <?php echo number_format($registro->CANTIDAD * $registro->IMPORTE, 2, ',', '.'); ?></strong>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="table-secondary">
                            <td colspan="5" class="text-end fw-bold">Subtotal <?php echo $tipo; ?>:</td>
                            <td class="text-end fw-bold">
                                $ <?php echo number_format($dataTipo->importe, 2, ',', '.'); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php 
            endforeach; // fin foreach tipos
        ?>
        
        <?php if (count($resumenDevoluciones->tipos) > 1): ?>
        <div class="alert alert-warning mb-0 d-flex justify-content-between align-items-center">
            <strong><i class="fas fa-calculator me-2"></i>Total General de Devoluciones:</strong>
            <span class="fs-5 fw-bold">
                $ <?php echo number_format($resumenDevoluciones->total_importe, 2, ',', '.'); ?>
            </span>
        <?php endif; // fin if tieneDetalleArticulos ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php endif; ?>

<!-- Modal para el detalle de pedidos recibidos pero no entregados -->
<div class="modal fade" id="modalPedidosRecibidosNoEntregados" tabindex="-1" aria-labelledby="modalPedidosRecibidosNoEntregadosLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPedidosRecibidosNoEntregadosLabel">
                    <i class="fas fa-store"></i> Detalle de Pedidos Recibidos en Tienda con stock de central Pendientes de Entrega
                </h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" onclick="exportToExcelPedidosRecibidosNoEntregados()">
                        <i class="fas fa-file-excel me-2"></i>Exportar
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Pedidos de los últimos 45 días que ya fueron recibidos en tienda pero aún no se marcaron como entregados al cliente</strong>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha Pedido</th>
                                <th>Nro. Pedido</th>
                                <th>Order ID</th>
                                <th>Cliente</th>
                                <th>Sucursal Entrega</th>
                                <th>Fecha Recibido</th>
                                <th>Días Pendiente</th>
                                <th class="text-end">Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            try {
                                $detallePedidosRecibidosNoEntregados = $control->traerDetallePedidosRecibidosNoEntregados();
                                $fechaActual = new DateTime();
                                
                                if (!empty($detallePedidosRecibidosNoEntregados)):
                                    foreach ($detallePedidosRecibidosNoEntregados as $detalle):
                                        $diasPendiente = $detalle->DIAS_PENDIENTE;
                                        $excedeDias = $diasPendiente > 7;
                                        ?>
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
                                                    <i class="fas fa-exclamation-circle text-danger" 
                                                    data-bs-toggle="tooltip" 
                                                    data-bs-placement="left"
                                                    title="Excede los 7 días (<?php echo $diasPendiente; ?> días)"></i>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach;
                                else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center">No hay datos para mostrar</td>
                                    </tr>
                                <?php endif;
                            } catch (Exception $e) {
                                ?>
                                <tr>
                                    <td colspan="9" class="text-center text-danger">Error: <?php echo $e->getMessage(); ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
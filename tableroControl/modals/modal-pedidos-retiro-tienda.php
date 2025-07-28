<!-- Modal para el detalle de pedidos de retiro en tienda -->
<div class="modal fade" id="modalPedidosRetiroTienda" tabindex="-1" aria-labelledby="modalPedidosRetiroTiendaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPedidosRetiroTiendaLabel">
                    <i class="fas fa-store-alt"></i> Detalle de Pedidos de Retiro en Tienda con Stock Local
                </h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" onclick="exportToExcelPedidosRetiroTienda()">
                        <i class="fas fa-file-excel me-2"></i>Exportar
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Pedidos de los últimos 45 días para retiro en tienda que aún no han sido entregados al cliente</strong>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Sucursal</th>
                                <th>Fecha Pedido</th>
                                <th>Nro. Pedido</th>
                                <th>Order ID</th>
                                <th>Cliente</th>
                                <th>Días Pendiente</th>
                                <th class="text-end">Total</th>
                                <th></th> <!-- Nueva columna para el ícono -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            try {
                                $detallePedidosRetiroTienda = $control->traerDetallePedidosRetiroTienda();
                                $fechaActual = new DateTime();
                                
                                if (!empty($detallePedidosRetiroTienda)):
                                    foreach ($detallePedidosRetiroTienda as $detalle):
                                        $diasPendiente = $detalle->DIAS_PENDIENTE;
                                        $excedeDias = $diasPendiente > 5; // Considerar exceso después de 5 días para retiro en tienda
                                        ?>
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
                                                    <i class="fas fa-exclamation-circle text-danger" 
                                                    data-bs-toggle="tooltip" 
                                                    data-bs-placement="left"
                                                    title="Excede los 5 días (<?php echo $diasPendiente; ?> días)"></i>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach;
                                else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No hay datos para mostrar</td>
                                    </tr>
                                <?php endif;
                            } catch (Exception $e) {
                                ?>
                                <tr>
                                    <td colspan="8" class="text-center text-danger">Error: <?php echo $e->getMessage(); ?></td>
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

<!-- Modal para el detalle de pedidos despachados pendientes de recepción -->
<div class="modal fade" id="modalPedidosDespachados" tabindex="-1" aria-labelledby="modalPedidosDespachadosLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPedidosDespachadosLabel">
                    <i class="fas fa-truck-loading"></i> Detalle de Pedidos Despachados Pendientes de Recepción
                </h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" onclick="exportToExcelPedidosDespachados()">
                        <i class="fas fa-file-excel me-2"></i>Exportar
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha Pedido</th>
                                <th>Nro. Pedido</th>
                                <th>Order ID</th>
                                <th>Sucursal Entrega</th>
                                <th>Fecha Despachado</th>
                                <th>Días Pendiente</th>
                                <th class="text-end">Total</th>
                                <th></th> <!-- Nueva columna para el ícono -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $detallePedidosDespachados = $control->traerDetallePedidosDespachados();
                            $fechaActual = new DateTime();
                            
                            if (!empty($detallePedidosDespachados)):
                                foreach ($detallePedidosDespachados as $detalle):
                                    $diasPendiente = $detalle->DIAS_PENDIENTE;
                                    $excedeDias = $diasPendiente > 3; // Considerar exceso después de 3 días
                                    ?>
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
                                                <i class="fas fa-exclamation-circle text-danger" 
                                                data-bs-toggle="tooltip" 
                                                data-bs-placement="left"
                                                title="Excede los 3 días (<?php echo $diasPendiente; ?> días)"></i>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach;
                            else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No hay datos para mostrar</td>
                                </tr>
                            <?php endif; ?>
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
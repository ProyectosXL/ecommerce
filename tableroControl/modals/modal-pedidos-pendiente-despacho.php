
    <!-- Modal para el detalle -->
    <div class="modal fade" id="modalPendingDispatch" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h5 class="modal-title">
                        <i class="fas fa-box-open"></i> Detalle de Pedidos Pendientes de Despacho
                    </h5>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-success" onclick="exportToExcelPendingDispatch()">
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
                                    <th>Fecha</th>
                                    <th>Canal</th>
                                    <th>Suc. Entrega</th>
                                    <th>Fecha Despacho</th>
                                    <th>Nro. Pedido</th>
                                    <th>Order ID</th>
                                    <th>Cliente</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $detallePedidos = $control->traerDetallePedidosPendienteDespacho();
                                if (!empty($detallePedidos)):
                                    foreach ($detallePedidos as $detalle): ?>
                                        <tr>
                                            <td><?php echo $detalle->FECHA_SINCRONIZADO->format('d/m/Y H:i'); ?></td>
                                            <td><?php echo htmlspecialchars($detalle->CANAL); ?></td>
                                            <td><?php echo htmlspecialchars($detalle->SUCURSAL_ENTREGA); ?></td>
                                            <td><?php echo $detalle->FECHA_DESPACHO ? $detalle->FECHA_DESPACHO->format('d/m/Y') : ''; ?></td>
                                            <td><?php echo htmlspecialchars($detalle->NRO_PEDIDO); ?></td>
                                            <td><?php echo htmlspecialchars($detalle->ORDER_ID_TIENDA); ?></td>
                                            <td><?php echo htmlspecialchars($detalle->CLIENTE); ?></td>
                                            <td class="text-end">$<?php echo number_format($detalle->TOTAL_PEDI, 0); ?></td>
                                        </tr>
                                    <?php endforeach;
                                else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No hay pedidos pendientes</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
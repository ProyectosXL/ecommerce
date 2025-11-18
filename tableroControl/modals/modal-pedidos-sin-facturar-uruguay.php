
            <!-- Modal para el detalle de Pedidos sin Facturar Uruguay -->
            <div class="modal fade" id="modalPedidosSinFacturarUruguay" tabindex="-1" aria-labelledby="modalPedidosSinFacturarUruguayLabel">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header d-flex justify-content-between align-items-center">
                            <h5 class="modal-title" id="modalPedidosSinFacturarUruguayLabel">
                                <i class="fas fa-file-invoice"></i> Detalle de Pedidos sin Facturar - Uruguay
                            </h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-success" onclick="exportToExcelPedidosUruguay()">
                                    <i class="fas fa-file-excel me-2"></i>Exportar
                                </button>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="tablaPedidosUruguay">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Canal</th>
                                            <th>Nro. Pedido</th>
                                            <th>Order ID</th>
                                            <th>Cliente</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $detallePedidosUruguay = $control->traerDetallePedidosSinFactTiendasUruguay();
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
                                            <?php endforeach;
                                        else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center">No hay pedidos pendientes</td>
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


            <!-- Modal para el detalle de Pedidos Flex -->
            <div class="modal fade" id="modalFlexDetalle" tabindex="-1" aria-labelledby="modalFlexDetalleLabel">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                    <div class="modal-header d-flex justify-content-between align-items-center">
                        <h5 class="modal-title" id="modalFlexDetalleLabel">
                            <i class="fas fa-truck-fast"></i> Detalle de Pedidos Flex Pendientes
                        </h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-success" onclick="exportToExcel()">
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
                                            <th>Fecha Sincronizado</th>
                                            <th>Canal</th>
                                            <th>Nro. Pedido</th>
                                            <th>Order ID</th>
                                            <th>Cliente</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $detalleFlex = $control->traerDetallePedidosFlex();
                                        if (!empty($detalleFlex)):
                                            foreach ($detalleFlex as $detalle): ?>
                                                <tr>
                                                    <td><?php echo $detalle->FECHA_SINCRONIZADO->format('d/m/Y H:i'); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->CANAL); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->NRO_PEDIDO); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->ORDER_ID_TIENDA); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->CLIENTE); ?></td>
                                                    <td class="text-end">$<?php echo number_format($detalle->TOTAL_PEDI, 0); ?></td>
                                                </tr>
                                            <?php endforeach;
                                        else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center">No hay pedidos pendientes</td>
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

            <!-- Modal para el detalle de Pedidos Pendientes de Preparar -->
            <div class="modal fade" id="modalPendientesPreparar" tabindex="-1" aria-labelledby="modalPendientesPrepararLabel">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                    <div class="modal-header d-flex justify-content-between align-items-center">
                        <h5 class="modal-title" id="modalPendientesPrepararLabel">
                            <i class="fas fa-clipboard-list"></i> Detalle de Pedidos Pendientes de Preparar
                        </h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-success" onclick="exportToExcelPendientesPreparar()">
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
                                            <th>Fecha Sincronizado</th>
                                            <th>Canal</th>
                                            <th>Nro. Pedido</th>
                                            <th>Order ID</th>
                                            <th>Cliente</th>
                                            <th>Estado Asignación</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $detallePendientesPreparar = $control->traerDetallePedidosPendientesPreparar();
                                        if (!empty($detallePendientesPreparar)):
                                            foreach ($detallePendientesPreparar as $detalle): ?>
                                                <tr>
                                                    <td><?php echo $detalle->FECHA_PEDI->format('d/m/Y H:i'); ?></td>
                                                    <td><?php echo $detalle->FECHA_SINCRONIZADO ? $detalle->FECHA_SINCRONIZADO->format('d/m/Y H:i') : 'N/A'; ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->CANAL); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->NRO_PEDIDO); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->ORDER_ID_TIENDA); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->CLIENTE); ?></td>
                                                    <td>
                                                        <span class="badge bg-warning">Pendiente Asignación</span>
                                                    </td>
                                                    <td class="text-end">$<?php echo number_format($detalle->TOTAL_PEDI, 0); ?></td>
                                                </tr>
                                            <?php endforeach;
                                        else: ?>
                                            <tr>
                                                <td colspan="8" class="text-center">No hay pedidos pendientes de preparar</td>
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


            <!-- Modal para el gráfico -->
            <div class="modal fade" id="modalNcr" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-chart-bar"></i> Notas de Crédito por Día
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <canvas id="ncrChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal para ver el detalle -->
            <div class="modal fade" id="modalNcDevolucionesDetalle" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header d-flex justify-content-between align-items-center">
                            <h5 class="modal-title">
                                <i class="fas fa-undo"></i> Detalle de NC Pendientes por Devoluciones
                            </h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-success" onclick="exportToExcelNcDevoluciones()">
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
                                            <th>Nro. Pedido</th>
                                            <th>Order ID</th>
                                            <th>Cliente</th>
                                            <th>Deposito</th>
                                            <th>Comprobante</th>
                                            <th class="text-end">Importe</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $detalleNcDevoluciones = $control->traerDetalleNcPendDevoluciones();
                                        if (!empty($detalleNcDevoluciones)):
                                            foreach ($detalleNcDevoluciones as $detalle): ?>
                                                <tr>
                                                    <td><?php echo $detalle->FECHA_PEDI->format('d/m/Y'); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->NRO_PEDIDO); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->ORDER_ID_TIENDA); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->CLIENTE); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->COD_SUCURS); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->N_COMP); ?></td>
                                                    <td class="text-end">$<?php echo number_format($detalle->IMPORTE, 0); ?></td>
                                                </tr>
                                            <?php endforeach;
                                        else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center">No hay datos para mostrar</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

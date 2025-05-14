
            <!-- Modal para el detalle -->
            <div class="modal fade" id="modalOrdenesSinIntegrar" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header d-flex justify-content-between align-items-center">
                            <h5 class="modal-title">
                                <i class="fas fa-exclamation-triangle"></i> Detalle de Órdenes sin Integrar
                            </h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-success" onclick="exportToExcelOrdenes()">
                                    <i class="fas fa-file-excel me-2"></i>Exportar a Excel
                                </button>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha Orden</th>
                                            <th>Tienda</th>
                                            <th>Nro. Orden</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $detalleOrdenes = $control->traerDetalleOrdenesSinIntegrar();
                                        if (!empty($detalleOrdenes)):
                                            foreach ($detalleOrdenes as $detalle): ?>
                                                <tr>
                                                    <td><?php echo $detalle->FECHA_ORDEN->format('d/m/Y H:i'); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->TIENDA); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->ORDER_NRO_TIENDA); ?></td>
                                                    <td class="text-end">$<?php echo number_format($detalle->TOTAL_ORDEN, 0); ?></td>
                                                </tr>
                                            <?php endforeach;
                                        else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center">No hay datos para mostrar</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
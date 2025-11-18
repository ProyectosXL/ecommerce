
            <!-- Modal para el detalle de NC Pendientes por Devoluciones Uruguay -->
            <div class="modal fade" id="modalNcDevolucionesUruguay" tabindex="-1" aria-labelledby="modalNcDevolucionesUruguayLabel">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header d-flex justify-content-between align-items-center">
                            <h5 class="modal-title" id="modalNcDevolucionesUruguayLabel">
                                <i class="fas fa-undo"></i> Detalle de NC Pendientes por Devoluciones - Uruguay
                            </h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-success" onclick="exportToExcelNcDevolucionesUruguay()">
                                    <i class="fas fa-file-excel me-2"></i>Exportar
                                </button>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="tablaNcDevolucionesUruguay">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Nro. Pedido</th>
                                            <th>Order ID</th>
                                            <th>Cliente</th>
                                            <th>Depósito</th>
                                            <th>Sucursal</th>
                                            <th>Comprobante</th>
                                            <th class="text-end">Importe</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $detalleNcDevolucionesUruguay = $control->traerDetalleNcPendDevolucionesUruguay();
                                        if (!empty($detalleNcDevolucionesUruguay)):
                                            foreach ($detalleNcDevolucionesUruguay as $detalle): ?>
                                                <tr>
                                                    <td><?php echo $detalle->FECHA_PEDI->format('d/m/Y'); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->NRO_PEDIDO); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->ORDER_ID_TIENDA); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->CLIENTE); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->COD_SUCURS); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->SUCURSAL); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->N_COMP); ?></td>
                                                    <td class="text-end">$<?php echo number_format($detalle->IMPORTE, 0); ?></td>
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


            <!-- Modal para el detalle de Facturas sin Remito Uruguay -->
            <div class="modal fade" id="modalFacturasSinRemitoUruguay" tabindex="-1" aria-labelledby="modalFacturasSinRemitoUruguayLabel">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header d-flex justify-content-between align-items-center">
                            <h5 class="modal-title" id="modalFacturasSinRemitoUruguayLabel">
                                <i class="fas fa-file-invoice-dollar"></i> Detalle de Facturas sin Remito - Uruguay
                            </h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-success" onclick="exportToExcelFacturasSinRemitoUruguay()">
                                    <i class="fas fa-file-excel me-2"></i>Exportar
                                </button>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="tablaFacturasSinRemitoUruguay">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha Factura</th>
                                            <th>Canal</th>
                                            <th>Nro. Pedido</th>
                                            <th>Order ID</th>
                                            <th>Factura</th>
                                            <th>Cliente</th>
                                            <th>Días Pendiente</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $detalleFacturasSinRemito = $control->traerDetalleFacturasSinRemitoUruguay();
                                        if (!empty($detalleFacturasSinRemito)):
                                            foreach ($detalleFacturasSinRemito as $detalle): ?>
                                                <tr>
                                                    <td><?php echo $detalle->FECHA_FACTURA->format('d/m/Y'); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->CANAL); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->NRO_PEDIDO); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->ORDER_ID_TIENDA); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->FACTURA); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->CLIENTE); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->DIAS_PENDIENTE); ?></td>
                                                    <td class="text-end">$<?php echo number_format($detalle->TOTAL_PEDI, 0); ?></td>
                                                </tr>
                                            <?php endforeach;
                                        else: ?>
                                            <tr>
                                                <td colspan="8" class="text-center">No hay facturas pendientes</td>
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

            <script>
            function exportToExcelFacturasSinRemitoUruguay() {
                const table = document.getElementById('tablaFacturasSinRemitoUruguay');
                const wb = XLSX.utils.table_to_book(table, {sheet: "Facturas sin Remito Uruguay"});
                XLSX.writeFile(wb, 'facturas_sin_remito_uruguay.xlsx');
            }
            </script>

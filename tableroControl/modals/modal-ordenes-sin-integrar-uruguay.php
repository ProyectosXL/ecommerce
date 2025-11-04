
            <!-- Modal para el detalle de Órdenes sin Integrar Uruguay -->
            <div class="modal fade" id="modalOrdenesSinIntegrarUruguay" tabindex="-1" aria-labelledby="modalOrdenesSinIntegrarUruguayLabel">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header d-flex justify-content-between align-items-center">
                            <h5 class="modal-title" id="modalOrdenesSinIntegrarUruguayLabel">
                                <i class="fas fa-shopping-cart"></i> Detalle de Órdenes sin Integrar en Tango - Uruguay
                            </h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-success" onclick="exportToExcelOrdenesSinIntegrarUruguay()">
                                    <i class="fas fa-file-excel me-2"></i>Exportar
                                </button>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="tablaOrdenesSinIntegrarUruguay">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha Orden</th>
                                            <th>Tienda</th>
                                            <th>Order Nro.</th>
                                            <th class="text-end">Total Orden</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $detalleOrdenesSinIntegrarUruguay = $control->traerDetalleOrdenesSinIntegrarUruguay();
                                        if (!empty($detalleOrdenesSinIntegrarUruguay)):
                                            foreach ($detalleOrdenesSinIntegrarUruguay as $detalle): ?>
                                                <tr>
                                                    <td><?php echo $detalle->FECHA_ORDEN->format('d/m/Y H:i'); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->TIENDA); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->ORDER_NRO_TIENDA); ?></td>
                                                    <td class="text-end">$<?php echo number_format($detalle->TOTAL_ORDEN, 2); ?></td>
                                                </tr>
                                            <?php endforeach;
                                        else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center">No hay órdenes sin integrar</td>
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
            function exportToExcelOrdenesSinIntegrarUruguay() {
                const table = document.getElementById('tablaOrdenesSinIntegrarUruguay');
                const wb = XLSX.utils.table_to_book(table, {sheet: "Ordenes sin Integrar Uruguay"});
                XLSX.writeFile(wb, 'ordenes_sin_integrar_uruguay.xlsx');
            }
            </script>

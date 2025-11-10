
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
                                            <th>Sucursal</th>
                                            <th>Fecha</th>
                                            <th>Factura</th>
                                            <th>Código</th>
                                            <th>Descripción</th>
                                            <th class="text-end">Cantidad</th>
                                            <th></th> <!-- Nueva columna para el ícono -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $detalleFacturasSinRemito = $control->traerDetalleFacturasSinRemitoUruguay();
                                        $fechaActual = new DateTime();
                                        
                                        if (!empty($detalleFacturasSinRemito)):
                                            foreach ($detalleFacturasSinRemito as $detalle):
                                                $fechaFactura = clone $detalle->FECHA_FACTURA;
                                                $diasTranscurridos = $fechaActual->diff($fechaFactura)->days;
                                                $excedeDias = $diasTranscurridos > 10;
                                                ?>
                                                <tr class="<?php echo $excedeDias ? 'text-danger' : ''; ?>">
                                                    <td><?php echo htmlspecialchars($detalle->SUCURSAL); ?></td>
                                                    <td><?php echo $detalle->FECHA_FACTURA->format('d/m/Y'); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->FACTURA); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->COD_ARTICU); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->DESC_CTA_ARTICULO); ?></td>
                                                    <td class="text-end"><?php echo number_format($detalle->CANTIDAD, 0); ?></td>
                                                    <td class="text-center">
                                                        <?php if ($excedeDias): ?>
                                                            <i class="fas fa-exclamation-circle text-danger" 
                                                            data-bs-toggle="tooltip" 
                                                            data-bs-placement="left"
                                                            title="Excede los 10 días (<?php echo $diasTranscurridos; ?> días)"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach;
                                        else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center">No hay datos para mostrar</td>
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

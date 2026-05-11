
            <!-- Modal para el detalle de Facturas sin Remito Uruguay -->
            <div class="modal fade" id="modalFacturasSinRemitoUruguay" tabindex="-1" aria-labelledby="modalFacturasSinRemitoUruguayLabel" data-modal-loader="facturasSinRemitoUy">
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
                                    <tbody class="modal-lazy-tbody">
                                        <tr><td colspan="7" class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-lazy-extra"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>

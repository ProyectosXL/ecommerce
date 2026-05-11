
            <!-- Modal para el detalle -->
            <div class="modal fade" id="modalOrdenesSinIntegrar" tabindex="-1" data-modal-loader="ordenesSinIntegrar">
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
                                    <tbody class="modal-lazy-tbody">
                                        <tr><td colspan="4" class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-lazy-extra"></div>
                        </div>
                    </div>
                </div>
            </div>

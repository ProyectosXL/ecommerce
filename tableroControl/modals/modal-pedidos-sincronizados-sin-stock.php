
            <!-- Modal para el detalle de Pedidos Sincronizados sin Stock - Central -->
            <div class="modal fade" id="modalSincronizadosSinStock" tabindex="-1" aria-labelledby="modalSincronizadosSinStockLabel" data-modal-loader="sincronizadosSinStock">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header d-flex justify-content-between align-items-center">
                            <h5 class="modal-title" id="modalSincronizadosSinStockLabel">
                                <i class="fas fa-box-open"></i> Detalle de Pedidos Sincronizados sin Stock - Central
                            </h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-success" onclick="exportToExcelSincronizadosSinStock()">
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
                                            <th class="text-end">Días</th>
                                            <th>Canal</th>
                                            <th>Nro. Pedido</th>
                                            <th>Order ID</th>
                                            <th>Cliente</th>
                                            <th class="text-end">Art. sin Stock</th>
                                            <th class="text-end">Total</th>
                                            <th class="text-center"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="modal-lazy-tbody">
                                        <tr><td colspan="9" class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></td></tr>
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

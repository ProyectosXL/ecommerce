
            <!-- Modal para el detalle de Pedidos Incompletos Central -->
            <div class="modal fade" id="modalPedidosIncompletosCentral" tabindex="-1" aria-labelledby="modalPedidosIncompletosCentralLabel" data-modal-loader="incompletosCentral">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                    <div class="modal-header d-flex justify-content-between align-items-center">
                        <h5 class="modal-title" id="modalPedidosIncompletosCentralLabel">
                            <i class="fas fa-exclamation-triangle"></i> Detalle de Pedidos Incompletos - Central
                        </h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-success" onclick="exportToExcelPedidosIncompletosCentral()">
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
                                            <th>Origen</th>
                                            <th>Nro. Orden Ecommerce</th>
                                            <th>Nro. Pedido</th>
                                            <th>Fecha Pedido</th>
                                            <th>Cliente</th>
                                            <th>Código Artículo</th>
                                            <th>Descripción</th>
                                            <th class="text-end">Cant. Pedida</th>
                                            <th class="text-end">Cant. Auditada</th>
                                            <th>Método Envío</th>
                                            <th>Tienda</th>
                                        </tr>
                                    </thead>
                                    <tbody class="modal-lazy-tbody">
                                        <tr><td colspan="11" class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></td></tr>
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

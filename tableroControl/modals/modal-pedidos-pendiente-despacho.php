
    <!-- Modal para el detalle -->
    <div class="modal fade" id="modalPendingDispatch" tabindex="-1" data-modal-loader="despachoNormal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h5 class="modal-title">
                        <i class="fas fa-box-open"></i> Detalle de Pedidos Pendientes de Despacho
                    </h5>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-success" onclick="exportToExcelPendingDispatch()">
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
                                    <th>Canal</th>
                                    <th>Suc. Entrega</th>
                                    <th>Fecha Despacho</th>
                                    <th>Nro. Pedido</th>
                                    <th>Order ID</th>
                                    <th>Cliente</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody class="modal-lazy-tbody">
                                <tr><td colspan="8" class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-lazy-extra"></div>
                </div>
            </div>
        </div>
    </div>

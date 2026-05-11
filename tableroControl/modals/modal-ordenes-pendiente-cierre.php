
    <!-- Modal para el detalle de Ordenes Pend. Cierre -->
    <div class="modal fade" id="modalOrdenesPendientesCierre" tabindex="-1" data-modal-loader="ordenesCierreVtex">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h5 class="modal-title">
                        <i class="fas fa-hourglass-half"></i> Detalle de Órdenes Pendientes de Cierre
                    </h5>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-success" onclick="exportToExcelOrdenesCierre()">
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
                                    <th>Order ID</th>
                                    <th>Cliente</th>
                                    <th>Sucursal</th>
                                    <th class="text-end">Días de Antigüedad</th>
                                    <th></th> <!-- Nueva columna para el ícono -->
                                </tr>
                            </thead>
                            <tbody class="modal-lazy-tbody">
                                <tr><td colspan="6" class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-lazy-extra"></div>
                </div>
            </div>
        </div>
    </div>

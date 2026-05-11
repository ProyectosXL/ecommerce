
<!-- Modal para el detalle de Órdenes Pendientes de Cierre - Uruguay -->
<div class="modal fade" id="modalOrdenesPendientesCierreUruguay" tabindex="-1" aria-labelledby="modalOrdenesPendientesCierreUruguayLabel" data-modal-loader="ordenesCierreUy">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title" id="modalOrdenesPendientesCierreUruguayLabel">
                    <i class="fas fa-hourglass-half"></i> Detalle de Órdenes Vtex Pendientes de Cierre - Uruguay
                </h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" onclick="exportToExcelOrdenesPendientesCierreUruguay()">
                        <i class="fas fa-file-excel me-2"></i>Exportar
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="tableOrdenesPendientesCierreUruguay" class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha Order</th>
                                <th>Order ID</th>
                                <th>Cliente</th>
                                <th>Sucursal</th>
                                <th class="text-end">Días Antigüedad</th>
                            </tr>
                        </thead>
                        <tbody class="modal-lazy-tbody">
                            <tr><td colspan="5" class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></td></tr>
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

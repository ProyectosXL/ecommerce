<!-- Modal para el ranking de Pedidos Pendientes de Control Sucursales -->
<div class="modal fade" id="modalRankingPedidosControlSucursales" tabindex="-1" aria-labelledby="modalRankingPedidosControlSucursalesLabel" aria-hidden="true" data-modal-loader="rankingControlSucursales">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRankingPedidosControlSucursalesLabel">
                    <i class="fas fa-trophy"></i> Ranking de Pedidos Pendientes de Control por Sucursal (Hasta Ayer)
                </h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" onclick="exportToExcelRankingControlSucursales()">
                        <i class="fas fa-file-excel me-2"></i>Exportar
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Ranking basado en pedidos sin controlar hasta ayer - Solo Sucursales (excluye Central)</strong>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="tablaRankingControlSucursales">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center" style="width: 80px;">#</th>
                                <th>Sucursal</th>
                                <th class="text-center" style="width: 120px;">Cantidad</th>
                                <th class="text-center" style="width: 100px;">%</th>
                                <th style="width: 200px;">Progreso</th>
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

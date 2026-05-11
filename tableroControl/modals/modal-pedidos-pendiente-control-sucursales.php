<!-- Modal para el detalle de Pedidos Pendientes de Control Sucursales -->
<div class="modal fade" id="modalPedidosPendientesControlSucursales" tabindex="-1" data-modal-loader="controlSucursales">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title">
                    <i class="fas fa-search"></i> Detalle de Pedidos Pendientes de Control Sucursales (Hasta Ayer)
                </h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" onclick="exportToExcelPedidosControlSucursales()">
                        <i class="fas fa-file-excel me-2"></i>Exportar
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="tablaPedidosControlSucursales">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha Sincronizado</th>
                                <th>Canal</th>
                                <th>Nro. Pedido</th>
                                <th>Order ID</th>
                                <th>Cliente</th>
                                <th>Sucursal Prepara</th>
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

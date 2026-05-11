
<!-- Modal para el detalle de Pedidos Pendientes de Retiro - Uruguay -->
<div class="modal fade" id="modalPedidosRetiroTiendaUruguay" tabindex="-1" aria-labelledby="modalPedidosRetiroTiendaUruguayLabel" data-modal-loader="retiroTiendaUy">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title" id="modalPedidosRetiroTiendaUruguayLabel">
                    <i class="fas fa-store-alt"></i> Detalle de Pedidos Pendientes de Retiro - Uruguay
                </h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" onclick="exportToExcelPedidosRetiroTiendaUruguay()">
                        <i class="fas fa-file-excel me-2"></i>Exportar
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="tablePedidosRetiroTiendaUruguay" class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Sucursal</th>
                                <th>Fecha Pedido</th>
                                <th>Nro. Pedido</th>
                                <th>Order ID</th>
                                <th>Cliente</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">Días Pendiente</th>
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

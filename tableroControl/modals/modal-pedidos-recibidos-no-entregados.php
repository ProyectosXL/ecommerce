<!-- Modal para el detalle de pedidos recibidos pero no entregados -->
<div class="modal fade" id="modalPedidosRecibidosNoEntregados" tabindex="-1" aria-labelledby="modalPedidosRecibidosNoEntregadosLabel" aria-hidden="true" data-modal-loader="retiroStockCentral">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPedidosRecibidosNoEntregadosLabel">
                    <i class="fas fa-store"></i> Detalle de Pedidos Recibidos en Tienda con stock de central Pendientes de Entrega
                </h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" onclick="exportToExcelPedidosRecibidosNoEntregados()">
                        <i class="fas fa-file-excel me-2"></i>Exportar
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Pedidos de los últimos 45 días que ya fueron recibidos en tienda pero aún no se marcaron como entregados al cliente</strong>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha Pedido</th>
                                <th>Nro. Pedido</th>
                                <th>Order ID</th>
                                <th>Cliente</th>
                                <th>Sucursal Entrega</th>
                                <th>Fecha Recibido</th>
                                <th>Días Pendiente</th>
                                <th class="text-end">Total</th>
                                <th></th>
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

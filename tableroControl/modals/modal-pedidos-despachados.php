
<!-- Modal para el detalle de pedidos despachados pendientes de recepción -->
<div class="modal fade" id="modalPedidosDespachados" tabindex="-1" aria-labelledby="modalPedidosDespachadosLabel" aria-hidden="true" data-modal-loader="pedidosDespachados">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPedidosDespachadosLabel">
                    <i class="fas fa-truck-loading"></i> Detalle de Pedidos Despachados Pendientes de Recepción
                </h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" onclick="exportToExcelPedidosDespachados()">
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
                                <th>Fecha Pedido</th>
                                <th>Nro. Pedido</th>
                                <th>Order ID</th>
                                <th>Sucursal Entrega</th>
                                <th>Fecha Despachado</th>
                                <th>Días Pendiente</th>
                                <th class="text-end">Total</th>
                                <th></th> <!-- Nueva columna para el ícono -->
                            </tr>
                        </thead>
                        <tbody class="modal-lazy-tbody">
                            <tr><td colspan="8" class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></td></tr>
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

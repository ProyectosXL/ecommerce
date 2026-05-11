
<!-- Modal para el detalle de facturas -->
<div class="modal fade" id="modalFacturasDetalle" tabindex="-1" aria-labelledby="modalFacturasDetalleLabel" data-modal-loader="facturasSinRemito">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h5 class="modal-title" id="modalFacturasDetalleLabel">
                        <i class="fas fa-file-invoice-dollar"></i> Detalle de Facturas sin Remito
                    </h5>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-success" onclick="exportToExcelFacturas()">
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
                                    <th>Sucursal</th>
                                    <th>Fecha</th>
                                    <th>Factura</th>
                                    <th>Código</th>
                                    <th>Descripción</th>
                                    <th class="text-end">Cantidad</th>
                                    <th></th> <!-- Nueva columna para el ícono -->
                                </tr>
                            </thead>
                            <tbody class="modal-lazy-tbody">
                                <tr><td colspan="7" class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-lazy-extra"></div>
                </div>
            </div>
        </div>
    </div>

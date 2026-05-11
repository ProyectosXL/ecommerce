<div class="modal fade" id="modalRemitosSinIntegrar" tabindex="-1" aria-labelledby="modalRemitosSinIntegrarLabel" aria-hidden="true" data-modal-loader="remitosSinIngresar">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title">
                    <i class="fas fa-file-invoice"></i> Remitos Abastecimiento Sin Ingresar
                </h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" onclick="exportarRemitosSinIntegrar()">
                        <i class="fas fa-file-excel me-2"></i>Exportar
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="tablaRemitosSinIntegrar">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Canal</th>
                                <th>N° Comprobante</th>
                                <th class="text-end">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody class="modal-lazy-tbody">
                            <tr><td colspan="4" class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></td></tr>
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

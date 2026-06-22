
            <!-- Modal para el historial de NC Promociones procesadas -->
            <div class="modal fade" id="modalHistorialNcPromo" tabindex="-1" data-modal-loader="ncPromoHistorial">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header d-flex justify-content-between align-items-center">
                            <h5 class="modal-title">
                                <i class="fas fa-clock-rotate-left"></i> Historial de NC Procesadas
                            </h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary" id="btnVolverNcPromo">
                                    <i class="fas fa-arrow-left me-2"></i>Volver
                                </button>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted small mb-2">Agrupadas por número de comprobante. Hacé click en una fila para ver el detalle.</p>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:40px"></th>
                                            <th>Nº Comprobante</th>
                                            <th class="text-center">Cantidad</th>
                                            <th class="text-end">Importe total</th>
                                            <th>Registrado</th>
                                        </tr>
                                    </thead>
                                    <tbody class="modal-lazy-tbody">
                                        <tr><td colspan="5" class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-lazy-extra"></div>
                        </div>
                    </div>
                </div>
            </div>

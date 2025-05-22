
<!-- Modal Historial -->
<div class="modal fade" id="historialModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center">
                    <i class="fas fa-clipboard me-2"></i>
                    <h5 class="modal-title mb-0">Historial de Reclamo</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <?php $historial = $pedidos->traerHistorialReclamo(trim($pedido->NRO_PEDIDO)); ?>

            <!-- Estado del Reclamo -->
            <div class="status-bar p-3 border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <span class="me-2">Estado del Reclamo:</span>
                        <span class="badge estado-actual" id="estado"></span>
                    </div>
                    <button class="btn btn-outline-success btn-sm" id="btnResolucion">
                        <i class="fas fa-check me-1"></i>Marcar como Resuelto
                    </button>
                </div>
                
                <!-- Sección de Resolución (inicialmente oculta) -->
                <div id="seccionResolucion" class="mt-3" style="display:none">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Resolución</label>
                            <select class="form-select" id="tipoResolucion">
                                <option value="">Seleccione...</option>
                                <option value="cambio" <?= (isset($historial[0]) && $historial[0]['RESOLUCION'] == 'cambio') ? 'selected' : '' ?>>Cambio</option>
                                <option value="cancelado" <?= (isset($historial[0]) && $historial[0]['RESOLUCION'] == 'cancelado') ? 'selected' : '' ?>>Cancelado</option>
                                <option value="completado" <?= (isset($historial[0]) && $historial[0]['RESOLUCION'] == 'completado') ? 'selected' : '' ?>>Completado</option>
                            </select>
                        </div>
                        <div class="col-md-4" id="seccionSucursal" style="display: none;">
                            <div id="sucursalSeleccionada" hidden> <?= (isset($historial[0])) ? $historial[0]['SUC_DESPACHO'] : '' ?></div>
                            <label class="form-label">Sucursal</label>
                            <select class="form-select" id="selectSucursal"></select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12" id="seccionArticulo" style="display: none;">
                            <label class="form-label">Artículo</label>
                            <div id="articuloCambioCod" hidden><?= (isset($historial[0])) ? $historial[0]['COD_ARTICULO_CAMBIO'] : '' ?></div>
                            <select id="selectArticulo" class="form-select"></select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-body">
                <div class="article-details mb-4 border-bottom pb-3">
                    <div class="row">
                        <div class="col-6">
                            <h6 class="mb-2">Artículo</h6>
                            <p id="modalArticulo" class="mb-1"></p>
                            <small id="modalCodigo" class="text-muted"></small>
                        </div>
                        <div class="col-3">
                            <h6 class="mb-2">Precio</h6>
                            <p id="modalPrecio"></p>
                        </div>
                        <div class="col-3">
                            <h6 class="mb-2">Cantidad</h6>
                            <p id="modalCantidad"></p>
                        </div>
                    </div>
                </div>
                
                <?php 
                if(count($detalleReclamo) != 0){
                    foreach ($detalleReclamo as $comentario) {
                        echo '<div class="seccion-historial border-start border-4 border-primary ps-3 mt-4 seccion-guardada">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label"><i class="fas fa-comments me-2"></i>Tipo de Contacto</label>
                                        <select class="form-select tipo-contacto">
                                            <option value="mail">'.$comentario[0]->TIPO_CONTACTO.'</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><i class="fas fa-user me-2"></i>Agente</label>
                                        <select class="form-select agente">
                                            <option value="at">'.$comentario[0]->AGENTE.'</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label"><i class="fas fa-comment me-2"></i>Comentario</label>
                                        <textarea class="form-control comentario" rows="4">'.$comentario[0]->COMENTARIOS.'</textarea>
                                    </div>
                                </div>
                            </div>';
                    }
                }
                ?>
                
                <div id="seccionesHistorial">
                    <div class="seccion-historial">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fas fa-comments me-2"></i>Tipo de Contacto
                                </label>
                                <select class="form-select tipo-contacto">
                                    <option value="mail">Mail</option>
                                    <option value="whatsapp">WhatsApp</option>
                                    <option value="facebook">Facebook</option>
                                    <option value="instagram">Instagram</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fas fa-user me-2"></i>Agente
                                </label>
                                <select class="form-select agente">
                                    <option value="at">Agustina Taboada</option>
                                    <option value="fc">Florencia Consoli</option>
                                    <option value="jd">Julieta Dalmeida</option>
                                    <option value="ls">Leonel Segovia</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">
                                    <i class="fas fa-comment me-2"></i>Comentario
                                </label>
                                <textarea class="form-control comentario" rows="4" placeholder="Ingrese su comentario aquí..."></textarea>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <small class="text-muted">
                                <i class="far fa-clock me-1"></i>Creado: <span class="fecha-creacion"></span>
                            </small>
                            <button type="button" class="btn btn-primary btn-guardar-seccion" onclick="guardarComentario(this)">
                                <i class="fas fa-save me-1"></i>Guardar Sección
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <div id="botonesNormales" class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="agregarSeccion">
                        <i class="fas fa-plus"></i> Agregar seguimiento
                    </button>
                </div>
                <div id="botonFinalizar" style="display: none;margin-top:20px">
                    <button type="button" class="btn btn-success" style="margin-top:10px" id="finalizarReclamo">
                        <i class="fas fa-check-circle me-1"></i>Finalizar Reclamo
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
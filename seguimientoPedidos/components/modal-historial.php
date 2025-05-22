
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
                        <span class="badge estado-actual" id="estado">
                            <?php 
                            // Prioridad 1: Si existe historial y está resuelto -> Finalizado
                            if (isset($historial[0]) && $historial[0]['ESTADO'] == 'resuelto') {
                                echo '<span class="badge bg-success">Finalizado</span>';
                            }
                            // Prioridad 2: Si tiene comentarios pero no está resuelto -> En Curso  
                            elseif (count($detalleReclamo) > 0) {
                                echo '<span class="badge bg-warning">En Curso</span>';
                            }
                            // Prioridad 3: Sin comentarios ni resolución -> Abierto
                            else {
                                echo '<span class="badge bg-danger">Abierto</span>';
                            }
                            ?>
                        </span>
                    </div>
                    <?php if (!isset($historial[0]) || $historial[0]['ESTADO'] != 'resuelto'): ?>
                    <button class="btn btn-outline-success btn-sm" id="btnResolucion">
                        <i class="fas fa-check me-1"></i>Marcar como Resuelto
                    </button>
                    <?php endif; ?>
                </div>
                
                <!-- Mostrar datos del reclamo completado -->
                <?php if (isset($historial[0]) && $historial[0]['ESTADO'] == 'resuelto'): ?>
                <div class="mt-3 p-3 bg-light border-start border-4 border-success">
                    <h6 class="mb-3"><i class="fas fa-check-circle text-success me-2"></i>Reclamo Finalizado</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Resolución:</strong><br>
                            <span class="text-capitalize"><?php echo $historial[0]['RESOLUCION'] ?? 'N/A'; ?></span>
                        </div>
                        <div class="col-md-4">
                            <strong>Sucursal:</strong><br>
                            <?php echo $historial[0]['SUC_DESPACHO'] ?? 'N/A'; ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Artículo:</strong><br>
                            <div class="fw-bold">
                                <?php echo $historial[0]['DESCRIPCION'] ?? 'N/A'; ?>
                            </div>
                            <small class="text-muted">
                                Código: <?php echo $historial[0]['COD_ARTICULO_CAMBIO'] ?? 'N/A'; ?>
                            </small>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
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
                                        <select class="form-select tipo-contacto" disabled>
                                            <option value="'.$comentario[0]->TIPO_CONTACTO.'" selected>'.$comentario[0]->TIPO_CONTACTO.'</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><i class="fas fa-user me-2"></i>Agente</label>
                                        <select class="form-select agente" disabled>
                                            <option value="'.$comentario[0]->AGENTE.'" selected>'.$comentario[0]->AGENTE.'</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label"><i class="fas fa-comment me-2"></i>Comentario</label>
                                        <textarea class="form-control comentario" rows="4" disabled>'.$comentario[0]->COMENTARIOS.'</textarea>
                                    </div>
                                </div>
                            </div>';
                    }
                }
                ?>
                
                <div id="seccionesHistorial">
                    <!-- Las secciones se agregarán dinámicamente aquí con JavaScript -->
                </div>
            </div>

            <div class="modal-footer">
                <?php if (!isset($historial[0]) || $historial[0]['ESTADO'] != 'resuelto'): ?>
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
                <?php else: ?>
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
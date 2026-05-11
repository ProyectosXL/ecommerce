<?php
/**
 * Componente: Logística Inversa (pestaña embebida)
 * Incluido desde seguimientoPedidos/index.php
 */
?>
<!-- Sub-navegación: Alta / Listado -->
<ul class="nav nav-tabs mb-3" id="li-nav-tabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="alta-tab"
                data-bs-toggle="tab" data-bs-target="#alta-content"
                type="button" role="tab" aria-selected="true">
            <i class="fas fa-plus-circle me-2"></i>Nueva devolución / cambio
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="listado-tab"
                data-bs-toggle="tab" data-bs-target="#listado-content"
                type="button" role="tab" aria-selected="false">
            <i class="fas fa-list me-2"></i>Listado
        </button>
    </li>
</ul>

<div class="tab-content" id="li-tab-content">

    <!-- ===================================================
         SUB-TAB 1 — ALTA
         =================================================== -->
    <div class="tab-pane fade show active" id="alta-content" role="tabpanel">

        <!-- Indicador de pasos -->
        <div class="li-steps mb-4">
            <div class="li-step li-step-active" id="step-1">
                <div class="li-step-num">1</div>
                <div class="li-step-label">Buscar pedido</div>
            </div>
            <div class="li-step-line" id="step-line-12"></div>
            <div class="li-step" id="step-2">
                <div class="li-step-num">2</div>
                <div class="li-step-label">Datos</div>
            </div>
            <div class="li-step-line" id="step-line-23"></div>
            <div class="li-step" id="step-3">
                <div class="li-step-num">3</div>
                <div class="li-step-label">Productos</div>
            </div>
        </div>

        <!-- Zona de alertas -->
        <div id="alerta-alta"></div>

        <!-- Búsqueda de pedido -->
        <div class="search-card mb-4">
            <h6 class="fw-bold mb-3">
                <i class="fas fa-search text-primary me-2"></i>Paso 1 — Buscar pedido
            </h6>
            <div class="row g-2 align-items-end">
                <div class="col-md-7">
                    <label class="form-label" for="pedido-id">
                        N° de Pedido / Orden
                        <small class="text-muted ms-1">— Presioná Enter o hacé clic en Buscar</small>
                    </label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                        <input type="text" id="pedido-id" class="form-control"
                               placeholder="Ej: 1234567 / VTX-2026-001"
                               autocomplete="off">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="button" id="btn-buscar-pedido" class="btn btn-primary btn-lg w-100">
                        <i class="fas fa-search me-1"></i>Buscar
                    </button>
                </div>
            </div>
        </div>

        <!-- Datos del pedido encontrado (renderizado por JS) -->
        <div id="pedido-encontrado" style="display:none"></div>

        <!-- Formulario de devolución -->
        <form id="form-devolucion" novalidate>
            <input type="hidden" id="campo-cliente"      name="cliente">
            <input type="hidden" id="campo-fecha-pedido" name="fecha_pedido">

            <!-- Paso 2 -->
            <div class="search-card mb-4 disabled-section" id="seccion-paso2">
                <h6 class="fw-bold mb-3">
                    <i class="fas fa-clipboard-list text-primary me-2"></i>Paso 2 — Datos de la devolución
                </h6>
                <div class="row g-3">

                    <!-- Fila 1: tipo, motivo, usuario -->
                    <div class="col-md-4">
                        <label class="form-label" for="campo-tipo">Tipo *</label>
                        <select id="campo-tipo" name="tipo" class="form-select" required>
                            <option value="">— Seleccionar —</option>
                            <option value="devolucion">Devolución</option>
                            <option value="cambio">Cambio</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="campo-motivo">Motivo</label>
                        <select id="campo-motivo" name="motivo" class="form-select">
                            <option value="">— Seleccionar —</option>
                            <option value="Producto defectuoso">Producto defectuoso</option>
                            <option value="Talla/talle incorrecto">Talla/talle incorrecto</option>
                            <option value="Producto incorrecto">Producto incorrecto</option>
                            <option value="Daño en el envío">Daño en el envío</option>
                            <option value="No corresponde a lo solicitado">No corresponde a lo solicitado</option>
                            <option value="Desistimiento del cliente">Desistimiento del cliente</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="campo-usuario">Usuario / Operador</label>
                        <input type="text" id="campo-usuario" name="usuario"
                               class="form-control" placeholder="Tu nombre o email">
                    </div>

                    <!-- Fila 2: observaciones -->
                    <div class="col-12">
                        <label class="form-label" for="campo-observaciones">Observaciones</label>
                        <textarea id="campo-observaciones" class="form-control" rows="2"
                                  placeholder="Notas internas sobre el caso..."></textarea>
                    </div>

                    <!-- Sección colapsable: Datos de resolución -->
                    <div class="col-12">
                        <div class="border-top pt-2">
                            <a class="li-section-toggle" data-bs-toggle="collapse"
                               href="#seccion-resolucion" role="button" aria-expanded="false">
                                <i class="fas fa-chevron-right li-toggle-icon me-1"></i>
                                Datos de resolución
                                <span class="badge bg-light text-secondary border ms-2 fw-normal" style="font-size:.72rem">Opcional</span>
                            </a>
                        </div>
                        <div class="collapse" id="seccion-resolucion">
                            <div class="row g-3 mt-1">
                                <div class="col-md-4">
                                    <label class="form-label" for="campo-nro-rto">Nro. Remito (RTO)</label>
                                    <input type="text" id="campo-nro-rto" class="form-control" placeholder="Ej: 2402">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="campo-nro-nc-fact">NC / Factura Nro.</label>
                                    <input type="text" id="campo-nro-nc-fact" class="form-control" placeholder="Ej: 5403">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección colapsable: Datos financieros -->
                    <div class="col-12">
                        <div class="border-top pt-2">
                            <a class="li-section-toggle" data-bs-toggle="collapse"
                               href="#seccion-financiero" role="button" aria-expanded="false">
                                <i class="fas fa-chevron-right li-toggle-icon me-1"></i>
                                Datos financieros
                                <span class="badge bg-light text-secondary border ms-2 fw-normal" style="font-size:.72rem">Opcional</span>
                            </a>
                        </div>
                        <div class="collapse" id="seccion-financiero">
                            <div class="row g-3 mt-1">
                                <div class="col-md-3">
                                    <label class="form-label" for="campo-precio-abonado">Precio Abonado ($)</label>
                                    <input type="number" id="campo-precio-abonado" class="form-control"
                                           step="0.01" min="0" placeholder="0.00">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label" for="campo-precio-art-cambio">Precio Art. Cambio ($)</label>
                                    <input type="number" id="campo-precio-art-cambio" class="form-control"
                                           step="0.01" min="0" placeholder="0.00">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Diferencia ($)
                                        <small class="text-muted">(calculado)</small>
                                    </label>
                                    <input type="number" id="campo-diferencia-precio" class="form-control bg-light"
                                           step="0.01" readonly placeholder="—">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="campo-link-pago-mp">Link de Pago MercadoPago</label>
                                    <input type="url" id="campo-link-pago-mp" class="form-control"
                                           placeholder="https://mpago.la/...">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label" for="campo-nro-operacion-mp">Nro. Operación MP</label>
                                    <input type="text" id="campo-nro-operacion-mp" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Badge de seguimiento (se muestra tras guardar Paso 2) -->
                <div id="li-nro-seguimiento-badge" class="mt-3" style="display:none">
                    <div class="alert alert-success d-flex align-items-center gap-3 py-2 mb-0">
                        <i class="fas fa-check-circle fs-4"></i>
                        <div>
                            <div class="fw-bold">Paso 2 guardado &mdash; N&deg; de Seguimiento:</div>
                            <span id="li-nro-seguimiento-valor" class="fs-5 fw-bold font-monospace"></span>
                        </div>
                    </div>
                </div>

                <!-- Botón Guardar Paso 2 -->
                <div class="d-flex justify-content-end mt-3">
                    <button type="button" id="btn-guardar-paso2" class="btn btn-primary btn-lg" disabled>
                        <i class="fas fa-arrow-right me-1"></i>Guardar y continuar al Paso 3
                    </button>
                </div>

            </div>

            <!-- Paso 3 — Productos -->
            <div id="seccion-productos" class="search-card mb-4 disabled-section">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0">
                        <i class="fas fa-boxes text-primary me-2"></i>Paso 3 — Productos a devolver / cambiar
                        <small class="text-muted fw-normal ms-2">Ajuste cantidades y acción por producto</small>
                    </h6>
                    <div id="li-sync-hint" class="text-success small" style="display:none">
                        <i class="fas fa-magic me-1"></i>Acción sincronizada con el tipo
                    </div>
                </div>
                <div id="tabla-productos" class="li-productos-container">
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-search me-2"></i>Busque un pedido para ver los productos.
                    </div>
                </div>
            </div>

            </div><!-- /seccion-productos -->

            <!-- Botones Paso 3 -->
            <div class="d-flex justify-content-between gap-2 mt-2">
                <button type="button" class="btn btn-outline-secondary" onclick="resetFormulario()">
                    <i class="fas fa-redo me-1"></i>Limpiar y empezar de nuevo
                </button>
                <button type="submit" id="btn-guardar" class="btn btn-success btn-lg" disabled>
                    <i class="fas fa-save me-1"></i>Guardar productos
                </button>
            </div>
        </form>

    </div><!-- /SUB-TAB alta -->

    <!-- ===================================================
         SUB-TAB 2 — LISTADO
         =================================================== -->
    <div class="tab-pane fade" id="listado-content" role="tabpanel">

        <!-- Filtros -->
        <div class="search-card mb-4">
            <h6 class="fw-bold mb-3">
                <i class="fas fa-filter text-primary me-2"></i>Filtros
            </h6>
            <div class="row g-2 align-items-end">
                <div class="col-sm-6 col-md-2">
                    <label class="form-label">Estado</label>
                    <select id="filtro-estado" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="en_transito">En tránsito</option>
                        <option value="recibido">Recibido</option>
                        <option value="resuelto">Resuelto</option>
                    </select>
                </div>
                <div class="col-sm-6 col-md-2">
                    <label class="form-label">Tipo</label>
                    <select id="filtro-tipo" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="devolucion">Devolución</option>
                        <option value="cambio">Cambio</option>
                    </select>
                </div>
                <div class="col-sm-6 col-md-2">
                    <label class="form-label">Desde</label>
                    <input type="date" id="filtro-desde" class="form-control form-control-sm"
                           value="<?php echo date('Y-m-d', strtotime('-30 days')); ?>">
                </div>
                <div class="col-sm-6 col-md-2">
                    <label class="form-label">Hasta</label>
                    <input type="date" id="filtro-hasta" class="form-control form-control-sm"
                           value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="col-sm-12 col-md-4 d-flex gap-2">
                    <button id="btn-filtrar" class="btn btn-primary btn-sm">
                        <i class="fas fa-search me-1"></i>Filtrar
                    </button>
                    <button id="btn-limpiar-filtros" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-eraser me-1"></i>Limpiar
                    </button>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-2 gap-2">
            <input type="text" id="busqueda-listado" class="form-control form-control-sm"
                   style="max-width:280px" placeholder="Buscar por N° seguimiento, pedido o cliente...">
            <div class="text-muted small" id="total-registros"></div>
        </div>

        <div class="table-responsive">
            <table id="tabla-listado" class="table table-bordered table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:130px">N° Seguimiento</th>
                        <th>Pedido</th>
                        <th>Cliente</th>
                        <th style="width:120px">Tipo</th>
                        <th style="width:140px">Estado</th>
                        <th style="width:140px">Fecha creación</th>
                        <th class="text-center" style="width:140px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Seleccione la pestaña para cargar el listado.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div><!-- /SUB-TAB listado -->

</div><!-- /li-tab-content -->

<!-- Modal detalle de devolución -->
<div class="modal fade" id="modal-detalle" tabindex="-1" aria-labelledby="modal-detalle-label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-detalle-label">
                    <i class="fas fa-file-alt me-2"></i>Detalle de devolución
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modal-detalle-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?php
/**
 * Módulo: Logística Inversa — Devoluciones y Cambios
 * Página principal
 */
date_default_timezone_set('America/Argentina/Buenos_Aires');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logística Inversa — Devoluciones y Cambios</title>
    <link rel="shortcut icon" href="../assets/icono.ico" />

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Módulo CSS -->
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="bg-light">

<div class="container-fluid py-4">

    <!-- Encabezado -->
    <div class="d-flex align-items-center mb-4 gap-3">
        <a href="../index.php" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Volver
        </a>
        <div>
            <h4 class="mb-0 fw-bold">
                <i class="fas fa-undo-alt text-primary me-2"></i>Logística Inversa
            </h4>
            <small class="text-muted">Gestión de devoluciones y cambios de pedidos</small>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="main-tabs" role="tablist">
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
        </div>

        <div class="card-body">
            <div class="tab-content" id="main-tab-content">

                <!-- =====================================================
                     TAB 1 — ALTA
                     ===================================================== -->
                <div class="tab-pane fade show active" id="alta-content" role="tabpanel">

                    <!-- Zona de alertas -->
                    <div id="alerta-alta"></div>

                    <!-- Búsqueda de pedido -->
                    <div class="search-card mb-4">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-search text-primary me-2"></i>Paso 1 — Buscar pedido
                        </h6>
                        <div class="row g-2 align-items-end">
                            <div class="col-md-6">
                                <label class="form-label" for="pedido-id">N° de Pedido / Orden</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-hashtag"></i>
                                    </span>
                                    <input type="text" id="pedido-id" class="form-control"
                                           placeholder="Ej: 1234567 / VTX-2026-001"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="button" id="btn-buscar-pedido" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-1"></i>Buscar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Datos del pedido encontrado -->
                    <div id="pedido-encontrado" class="card p-3 mb-4" style="display:none">
                        <h6 class="fw-bold mb-3 text-primary">
                            <i class="fas fa-check-circle me-2"></i>Pedido encontrado
                        </h6>
                        <div class="row g-3">
                            <div class="col-sm-6 col-md-3">
                                <div class="info-label">Cliente</div>
                                <div class="info-value" id="info-cliente">—</div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="info-label">Fecha pedido</div>
                                <div class="info-value" id="info-fecha">—</div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="info-label">N° Orden</div>
                                <div class="info-value" id="info-orden">—</div>
                            </div>
                            <div class="col-sm-6 col-md-2">
                                <div class="info-label">Canal</div>
                                <div class="info-value" id="info-canal">—</div>
                            </div>
                            <div class="col-sm-6 col-md-1">
                                <div class="info-label">Productos</div>
                                <div class="info-value" id="info-items">—</div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario de devolución -->
                    <form id="form-devolucion" novalidate>
                        <!-- Campos ocultos pre-completados -->
                        <input type="hidden" id="campo-cliente"       name="cliente">
                        <input type="hidden" id="campo-fecha-pedido"  name="fecha_pedido">

                        <!-- Paso 2 — Datos de la devolución -->
                        <div class="search-card mb-4">
                            <h6 class="fw-bold mb-3">
                                <i class="fas fa-clipboard-list text-primary me-2"></i>Paso 2 — Datos de la devolución
                            </h6>
                            <div class="row g-3">
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
                            </div>
                        </div>

                        <!-- Paso 3 — Productos -->
                        <div id="seccion-productos" class="search-card mb-4 disabled-section">
                            <h6 class="fw-bold mb-3">
                                <i class="fas fa-boxes text-primary me-2"></i>Paso 3 — Productos a devolver / cambiar
                                <small class="text-muted fw-normal ms-2">Ajuste cantidades y acción por producto</small>
                            </h6>
                            <div class="table-responsive">
                                <table id="tabla-productos" class="table table-bordered table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width:120px">Código</th>
                                            <th>Producto</th>
                                            <th class="text-center" style="width:100px">Cant. orig.</th>
                                            <th class="text-center" style="width:110px">Cant. a gestionar</th>
                                            <th style="width:150px">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <i class="fas fa-search me-2"></i>
                                                Busque un pedido para ver los productos.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-secondary"
                                    onclick="resetFormulario()">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </button>
                            <button type="submit" id="btn-guardar" class="btn btn-success">
                                <i class="fas fa-save me-1"></i>Guardar
                            </button>
                        </div>
                    </form>

                </div><!-- /TAB alta -->

                <!-- =====================================================
                     TAB 2 — LISTADO
                     ===================================================== -->
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

                    <!-- Totalizador -->
                    <div class="mb-2 text-end text-muted small" id="total-registros"></div>

                    <!-- Tabla -->
                    <div class="table-responsive">
                        <table id="tabla-listado" class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th style="width:60px">#</th>
                                    <th>Pedido</th>
                                    <th>Cliente</th>
                                    <th style="width:120px">Tipo</th>
                                    <th style="width:140px">Estado</th>
                                    <th style="width:140px">Fecha creación</th>
                                    <th class="text-center" style="width:100px">Acciones</th>
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

                </div><!-- /TAB listado -->

            </div><!-- /tab-content -->
        </div><!-- /card-body -->
    </div><!-- /card -->
</div><!-- /container -->

<!-- =====================================================
     MODAL — DETALLE DE DEVOLUCIÓN
     ===================================================== -->
<div class="modal fade" id="modal-detalle" tabindex="-1" aria-labelledby="modal-detalle-label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-detalle-label">
                    <i class="fas fa-file-alt me-2"></i>Detalle de devolución
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modal-detalle-body">
                <!-- Se rellena con JS -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/main.js"></script>

</body>
</html>

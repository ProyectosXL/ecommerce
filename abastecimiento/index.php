
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Remitos Ecommerce</title>
    <link rel="shortcut icon" href="../assets/icono.ico" />
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="/ecommerce/Abastecimiento/css/importarRemitos.css" rel="stylesheet">
</head>
<body class="bg-light">
    
    <!-- Header -->
    <div class="header-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-0"><i class="bi bi-truck"></i> Remitos Abastecimiento Ecommerce</h1>
                    <p class="mb-0 mt-2 opacity-75">Control y gestión de remitos de abastecimiento ecommerce</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-success-custom" id="btnImportar">
                            <i class="bi bi-cloud-download"></i> Importar Remitos
                        </button>
                        <button type="button" class="btn btn-info-custom" id="btnActualizarRemito" title="Forzar Remito Individual">
                            <i class="bi bi-pencil-square"></i> Forzar Remito
                        </button>
                        <button type="button" class="btn btn-warning-custom" onclick="window.remitoManager && window.remitoManager.exportarExcel()" title="Exportar Excel">
                            <i class="bi bi-file-earmark-excel"></i> Exportar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-4">
        <!-- Filtros -->
        <div class="card filters-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-funnel"></i> Filtros de Búsqueda</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="fechaDesde" class="form-label">Fecha Desde:</label>
                        <input type="date" class="form-control" id="fechaDesde">
                    </div>
                    <div class="col-md-3">
                        <label for="fechaHasta" class="form-label">Fecha Hasta:</label>
                        <input type="date" class="form-control" id="fechaHasta">
                    </div>
                    <div class="col-md-3">
                        <label for="estado" class="form-label">Estado:</label>
                        <select class="form-select" id="estado">
                            <option value="TODOS">Todos los Estados</option>
                            <option value="SIN IMPORTAR">Sin Importar</option>
                            <option value="INGRESADO">Ingresado</option>
                            <option value="SIN INGRESAR">Sin Ingresar</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="button" class="btn btn-primary-custom flex-fill" id="btnFiltrar">
                            <i class="bi bi-search"></i> Filtrar
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="btnLimpiar" title="Limpiar Filtros">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="bi bi-list-ul text-primary fs-1 me-2"></i>
                            <div>
                                <h3 class="mb-0 text-primary" id="totalRemitos">0</h3>
                                <small class="text-muted">Total Remitos</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="bi bi-clock-history text-warning fs-1 me-2"></i>
                            <div>
                                <h3 class="mb-0 text-warning" id="sinImportar">0</h3>
                                <small class="text-muted">Sin Importar</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="bi bi-check-circle text-success fs-1 me-2"></i>
                            <div>
                                <h3 class="mb-0 text-success" id="ingresado">0</h3>
                                <small class="text-muted">Ingresado</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="bi bi-x-circle text-danger fs-1 me-2"></i>
                            <div>
                                <h3 class="mb-0 text-danger" id="sinIngresar">0</h3>
                                <small class="text-muted">Sin Ingresar</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Remitos -->
        <div class="table-container">
            <table class="table table-hover mb-0" id="tablaRemitos">
                <thead>
                    <tr>
                        <th><i class="bi bi-calendar3"></i> Fecha</th>
                        <th><i class="bi bi-clock"></i> Hora</th>
                        <th><i class="bi bi-building"></i> Proveedor</th>
                        <th><i class="bi bi-file-text"></i> N° Comprobante</th>
                        <th class="text-end"><i class="bi bi-123"></i> Cantidad</th>
                        <th><i class="bi bi-flag"></i> Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- DataTables se encargará del contenido -->
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="text-center mt-4 mb-4">
            <small class="text-muted">
                <i class="bi bi-info-circle"></i> 
                Sistema de Gestión de Remitos - Última actualización: <?php echo date('d/m/Y H:i'); ?>
            </small>
        </div>
    </div>

    <!-- Agregar este modal antes del cierre del body -->
    <!-- Modal para actualizar remito individual -->
    <div class="modal fade" id="modalActualizarRemito" tabindex="-1" aria-labelledby="modalActualizarRemitoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalActualizarRemitoLabel">
                        <i class="bi bi-pencil-square text-info me-2"></i>
                        Actualizar Estado de Remito
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="inputNRemito" class="form-label">
                                    <i class="bi bi-file-text me-1"></i>
                                    Número de Remito:
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="inputNRemito" placeholder="Ingrese el número de remito" maxlength="20">
                                    <button class="btn btn-outline-primary" type="button" id="btnVerificarRemito">
                                        <i class="bi bi-search"></i> Verificar
                                    </button>
                                </div>
                                <div class="form-text">Ingrese el número de remito que desea actualizar</div>
                            </div>
                        </div>
                    </div>

                    <!-- Información del remito -->
                    <div id="infoRemito" class="d-none">
                        <div class="card bg-light border-0 mb-3">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="bi bi-info-circle me-1"></i> Información del Remito</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">Número:</small>
                                        <div class="fw-bold" id="detalleNumero">-</div>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">Fecha:</small>
                                        <div class="fw-bold" id="detalleFecha">-</div>
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <small class="text-muted">Proveedor:</small>
                                        <div class="fw-bold" id="detalleProveedor">-</div>
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <small class="text-muted">Estado Actual:</small>
                                        <div class="fw-bold" id="detalleEstado">-</div>
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <small class="text-muted">Total Artículos:</small>
                                        <div class="fw-bold" id="detalleTotalArticulos">-</div>
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <small class="text-muted">Cantidad Total:</small>
                                        <div class="fw-bold" id="detalleCantidadTotal">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Alertas -->
                    <div id="alertaRemito" class="d-none">
                        <div class="alert alert-info mb-3" role="alert">
                            <i class="bi bi-info-circle me-2"></i>
                            <span id="mensajeAlerta"></span>
                        </div>
                    </div>

                    <!-- Instrucciones -->
                    <div class="alert alert-warning" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Importante:</strong> Esta operación ejecutará el stored procedure <code>RO_SP_ACTUALIZAR_ESTADO_Y_CANTIDAD_GTWEB</code> que:
                        <ul class="mb-0 mt-2">
                            <li>Cambiará el estado del remito a 'P' (Procesado)</li>
                            <li>Actualizará la cantidad real (CANT_REAL = CANTIDAD) en los artículos</li>
                            <li>Solo afectará remitos de los últimos 45 días</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="btnEjecutarActualizacion" disabled>
                        <i class="bi bi-gear me-1"></i>
                        Ejecutar Actualización
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery (necesario para DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SheetJS para exportar XLSX -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <!-- Custom JS -->
    <script src="/ecommerce/Abastecimiento/js/importarRemitos.js"></script>
</body>
</html>
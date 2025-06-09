
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
                        <button type="button" class="btn btn-warning-custom" onclick="window.remitoManager && window.remitoManager.exportarExcel()" title="Exportar Excel">
                            <i class="bi bi-file-earmark-excel"></i> Exportar
                        </button>
                        <button type="button" class="btn btn-success-custom" id="btnImportar">
                            <i class="bi bi-cloud-download"></i> Importar Remitos
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
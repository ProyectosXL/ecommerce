
<?php
// seguimientoPedidos/historial-incidentes.php

// Verificación de autenticación (ajustar según el sistema existente)
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_autenticado']) && !checkUserAuthentication()) {
    header('Location: /login.php');
    exit;
}

// Incluir configuración y clases necesarias
require_once 'config.php';

// Función de verificación de autenticación
function checkUserAuthentication() {
    // Implementar según el sistema de autenticación existente
    // Por ejemplo, verificar token de sesión, cookies, etc.
    
    // Verificación básica por IP permitidas (ejemplo)
    $ips_permitidas = [
        '127.0.0.1',
        '::1',
        // Agregar IPs corporativas
    ];
    
    $ip_cliente = $_SERVER['REMOTE_ADDR'] ?? '';
    
    // En un entorno de producción, implementar autenticación más robusta
    // return in_array($ip_cliente, $ips_permitidas);
    return true; // Para desarrollo
}

// Verificar permisos de acceso al módulo de historial
function checkModulePermissions() {
    // Verificar si el usuario tiene permisos para ver el historial de incidentes
    // Implementar según el sistema de roles existente
    return true; // Para desarrollo
}

if (!checkModulePermissions()) {
    http_response_code(403);
    die('Acceso denegado. No tiene permisos para acceder a este módulo.');
}

// Configuración de la página
$page_title = 'Historial de Incidentes - E-commerce';
$page_description = 'Visualización y análisis del historial completo de incidentes por falta de artículos';

// Obtener parámetros de configuración
$config = [
    'fecha_maxima' => date('Y-m-d'),
    'fecha_por_defecto_inicio' => date('Y-m-d', strtotime('-30 days')),
    'usuario_actual' => $_SESSION['usuario_nombre'] ?? 'Usuario',
    'nivel_acceso' => $_SESSION['nivel_acceso'] ?? 'readonly',
    'timezone' => 'America/Argentina/Buenos_Aires'
];

// Configurar zona horaria
date_default_timezone_set($config['timezone']);

// Logs de acceso (opcional)
error_log("Acceso al historial de incidentes - Usuario: {$config['usuario_actual']} - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Pedidos con Faltantes - E-commerce</title>
    <link rel="shortcut icon" href="../assets/icono.ico" />
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Font Awesome y Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="css/historial-faltantes.css" class="rel">
    
    <!-- SweetAlert2 -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Chart.js para gráficos analíticos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
</head>
<body>
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay" style="display: none;">
        <div class="text-center">
            <div class="loading-spinner"></div>
            <p class="mt-3">Cargando datos...</p>
        </div>
    </div>

    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="material-card mb-4">
            <div class="card-header">
                <h4>
                    <i class="fas fa-exclamation-triangle"></i>
                    Historial de Pedidos con Faltantes
                </h4>
            </div>
        </div>

        <!-- Estadísticas Resumidas -->
        <div class="stats-grid">
            <div class="stat-card danger">
                <i class="fas fa-exclamation-triangle stat-icon text-danger"></i>
                <div class="stat-number text-danger" id="totalFaltantes">0</div>
                <div class="stat-label">Total Pedidos con Faltantes</div>
            </div>
            <div class="stat-card warning">
                <i class="fas fa-calendar-day stat-icon text-warning"></i>
                <div class="stat-number text-warning" id="faltantesHoy">0</div>
                <div class="stat-label">Faltantes Hoy</div>
            </div>
            <div class="stat-card primary">
                <i class="fas fa-boxes stat-icon text-primary"></i>
                <div class="stat-number text-primary" id="articulosFaltantes">0</div>
                <div class="stat-label">Artículos Faltantes</div>
            </div>
            <div class="stat-card success">
                <i class="fas fa-warehouse stat-icon text-success"></i>
                <div class="stat-number text-success" id="warehousesAfectados">0</div>
                <div class="stat-label">Warehouses Afectados</div>
            </div>
        </div>

        <!-- Filtros en una sola línea -->
        <div class="filters-section">
            <h5 class="mb-3">
                <i class="fas fa-filter me-2"></i>
                Filtros de Búsqueda
            </h5>
            <form id="filtrosForm">
                <div class="row g-3 filters-row">
                    <div class="col-md-2">
                        <label class="filter-label" for="fechaInicio">Fecha Inicio</label>
                        <input type="date" class="form-control" id="fechaInicio" name="fechaInicio" 
                               max="" value="">
                    </div>
                    <div class="col-md-2">
                        <label class="filter-label" for="fechaFin">Fecha Fin</label>
                        <input type="date" class="form-control" id="fechaFin" name="fechaFin" 
                               max="" value="">
                    </div>
                    <div class="col-md-2">
                        <label class="filter-label" for="warehouse">Warehouse</label>
                        <select class="form-select" id="warehouse" name="warehouse">
                            <option value="">Todos</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="filter-label" for="estado">Estado</label>
                        <select class="form-select" id="estado" name="estado">
                            <option value="">Todos</option>
                            <option value="abierto">Abierto</option>
                            <option value="proceso">En Proceso</option>
                            <option value="resuelto">Resuelto</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="filter-label" for="resolucion">Resolución</label>
                        <select class="form-select" id="resolucion" name="resolucion">
                            <option value="">Todas</option>
                            <option value="cambio">Cambio</option>
                            <option value="cancelado">Cancelado</option>
                            <option value="completado">Completado</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-1">
                        <button type="button" class="btn btn-primary btn-material flex-grow-1" id="aplicarFiltros">
                            <i class="fas fa-search"></i>
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-material" id="limpiarFiltros" title="Limpiar filtros">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Gráficos Analíticos -->
        <div class="charts-container">
            <div class="chart-card">
                <div class="chart-title">Faltantes por Estado</div>
                <canvas id="chartEstados" width="400" height="200"></canvas>
            </div>
            <div class="chart-card">
                <div class="chart-title">Tendencia de Faltantes</div>
                <canvas id="chartTendencia" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Tabla de Pedidos con Faltantes -->
        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                    <i class="fas fa-table me-2"></i>
                    Registro de Pedidos con Faltantes
                </h5>
                <div id="tableControls">
                    <!-- Los botones de exportación se generarán aquí -->
                </div>
            </div>
            
            <table id="faltantesTable" class="table table-striped table-hover w-100">
                <thead>
                    <tr>
                        <th>Nro. Pedido</th>
                        <th>Nro. Orden</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Artículo Faltante</th>
                        <th>Descripción</th>
                        <th>Cantidad</th>
                        <th>Warehouse</th>
                        <th>Estado</th>
                        <th>Resolución</th>
                        <th>Última Modificación</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Los datos se cargarán dinámicamente -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- DataTables JS -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="js/historial-faltantes.js"></script>

    <!-- Configuración JavaScript -->
    <script>
        // Configuración global
        window.APP_CONFIG = {
            apiBaseUrl: 'Controller/',
            dateFormat: 'dd/mm/yyyy',
            timezone: '<?php echo $config['timezone']; ?>',
            userLevel: '<?php echo $config['nivel_acceso']; ?>',
            autoRefreshInterval: 300000, // 5 minutos
            maxRetries: 3
        };

        // Configuración de idioma para DataTables
        window.DATATABLES_LANGUAGE = {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        };
    </script>

</body>
</html>

<?php
// Log de finalización de carga de página
error_log("Página de historial de incidentes cargada exitosamente - Usuario: {$config['usuario_actual']}");
?>
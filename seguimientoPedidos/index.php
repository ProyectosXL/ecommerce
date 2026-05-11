<?php 
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguimiento y Reportes E-commerce</title>
    <link rel="shortcut icon" href="../assets/icono.ico" />
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Select2 CSS y JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Font Awesome y Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- SweetAlert2 -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- DataTables (necesario para el reporte) -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <!-- NUEVO: CSS para los botones de DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    
    <!-- Chart.js para gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/timeline.css">
    <link rel="stylesheet" href="css/modal.css">
    <link rel="stylesheet" href="css/reportes.css">
    <!-- Logística Inversa -->
    <link rel="stylesheet" href="../cambios-devoluciones/assets/css/styles.css">
</head>
<body>
    <div class="container py-4">
        <div class="card">
            <div class="card-header">
                <!-- NUEVO: Toggle de país a nivel global -->
                <div class="d-flex justify-content-end align-items-center mb-3">
                    <label class="me-2 fw-bold">País:</label>
                    <div class="btn-group" role="group" id="country-selector-global">
                        <?php 
                        // CORRECCIÓN: Preservar país seleccionado después de búsqueda
                        $paisActual = isset($_POST['pais']) ? strtoupper(trim($_POST['pais'])) : 'AR';
                        ?>
                        <input type="radio" class="btn-check" name="country-global" id="country-ar-global" value="AR" <?php echo $paisActual === 'AR' ? 'checked' : ''; ?> autocomplete="off">
                        <label class="btn btn-outline-primary d-flex align-items-center gap-2" for="country-ar-global">
                            <img src="https://flagcdn.com/w20/ar.png" srcset="https://flagcdn.com/w40/ar.png 2x" width="20" alt="Argentina">
                            Argentina
                        </label>
                        
                        <input type="radio" class="btn-check" name="country-global" id="country-uy-global" value="UY" <?php echo $paisActual === 'UY' ? 'checked' : ''; ?> autocomplete="off">
                        <label class="btn btn-outline-primary d-flex align-items-center gap-2" for="country-uy-global">
                            <img src="https://flagcdn.com/w20/uy.png" srcset="https://flagcdn.com/w40/uy.png 2x" width="20" alt="Uruguay">
                            Uruguay
                        </label>
                    </div>
                </div>

                <!-- Pestañas de Navegación -->
                <ul class="nav nav-tabs card-header-tabs" id="main-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="seguimiento-tab" data-bs-toggle="tab" data-bs-target="#seguimiento-content" type="button" role="tab" aria-controls="seguimiento-content" aria-selected="true">
                            <i class="fas fa-search me-2"></i>Seguimiento de Pedido
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="reporte-tab" data-bs-toggle="tab" data-bs-target="#reporte-content" type="button" role="tab" aria-controls="reporte-content" aria-selected="false">
                            <i class="fas fa-chart-line me-2"></i>Reporte de Incidentes
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="logistica-tab" data-bs-toggle="tab" data-bs-target="#logistica-content" type="button" role="tab" aria-controls="logistica-content" aria-selected="false">
                            <i class="fas fa-undo-alt me-2"></i>Logística Inversa
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="myTabContent">
                    <!-- Contenido de la Pestaña de Seguimiento -->
                    <div class="tab-pane fade show active" id="seguimiento-content" role="tabpanel" aria-labelledby="seguimiento-tab">
                        <?php include 'components/search-form.php'; ?>

                        <div id="search-results-container">
                        <?php
                        if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['numero'])) {
                            $numero = trim($_POST['numero']);
                            $desde = isset($_POST['desde']) ? $_POST['desde'] : date('Y-m-d', strtotime('-30 days'));
                            $hasta = isset($_POST['hasta']) ? $_POST['hasta'] : date('Y-m-d');
                            $pais = isset($_POST['pais']) ? strtoupper(trim($_POST['pais'])) : 'AR';
                            
                            $resultado = $pedidos->buscarPedido($desde, $hasta, $numero, $pais);
                            
                            if ($resultado && !empty($resultado)) {
                                foreach($resultado as $row) {
                                    $pedido = $row[0];
                                    
                                    // Usar método según país
                                    if ($pais === 'UY') {
                                        $detalleReclamo = $pedidos->listarReclamoDetalleUY($pedido->NRO_PEDIDO);
                                    } else {
                                        $detalleReclamo = $pedidos->listarReclamoDetalle($pedido->NRO_PEDIDO);
                                    }
                                    
                                    // Cargar información de cancelación/reintegro
                                    $nroOrden = isset($pedido->NRO_ORDEN) ? $pedido->NRO_ORDEN : ($pedido->ORDER_ID_TIENDA ?? '');
                                    $infoCancelacion = $pedidos->verificarCancelacion(trim($pedido->NRO_PEDIDO), trim($nroOrden), $pais);
                                    if ($infoCancelacion) {
                                        $pedido->REINTEGRADO = 1;
                                        $pedido->NCR = $infoCancelacion->numero_ncr;
                                        $pedido->FECHA_NCR = $infoCancelacion->fecha_ncr;
                                        $pedido->FECHA_PEDI = $pedido->FECHA_PEDIDO;
                                    }

                                    // Pre-cargar historial para timeline y modal (evita doble consulta)
                                    $historial = $pedidos->traerHistorialReclamo(trim($pedido->NRO_PEDIDO));

                                    // Si INCOMPLETO=1, verificar si el faltante ya fue resuelto o si solo hay OHGIFT
                                    if (($pedido->INCOMPLETO ?? 0) == 1) {
                                        $nroOrdenCheck = $pedido->NRO_ORDEN ?? ($pedido->ORDER_ID_TIENDA ?? '');
                                        if (!$pedidos->pedidoTieneRealFaltante(trim($nroOrdenCheck))) {
                                            // Solo tenía OHGIFT como faltante
                                            $pedido->INCOMPLETO = 0;
                                        } else {
                                            // Tiene faltante real: verificar si ya fue resuelto en el historial
                                            if ($historial && isset($historial[0]) && $historial[0]['ESTADO'] === 'resuelto') {
                                                $pedido->INCOMPLETO = 0;
                                                $pedido->FALTANTE_RESUELTO = 1;
                                            }
                                        }
                                    }
                                    // Pre-cargar detalle para reutilizarlo en detalle-pedido.php
                                    $detalles = $pedidos->buscarDetallePedido($desde, $hasta, $numero, $pais);
                                    
                                    include 'components/pedido-info.php';
                                    include 'components/devoluciones.php';
                                    include 'components/timeline.php';
                                    include 'components/detalle-pedido.php';
                                    include 'components/modal-historial.php';
                                }
                            } else {
                                echo '<div class="alert alert-warning">No se encontraron resultados para la búsqueda.</div>';
                            }
                        }
                        ?>
                        </div>
                    </div>
                    <!-- Contenido de la Pestaña de Reportes -->
                    <div class="tab-pane fade" id="reporte-content" role="tabpanel" aria-labelledby="reporte-tab">
                        <?php include 'components/reporte-incidentes.php'; ?>
                    </div>
                    <!-- Contenido de la Pestaña de Logística Inversa -->
                    <div class="tab-pane fade" id="logistica-content" role="tabpanel" aria-labelledby="logistica-tab">
                        <?php include 'components/logistica-inversa.php'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DataTables JS y extensiones para botones -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <!-- NUEVO: Scripts para los botones de exportación -->
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <!-- Scripts personalizados -->
    <script src="js/main.js"></script>
    <script src="js/modal-handler.js"></script>
    <script src="js/ajax-requests.js"></script>
    <script src="js/reporte-incidentes.js"></script>
    <!-- Logística Inversa -->
    <script>window.LOGISTICA_API_BASE = '../cambios-devoluciones/api/';</script>
    <script src="../cambios-devoluciones/assets/js/main.js"></script>
    
    <script>
        // CORRECCIÓN: Sincronizar país seleccionado con valor del servidor
        window.paisSeleccionado = '<?php echo $paisActual ?? 'AR'; ?>';
        
        window.addEventListener('load', function() {
            hideSpinner();
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
                setTimeout(function() { hideSpinner(); }, 500);
            <?php endif; ?>
        });
    </script>
</body>
</html>
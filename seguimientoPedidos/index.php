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
    
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/timeline.css">
    <link rel="stylesheet" href="css/modal.css">
    <link rel="stylesheet" href="css/reportes.css"> <!-- Nuevo archivo CSS -->
</head>
<body>
    <div class="container py-4">
        <div class="card">
            <div class="card-header">
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
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="myTabContent">
                    <!-- Contenido de la Pestaña de Seguimiento -->
                    <div class="tab-pane fade show active" id="seguimiento-content" role="tabpanel" aria-labelledby="seguimiento-tab">
                        <?php include 'components/search-form.php'; ?>

                        <?php
                        if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['numero'])) {
                            $numero = trim($_POST['numero']);
                            $desde = isset($_POST['desde']) ? $_POST['desde'] : date('Y-m-d', strtotime('-30 days'));
                            $hasta = isset($_POST['hasta']) ? $_POST['hasta'] : date('Y-m-d');
                            
                            $resultado = $pedidos->buscarPedido($desde, $hasta, $numero);
                            
                            if ($resultado && !empty($resultado)) {
                                foreach($resultado as $row) {
                                    $pedido = $row[0];
                                    $detalleReclamo = $pedidos->listarReclamoDetalle($pedido->NRO_PEDIDO);
                                    
                                    include 'components/pedido-info.php';
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
                    <!-- Contenido de la Pestaña de Reportes -->
                    <div class="tab-pane fade" id="reporte-content" role="tabpanel" aria-labelledby="reporte-tab">
                        <?php include 'components/reporte-incidentes.php'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DataTables JS (necesario para el reporte) -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <!-- Scripts personalizados -->
    <script src="js/main.js"></script>
    <script src="js/modal-handler.js"></script>
    <script src="js/ajax-requests.js"></script>
    <script src="js/reporte-incidentes.js"></script> <!-- Nuevo archivo JS -->
    
    <script>
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
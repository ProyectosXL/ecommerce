
<?php 
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguimiento de Pedidos E-commerce</title>
    <link rel="shortcut icon" href="assets/icono.ico" />
    
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
    
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/timeline.css">
    <link rel="stylesheet" href="css/modal.css">
</head>
<body>
    <div class="container py-4">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-shopping-cart me-2"></i>
                    Seguimiento de Pedidos E-commerce
                </h4>
            </div>
            <div class="card-body">
                <!-- Incluir formulario de búsqueda -->
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
                            
                            // Incluir componentes del pedido
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
        </div>
    </div>

    <!-- Scripts personalizados -->
    <script src="js/main.js"></script>
    <script src="js/modal-handler.js"></script>
    <script src="js/ajax-requests.js"></script>
</body>
</html>
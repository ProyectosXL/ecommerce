

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tablero de Control Ecommerce</title>
    <?php 
        require_once $_SERVER['DOCUMENT_ROOT']. '/ecommerce/assets/css/css.php';
    ?>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css" class="rel css">
    
    <style>
        /* Estilos para las cards */
        .dashboard-card {
            height: 400px; /* Altura fija para todas las cards */
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            transition: transform 0.2s, box-shadow 0.2s;
            margin-bottom: 20px;
            border: none;
        }
    </style>
</head>

<body>
    <?php date_default_timezone_set('America/Argentina/Buenos_Aires'); ?>

    <div class="container py-4">
        
        <!-- Header -->
        <?php include 'includes/header.php'; ?>

        <!-- Navegación por pestañas -->
        <?php include 'includes/tabs-navigation.php'; ?>

        <!-- Contenido de las pestañas -->
        <div class="tab-content" id="dashboardTabsContent">
            <!-- Pestaña de Documentación -->
            <div class="tab-pane fade show active" id="documentacion" role="tabpanel" aria-labelledby="documentacion-tab">
                <?php include 'tabs/tab-documentacion.php'; ?>
            </div>
            
            <!-- Pestaña de Integraciones -->
            <div class="tab-pane fade" id="integraciones" role="tabpanel" aria-labelledby="integraciones-tab">
                <?php include 'tabs/tab-integraciones.php'; ?>
            </div>
            
            <!-- Pestaña de Operaciones Central -->
            <div class="tab-pane fade" id="operaciones-central" role="tabpanel" aria-labelledby="operaciones-central-tab">
                <?php include 'tabs/tab-operaciones-central.php'; ?>
            </div>
            
            <!-- Pestaña de Operaciones Sucursales -->
            <div class="tab-pane fade" id="operaciones-sucursales" role="tabpanel" aria-labelledby="operaciones-sucursales-tab">
                <?php include 'tabs/tab-operaciones-sucursales.php'; ?>
            </div>
            
            <!-- Pestaña de Uruguay -->
            <div class="tab-pane fade" id="uruguay" role="tabpanel" aria-labelledby="uruguay-tab">
                <?php include 'tabs/tab-uruguay.php'; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap y librerías base (deben cargarse primero) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script src="js/xlsx.full.min.js"></script>

    <!-- Modales (después de Bootstrap) -->
    <?php require_once 'modals/modals.php'; ?>

    <!-- Scripts personalizados y configuración -->
    <?php include 'includes/scripts.php'; ?>

</body>
</html>
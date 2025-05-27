
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
    <?php
    require_once '../Class/Control.php';
    require_once 'includes/data-loader.php';

    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $ultimaActualizacion = new DateTime();
    ?>

    <div class="container py-4">
        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                Error: <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
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
            
            <!-- Pestaña de Operaciones -->
            <div class="tab-pane fade" id="operaciones" role="tabpanel" aria-labelledby="operaciones-tab">
                <?php include 'tabs/tab-operaciones.php'; ?>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <?php include 'includes/scripts.php'; ?>

    <?php require_once 'modals/modals.php'; ?>

</body>
</html>
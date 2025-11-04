
<!-- includes/tabs-navigation.php -->
<?php
// Fecha de lanzamiento de la pestaña Uruguay
$fechaLanzamientoUruguay = new DateTime('2025-11-04'); // Fecha actual
$fechaActual = new DateTime();
$diasDesdeeLanzamiento = $fechaActual->diff($fechaLanzamientoUruguay)->days;
$mostrarNuevoUruguay = $diasDesdeeLanzamiento <= 7;
?>

<ul class="nav nav-tabs mb-4" id="dashboardTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="documentacion-tab" data-bs-toggle="tab" data-bs-target="#documentacion" type="button" role="tab" aria-controls="documentacion" aria-selected="true">
            <i class="fas fa-file-alt me-2"></i>Documentación
            <?php if ($totalPendientesDocumentacion > 0): ?>
                <span class="badge bg-danger"><?php echo $totalPendientesDocumentacion; ?></span>
            <?php endif; ?>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="integraciones-tab" data-bs-toggle="tab" data-bs-target="#integraciones" type="button" role="tab" aria-controls="integraciones" aria-selected="false">
            <i class="fas fa-link me-2"></i>Integraciones
            <?php if ($totalPendientesIntegraciones > 0): ?>
                <span class="badge bg-danger"><?php echo $totalPendientesIntegraciones; ?></span>
            <?php endif; ?>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="operaciones-central-tab" data-bs-toggle="tab" data-bs-target="#operaciones-central" type="button" role="tab" aria-controls="operaciones-central" aria-selected="false">
            <i class="fas fa-warehouse me-2"></i>Operaciones Central
            <?php if ($totalPendientesOperacionesCentral > 0): ?>
                <span class="badge bg-danger"><?php echo $totalPendientesOperacionesCentral; ?></span>
            <?php endif; ?>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="operaciones-sucursales-tab" data-bs-toggle="tab" data-bs-target="#operaciones-sucursales" type="button" role="tab" aria-controls="operaciones-sucursales" aria-selected="false">
            <i class="fas fa-store me-2"></i>Operaciones Sucursales
            <?php if ($totalPendientesOperacionesSucursales > 0): ?>
                <span class="badge bg-danger"><?php echo $totalPendientesOperacionesSucursales; ?></span>
            <?php endif; ?>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="uruguay-tab" data-bs-toggle="tab" data-bs-target="#uruguay" type="button" role="tab" aria-controls="uruguay" aria-selected="false">
            <i class="fas fa-flag me-2"></i>Uruguay
            <?php if ($mostrarNuevoUruguay): ?>
                <span class="badge bg-success ms-1">NUEVO</span>
            <?php endif; ?>
            <?php if ($totalPendientesUruguay > 0): ?>
                <span class="badge bg-danger"><?php echo $totalPendientesUruguay; ?></span>
            <?php endif; ?>
        </button>
    </li>
</ul>
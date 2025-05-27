
<!-- includes/tabs-navigation.php -->
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
        <button class="nav-link" id="operaciones-tab" data-bs-toggle="tab" data-bs-target="#operaciones" type="button" role="tab" aria-controls="operaciones" aria-selected="false">
            <i class="fas fa-cogs me-2"></i>Operaciones
            <?php if ($totalPendientesOperaciones > 0): ?>
                <span class="badge bg-danger"><?php echo $totalPendientesOperaciones; ?></span>
            <?php endif; ?>
        </button>
    </li>
</ul>
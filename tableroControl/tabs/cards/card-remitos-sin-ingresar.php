<div class="col-md-6 col-lg-3" data-card-id="remitosSinIngresar">
    <div class="card dashboard-card card-pending-remitos">
        <div class="card-header position-relative">
            <button type="button" class="btn-card-refresh" onclick="refreshCard('operaciones-central', 'remitosSinIngresar')" title="Actualizar"><i class="fas fa-sync-alt"></i></button>
            <i class="fas fa-file-invoice card-icon"></i>
            <h5 class="card-title">
                Remitos Abastecimiento Sin Ingresar
                <i class="fas fa-info-circle info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Remitos de abastecimiento que aún no han sido ingresados al sistema de los últimos 60 días"></i>
            </h5>
        </div>
        <div class="card-body">
            <?php if ($remitosSinIntegrar !== null && is_array($remitosSinIntegrar) && count($remitosSinIntegrar) > 0): ?>
                <p class="card-value"><?php echo count($remitosSinIntegrar); ?></p>
                <?php if (isset($remitosSinIntegrar[0]->FECHA_MAS_ANTIGUA)): ?>
                    <br>
                    <p class="date-info">Desde: <?php echo $remitosSinIntegrar[0]->FECHA_MAS_ANTIGUA->format('d/m/Y'); ?></p>
                <?php endif; ?>
                <div class="mt-2">
                    <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#modalRemitosSinIntegrar">
                        <i class="fas fa-list-ul me-2"></i>Ver Detalle
                    </button>
                </div>
            <?php else: ?>
                <p class="no-data">Sin remitos pendientes</p>
            <?php endif; ?>
        </div>
    </div>
</div>

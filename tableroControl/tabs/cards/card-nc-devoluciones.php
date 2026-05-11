<div class="col-md-6 col-lg-3" data-card-id="ncDevoluciones">
    <div class="card dashboard-card card-devoluciones">
        <div class="card-header position-relative">
            <button type="button" class="btn-card-refresh" onclick="refreshCard('documentacion', 'ncDevoluciones')" title="Actualizar"><i class="fas fa-sync-alt"></i></button>
            <i class="fas fa-undo card-icon"></i>
            <h5 class="card-title">
                NC Pendientes por Devoluciones
                <i class="fas fa-info-circle info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Notas de crédito pendientes por devoluciones de productos de los últimos 270 días"></i>
            </h5>
        </div>
        <div class="card-body">
            <?php if ($ncDevoluciones && !empty($ncDevoluciones->CANT_NC_DEV)): ?>
                <p class="card-value"><?php echo htmlspecialchars($ncDevoluciones->CANT_NC_DEV); ?></p>
                <p class="mb-0">Importe: $<?php echo number_format($ncDevoluciones->IMPORTE_PEND, 2); ?></p>
                <p class="date-info">Desde: <?php echo $ncDevoluciones->FECHA->format('d/m/Y'); ?></p>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalNcr">
                        <i class="fas fa-chart-bar me-2"></i>Ver Estadísticas
                    </button>
                    <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#modalNcDevolucionesDetalle">
                        <i class="fas fa-list-ul me-2"></i>Ver Detalle
                    </button>
                </div>
            <?php else: ?>
                <p class="no-data">Sin devoluciones pendientes</p>
            <?php endif; ?>
        </div>
    </div>
</div>

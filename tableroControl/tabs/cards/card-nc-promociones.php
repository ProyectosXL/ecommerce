<div class="col-md-6 col-lg-3" data-card-id="ncPromociones">
    <div class="card dashboard-card card-promociones">
        <div class="card-header position-relative">
            <button type="button" class="btn-card-refresh" onclick="refreshCard('documentacion', 'ncPromociones')" title="Actualizar"><i class="fas fa-sync-alt"></i></button>
            <i class="fas fa-tags card-icon"></i>
            <h5 class="card-title">
                NC Pendientes por Promociones
                <i class="fas fa-info-circle info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Notas de crédito pendientes por promociones aplicadas"></i>
            </h5>
        </div>
        <div class="card-body">
            <?php if ($ncPromociones && !empty($ncPromociones->CANT_NC_PROMO)): ?>
                <p class="card-value"><?php echo htmlspecialchars($ncPromociones->CANT_NC_PROMO); ?></p>
                <p class="mb-0">Importe: $<?php echo number_format($ncPromociones->IMPORTE_NC, 2); ?></p>
                <p class="date-info">Desde: <?php echo $ncPromociones->FECHA->format('d/m/Y'); ?></p>
                <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#modalNcPromocionesDetalle">
                    <i class="fas fa-list-ul me-2"></i>Ver Detalle
                </button>
            <?php else: ?>
                <p class="no-data">Sin NC pendientes</p>
            <?php endif; ?>
        </div>
    </div>
</div>

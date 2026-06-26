<div class="col-md-6 col-lg-3" data-card-id="sincronizadosSinStock">
    <div class="card dashboard-card card-incomplete">
        <div class="card-header position-relative">
            <button type="button" class="btn-card-refresh" onclick="refreshCard('operaciones-central', 'sincronizadosSinStock')" title="Actualizar"><i class="fas fa-sync-alt"></i></button>
            <i class="fas fa-box-open card-icon"></i>
            <h5 class="card-title">
                Sincronizados sin Stock - Central
                <i class="fas fa-info-circle info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Pedidos ecommerce de Central en estado sincronizado (sin avanzar y no cancelados) con al menos un artículo sin stock disponible en el depósito 01"></i>
            </h5>
        </div>
        <div class="card-body">
            <?php if ($pedidosSincronizadosSinStock && !empty($pedidosSincronizadosSinStock->CANT_PEDIDOS)): ?>
                <p class="card-value"><?php echo htmlspecialchars($pedidosSincronizadosSinStock->CANT_PEDIDOS); ?></p>
                <p class="mb-0">Total: $<?php echo number_format($pedidosSincronizadosSinStock->TOTAL_PEDIDOS, 2); ?></p>
                <?php if ($pedidosSincronizadosSinStock->FECHA_MAS_ANTIGUA): ?>
                    <p class="date-info">Desde: <?php echo $pedidosSincronizadosSinStock->FECHA_MAS_ANTIGUA->format('d/m/Y H:i'); ?></p>
                <?php endif; ?>
                <button type="button" class="btn btn-outline-warning w-100" data-bs-toggle="modal" data-bs-target="#modalSincronizadosSinStock">
                    <i class="fas fa-list-ul me-2"></i>Ver Detalle
                </button>
            <?php else: ?>
                <p class="no-data">Sin pedidos pendientes</p>
            <?php endif; ?>
        </div>
    </div>
</div>

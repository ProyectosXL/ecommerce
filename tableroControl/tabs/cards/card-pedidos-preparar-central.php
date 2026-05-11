<div class="col-md-6 col-lg-3" data-card-id="pedidosPreparar">
    <div class="card dashboard-card card-pending-prepare">
        <div class="card-header position-relative">
            <button type="button" class="btn-card-refresh" onclick="refreshCard('operaciones-central', 'pedidosPreparar')" title="Actualizar"><i class="fas fa-sync-alt"></i></button>
            <i class="fas fa-clipboard-list card-icon"></i>
            <h5 class="card-title">
                Pedidos Pend. de Preparar Central
                <i class="fas fa-info-circle info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Pedidos que aún no han sido asignados para picking en los últimos 7 días"></i>
            </h5>
        </div>
        <div class="card-body">
            <?php if ($pedidosPendientesPreparar !== null && $pedidosPendientesPreparar->CANT_PED_PEND !== null && $pedidosPendientesPreparar->CANT_PED_PEND > 0): ?>
                <p class="card-value"><?php echo htmlspecialchars($pedidosPendientesPreparar->CANT_PED_PEND); ?></p>
                <p class="mb-0">Total: $<?php echo number_format($pedidosPendientesPreparar->TOTAL_PEDIDOS, 2); ?></p>
                <?php if ($pedidosPendientesPreparar->FECHA_PEDI): ?>
                    <p class="date-info">Desde: <?php echo $pedidosPendientesPreparar->FECHA_PEDI->format('d/m/Y H:i'); ?></p>
                <?php endif; ?>
                <button type="button" class="btn btn-outline-success w-100" data-bs-toggle="modal" data-bs-target="#modalPendientesPreparar">
                    <i class="fas fa-list-ul me-2"></i>Ver Detalle
                </button>
            <?php else: ?>
                <p class="no-data">Sin pedidos pendientes</p>
            <?php endif; ?>
        </div>
    </div>
</div>

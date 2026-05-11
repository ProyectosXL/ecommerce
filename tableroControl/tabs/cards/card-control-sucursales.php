<div class="col-md-6 col-lg-3" data-card-id="controlSucursales">
    <div class="card dashboard-card card-pending-control">
        <div class="card-header position-relative">
            <button type="button" class="btn-card-refresh" onclick="refreshCard('operaciones-sucursales', 'controlSucursales')" title="Actualizar"><i class="fas fa-sync-alt"></i></button>
            <i class="fas fa-clipboard-check card-icon"></i>
            <h5 class="card-title">
                Pedidos Pendientes de Control Sucursales
                <i class="fas fa-info-circle info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Pedidos sin controlar de Sucursales de los últimos 7 días hasta ayer (excluye Central)"></i>
            </h5>
        </div>
        <div class="card-body">
            <?php if ($pedidosPendientesControlSucursales && !empty($pedidosPendientesControlSucursales->CANTIDAD_PEDIDOS)): ?>
                <p class="card-value"><?php echo htmlspecialchars($pedidosPendientesControlSucursales->CANTIDAD_PEDIDOS); ?></p>
                <?php if ($pedidosPendientesControlSucursales->FECHA_MAS_ANTIGUA): ?>
                    <p class="date-info">Desde: <?php echo $pedidosPendientesControlSucursales->FECHA_MAS_ANTIGUA->format('d/m/Y H:i'); ?></p>
                <?php endif; ?>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalRankingPedidosControlSucursales">
                        <i class="fas fa-trophy me-2"></i>Ver Ranking
                    </button>
                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalPedidosPendientesControlSucursales">
                        <i class="fas fa-list-ul me-2"></i>Ver Detalle
                    </button>
                </div>
            <?php else: ?>
                <p class="no-data">Sin pedidos pendientes</p>
            <?php endif; ?>
        </div>
    </div>
</div>

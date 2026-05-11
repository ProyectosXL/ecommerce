<div class="col-md-6 col-lg-3" data-card-id="controlSucursalesUy">
    <div class="card dashboard-card card-pending-control">
        <div class="card-header position-relative">
            <button type="button" class="btn-card-refresh" onclick="refreshCard('uruguay', 'controlSucursalesUy')" title="Actualizar"><i class="fas fa-sync-alt"></i></button>
            <i class="fas fa-clipboard-check card-icon"></i>
            <h5 class="card-title">
                Pedidos Pendientes de Control
                <i class="fas fa-info-circle info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Pedidos sin controlar de Sucursales de Uruguay de los últimos 7 días hasta ayer"></i>
            </h5>
        </div>
        <div class="card-body">
            <?php if ($pedidosPendientesControlSucursalesUruguay && !empty($pedidosPendientesControlSucursalesUruguay->CANTIDAD_PEDIDOS)): ?>
                <p class="card-value"><?php echo htmlspecialchars($pedidosPendientesControlSucursalesUruguay->CANTIDAD_PEDIDOS); ?></p>
                <?php if ($pedidosPendientesControlSucursalesUruguay->FECHA_MAS_ANTIGUA): ?>
                    <p class="date-info">Desde: <?php echo $pedidosPendientesControlSucursalesUruguay->FECHA_MAS_ANTIGUA->format('d/m/Y H:i'); ?></p>
                <?php endif; ?>
                <button type="button" class="btn btn-outline-success w-100" data-bs-toggle="modal" data-bs-target="#modalPedidosPendientesControlSucursalesUruguay">
                    <i class="fas fa-list-ul me-2"></i>Ver Detalle
                </button>
            <?php else: ?>
                <p class="no-data">Sin pedidos pendientes</p>
            <?php endif; ?>
        </div>
    </div>
</div>

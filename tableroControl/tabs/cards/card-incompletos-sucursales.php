<div class="col-md-6 col-lg-3" data-card-id="incompletosSucursales">
    <div class="card dashboard-card card-incomplete">
        <div class="card-header position-relative">
            <button type="button" class="btn-card-refresh" onclick="refreshCard('operaciones-sucursales', 'incompletosSucursales')" title="Actualizar"><i class="fas fa-sync-alt"></i></button>
            <i class="fas fa-exclamation-triangle card-icon"></i>
            <h5 class="card-title">
                Pedidos Incompletos - Sucursales
                <?php if (!empty($mostrarNuevoPedidosIncompletos)): ?>
                    <span class="badge bg-success ms-1" style="font-size: 0.65rem;">NUEVO</span>
                <?php endif; ?>
                <i class="fas fa-info-circle info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Pedidos con artículos faltantes en sucursales"></i>
            </h5>
        </div>
        <div class="card-body">
            <?php if ($pedidosIncompletosSucursales && !empty($pedidosIncompletosSucursales->CANT_PEDIDOS_INCOMPLETOS)): ?>
                <p class="card-value"><?php echo htmlspecialchars($pedidosIncompletosSucursales->CANT_PEDIDOS_INCOMPLETOS); ?></p>
                <?php if ($pedidosIncompletosSucursales->FECHA_MAS_ANTIGUA): ?>
                    <p class="date-info">Desde: <?php echo $pedidosIncompletosSucursales->FECHA_MAS_ANTIGUA->format('d/m/Y'); ?></p>
                <?php endif; ?>
                <button type="button" class="btn btn-outline-warning w-100" data-bs-toggle="modal" data-bs-target="#modalPedidosIncompletosSucursales">
                    <i class="fas fa-list-ul me-2"></i>Ver Detalle
                </button>
            <?php else: ?>
                <p class="no-data">Sin pedidos incompletos</p>
            <?php endif; ?>
        </div>
    </div>
</div>

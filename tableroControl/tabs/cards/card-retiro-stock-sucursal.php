<div class="col-md-6 col-lg-3" data-card-id="retiroStockSucursal">
    <div class="card dashboard-card card-pickup">
        <div class="card-header position-relative">
            <button type="button" class="btn-card-refresh" onclick="refreshCard('operaciones-sucursales', 'retiroStockSucursal')" title="Actualizar"><i class="fas fa-sync-alt"></i></button>
            <i class="fas fa-store-alt card-icon"></i>
            <h5 class="card-title">
                Pedidos Pendientes de Retiro - Stock Sucursal
                <i class="fas fa-info-circle info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Pedidos para retiro en tienda con stock disponible en la sucursal de los últimos 45 días que aún no fueron entregados"></i>
            </h5>
        </div>
        <div class="card-body">
            <?php if ($pedidosRetiroTienda && !empty($pedidosRetiroTienda->CANT_PED_RETIRO)): ?>
                <p class="card-value"><?php echo htmlspecialchars($pedidosRetiroTienda->CANT_PED_RETIRO); ?></p>
                <p class="mb-0">Total: $<?php echo number_format($pedidosRetiroTienda->TOTAL_PEDIDOS, 2); ?></p>
                <p class="date-info">Desde: <?php echo $pedidosRetiroTienda->FECHA_PEDI->format('d/m/Y H:i'); ?></p>
                <button type="button" class="btn btn-outline-info w-100" data-bs-toggle="modal" data-bs-target="#modalPedidosRetiroTienda">
                    <i class="fas fa-list-ul me-2"></i>Ver Detalle
                </button>
            <?php else: ?>
                <p class="no-data">Sin pedidos pendientes</p>
            <?php endif; ?>
        </div>
    </div>
</div>

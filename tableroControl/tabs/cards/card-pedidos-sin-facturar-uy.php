<div class="col-md-6 col-lg-3" data-card-id="pedidosSinFacturarUy">
    <div class="card dashboard-card card-pedidos">
        <div class="card-header position-relative">
            <button type="button" class="btn-card-refresh" onclick="refreshCard('uruguay', 'pedidosSinFacturarUy')" title="Actualizar"><i class="fas fa-sync-alt"></i></button>
            <i class="fas fa-file-invoice card-icon"></i>
            <h5 class="card-title">
                Pedidos sin Facturar - Tiendas
                <i class="fas fa-info-circle info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Pedidos de Uruguay con más de 30 minutos desde su ingreso en tango pendientes de facturación"></i>
            </h5>
        </div>
        <div class="card-body">
            <?php if ($pedidosSinFacturarUruguay !== null && $pedidosSinFacturarUruguay->CANT_PED_SIN_FACT !== null && $pedidosSinFacturarUruguay->CANT_PED_SIN_FACT > 0): ?>
                <p class="card-value"><?php echo htmlspecialchars($pedidosSinFacturarUruguay->CANT_PED_SIN_FACT); ?></p>
                <p class="mb-0">Total: $<?php echo number_format($pedidosSinFacturarUruguay->TOTAL_PEDIDOS, 2); ?></p>
                <?php if ($pedidosSinFacturarUruguay->FECHA_PEDI): ?>
                    <p class="date-info">Desde: <?php echo $pedidosSinFacturarUruguay->FECHA_PEDI->format('d/m/Y H:i'); ?></p>
                <?php endif; ?>
                <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#modalPedidosSinFacturarUruguay">
                    <i class="fas fa-list-ul me-2"></i>Ver Detalle
                </button>
            <?php else: ?>
                <p class="no-data">Sin pedidos pendientes</p>
            <?php endif; ?>
        </div>
    </div>
</div>

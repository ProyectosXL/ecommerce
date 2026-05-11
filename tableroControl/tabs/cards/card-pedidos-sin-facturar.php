<div class="col-md-6 col-lg-3" data-card-id="pedidosSinFacturar">
    <div class="card dashboard-card card-pedidos">
        <div class="card-header position-relative">
            <button type="button" class="btn-card-refresh" onclick="refreshCard('documentacion', 'pedidosSinFacturar')" title="Actualizar"><i class="fas fa-sync-alt"></i></button>
            <i class="fas fa-file-invoice card-icon"></i>
            <h5 class="card-title mt-2">
                Pedidos sin Facturar Tiendas
                <i class="fas fa-info-circle info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Pedidos con más de 30 minutos desde su ingreso en tango pendientes de facturación"></i>
            </h5>
        </div>
        <div class="card-body">
            <?php if ($pedidosSinFacturar !== null && $pedidosSinFacturar->TOTAL_PEDIDOS !== null): ?>
                <p class="card-value"><?php echo htmlspecialchars($pedidosSinFacturar->CANT_PED_SIN_FACT); ?></p>
                <p class="mb-0">Total: $<?php echo number_format($pedidosSinFacturar->TOTAL_PEDIDOS, 2); ?></p>
                <?php if ($pedidosSinFacturar->FECHA_PEDI): ?>
                    <p class="date-info">Desde: <?php echo $pedidosSinFacturar->FECHA_PEDI->format('d/m/Y H:i'); ?></p>
                <?php endif; ?>
            <?php else: ?>
                <p class="no-data">Sin pedidos pendientes</p>
            <?php endif; ?>
        </div>
    </div>
</div>

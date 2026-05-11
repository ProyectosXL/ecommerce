<div class="col-md-6 col-lg-3" data-card-id="ordenesCierreUy">
    <div class="card dashboard-card card-pending-close">
        <div class="card-header position-relative">
            <button type="button" class="btn-card-refresh" onclick="refreshCard('uruguay', 'ordenesCierreUy')" title="Actualizar"><i class="fas fa-sync-alt"></i></button>
            <i class="fas fa-hourglass-half card-icon"></i>
            <h5 class="card-title">
                Órdenes de Vtex Pendientes de Cierre
                <i class="fas fa-info-circle info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Órdenes de Vtex de Uruguay pendientes de cierre"></i>
            </h5>
        </div>
        <div class="card-body">
            <?php if ($ordenesPendientesCierreUruguay && !empty($ordenesPendientesCierreUruguay->CANT_ORDENES)): ?>
                <p class="card-value"><?php echo htmlspecialchars($ordenesPendientesCierreUruguay->CANT_ORDENES); ?></p>
                <p class="mb-0">Promedio Retraso: <?php echo number_format($ordenesPendientesCierreUruguay->PROM_RETRASO, 0); ?> días</p>
                <p class="date-info">Desde: <?php echo $ordenesPendientesCierreUruguay->FECHA->format('d/m/Y H:i'); ?></p>
                <button type="button" class="btn btn-outline-purple w-100" data-bs-toggle="modal" data-bs-target="#modalOrdenesPendientesCierreUruguay">
                    <i class="fas fa-list-ul me-2"></i>Ver Detalle
                </button>
            <?php else: ?>
                <p class="no-data">Sin órdenes pendientes</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="col-md-6 col-lg-3" data-card-id="ncDevolucionesUy">
    <div class="card dashboard-card card-devoluciones">
        <div class="card-header position-relative">
            <button type="button" class="btn-card-refresh" onclick="refreshCard('uruguay', 'ncDevolucionesUy')" title="Actualizar"><i class="fas fa-sync-alt"></i></button>
            <i class="fas fa-undo card-icon"></i>
            <h5 class="card-title">
                NC Pendientes por Devoluciones
                <i class="fas fa-info-circle info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Notas de crédito pendientes por devoluciones de productos de Uruguay de los últimos 150 días"></i>
            </h5>
        </div>
        <div class="card-body">
            <?php if ($ncDevolucionesUruguay && !empty($ncDevolucionesUruguay->CANT_NC_DEV)): ?>
                <p class="card-value"><?php echo htmlspecialchars($ncDevolucionesUruguay->CANT_NC_DEV); ?></p>
                <p class="mb-0">Importe: $<?php echo number_format($ncDevolucionesUruguay->IMPORTE_PEND, 2); ?></p>
                <p class="date-info">Desde: <?php echo $ncDevolucionesUruguay->FECHA->format('d/m/Y'); ?></p>
                <button type="button" class="btn btn-outline-info w-100" data-bs-toggle="modal" data-bs-target="#modalNcDevolucionesUruguay">
                    <i class="fas fa-list-ul me-2"></i>Ver Detalle
                </button>
            <?php else: ?>
                <p class="no-data">Sin devoluciones pendientes</p>
            <?php endif; ?>
        </div>
    </div>
</div>

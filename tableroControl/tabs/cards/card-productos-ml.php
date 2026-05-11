<div class="col-md-6 col-lg-3" data-card-id="productosMl">
    <div class="card dashboard-card card-ml">
        <div class="card-header position-relative">
            <button type="button" class="btn-card-refresh" onclick="refreshCard('integraciones', 'productosMl')" title="Actualizar"><i class="fas fa-sync-alt"></i></button>
            <i class="fas fa-shopping-bag card-icon"></i>
            <h5 class="card-title">
                Productos Pausados en ML Normal
                <i class="fas fa-info-circle info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Productos que tienen stock en Central pero están pausados en Mercado Libre"></i>
            </h5>
        </div>
        <div class="card-body">
            <?php if ($productosMlFull && !empty($productosMlFull->CANTIDAD_PRODUCTOS)): ?>
                <p class="card-value"><?php echo htmlspecialchars($productosMlFull->CANTIDAD_PRODUCTOS); ?></p>
                <p class="mb-0">Stock total: <?php echo number_format($productosMlFull->PROMEDIO_STOCK, 0); ?> unidades</p>
                <button type="button" class="btn btn-outline-info w-100" data-bs-toggle="modal" data-bs-target="#modalProductosMlFull">
                    <i class="fas fa-list-ul me-2"></i>Ver Detalle
                </button>
            <?php else: ?>
                <p class="no-data">Sin productos pausados</p>
            <?php endif; ?>
        </div>
    </div>
</div>

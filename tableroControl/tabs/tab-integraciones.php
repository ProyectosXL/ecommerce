
<!-- tabs/tab-integraciones.php -->
<div class="d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-outline-primary btn-sm" onclick="actualizarIntegraciones()" id="btnActualizarIntegraciones">
        <i class="fas fa-sync-alt me-2"></i>Actualizar Integraciones
    </button>
</div>
<div class="row">
    <!-- Card de Órdenes sin Integrar -->
    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card card-ordenes">
            <div class="card-header">
                <i class="fas fa-exclamation-triangle card-icon"></i>
                <h5 class="card-title">
                    Órdenes sin Integrar en Tango
                    <i class="fas fa-info-circle info-icon" 
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="Órdenes pendientes de integración al sistema tango">
                    </i>
                </h5>
            </div>
            <div class="card-body">
                <?php if ($ordenesSinIntegrar && !empty($ordenesSinIntegrar->CANT_ORDENES)): ?>
                    <p class="card-value"><?php echo htmlspecialchars($ordenesSinIntegrar->CANT_ORDENES); ?></p>
                    <p class="mb-0">Total: $<?php echo number_format($ordenesSinIntegrar->TOTAL_ORDEN, 2); ?></p>
                    <p class="date-info">Desde: <?php echo $ordenesSinIntegrar->FECHA_ORDEN->format('d/m/Y H:i'); ?></p>
                    <button type="button" class="btn btn-outline-warning w-100" data-bs-toggle="modal" data-bs-target="#modalOrdenesSinIntegrar">
                        <i class="fas fa-list-ul me-2"></i>Ver Detalle
                    </button>
                <?php else: ?>
                    <p class="no-data">Sin órdenes pendientes</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Card para Productos ML Full -->
    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card card-ml">
            <div class="card-header">
                <i class="fas fa-shopping-bag card-icon"></i>
                <h5 class="card-title">
                    Productos Pausados en ML Normal
                    <i class="fas fa-info-circle info-icon" 
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="Productos que tienen stock en Central pero están pausados en Mercado Libre">
                    </i>
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
</div>
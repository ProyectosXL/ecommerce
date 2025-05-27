
<!-- includes/header.php -->
<div class="alert alert-info p-2">
    <div class="d-flex justify-content-between align-items-center">
        <button type="button" class="btn btn-outline-primary btn-sm" onclick="window.location.reload()">
            <i class="fas fa-sync-alt me-2"></i>Actualizar
        </button>
        
        <h2 class="mb-0 text-center flex-grow-1">
            <i class="fas fa-chart-line"></i> Tablero de Control Ecommerce
        </h2>
        
        <small class="text-muted">
            <i class="fas fa-clock"></i> 
            Última actualización: <?php echo $ultimaActualizacion->format('d/m/Y H:i:s'); ?>
        </small>
    </div>
</div>

<!-- Formulario de búsqueda -->
<form method="POST" class="search-container mb-4">
    <div class="row align-items-center">
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-calendar"></i>
                </span>
                <input type="date" name="desde" class="form-control" 
                    value="<?php echo isset($_POST['desde']) ? $_POST['desde'] : date('Y-m-d', strtotime('-30 days')); ?>"
                    max="<?php echo date('Y-m-d'); ?>"
                    required>
            </div>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-calendar"></i>
                </span>
                <input type="date" name="hasta" class="form-control" 
                    value="<?php echo isset($_POST['hasta']) ? $_POST['hasta'] : date('Y-m-d'); ?>"
                    max="<?php echo date('Y-m-d'); ?>"
                    required>
            </div>
        </div>
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" name="numero" class="form-control" 
                    placeholder="Ingrese número de Orden, Pedido o Factura" 
                    value="<?php echo isset($_POST['numero']) ? htmlspecialchars($_POST['numero']) : ''; ?>"
                    required>
            </div>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-search me-2"></i>Buscar
            </button>
        </div>
        <div id="spinner" class="spinner-wrapper spinner-hidden">
            <div class="spinner-dots">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </div>
</form>
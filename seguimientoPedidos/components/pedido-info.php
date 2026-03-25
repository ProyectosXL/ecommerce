
<!-- Información del Pedido -->
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="info-label">Fecha y Hora</div>
                <div class="info-value" id="fechaHora">
                    <?php 
                    echo $pedido->FECHA_PEDIDO instanceof DateTime ? 
                        $pedido->FECHA_PEDIDO->format('d/m/Y') : date('d/m/Y', strtotime($pedido->FECHA_PEDIDO));
                    echo ' ' . $pedido->HORA;
                    ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-label">Marketplace</div>
                <div class="info-value"><?php echo $pedido->MARKETPLACE ?? $pedido->ORIGEN ?? 'N/A'; ?></div>
            </div>
            <div class="col-md-4">
                <div class="info-label">Nro. Pedido</div>
                <div class="info-value d-flex align-items-center" id="nroPedido">
                    <?php echo $pedido->NRO_PEDIDO; ?>
                    <?php if (($pedido->CANCELADO ?? 0) == 1): 
                        $tooltipText = "Pedido Cancelado";
                        if (isset($pedido->NCR) && !empty($pedido->NCR)) {
                            $tooltipText .= " - NCR " . $pedido->NCR;
                        }
                    ?>
                        <i class="bi bi-x-circle-fill ms-2 text-danger icon-state" 
                        data-bs-toggle="tooltip" 
                        title="<?php echo $tooltipText; ?>">
                        </i>
                    <?php endif; ?>
                    <?php if (($pedido->FALTANTE_RESUELTO ?? 0) == 1): ?>
                        <i class="fas fa-check-circle ms-2 text-success icon-state" data-bs-toggle="tooltip" title="Faltante resuelto"></i>
                    <?php elseif (($pedido->INCOMPLETO ?? 0) == 1): ?>
                        <i class="fas fa-exclamation-triangle ms-2 text-warning icon-state" data-bs-toggle="tooltip" title="Pedido Incompleto"></i>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-label">Nro. Orden</div>
                <div class="info-value" id="nroOrden"><?php echo $pedido->NRO_ORDEN ?? $pedido->ORDER_ID_TIENDA ?? ''; ?></div>
            </div>
            <div class="col-md-4">
                <div class="info-label">Nro. Factura</div>
                <div class="info-value"><?php echo $pedido->FACTURA; ?></div>
            </div>
            <div class="col-md-4">
                <div class="info-label">Cliente</div>
                <div class="info-value" id="cliente"><?php echo $pedido->CLIENTE ?? $pedido->RAZON_SOCI ?? 'N/A'; ?></div>
            </div>
            <div class="col-md-4">
                <div class="info-label">Dirección de Entrega</div>
                <div class="info-value"><?php echo $pedido->DIRECCION_ENTREGA ?? $pedido->DEPARTAMENTO ?? 'N/A'; ?></div>
            </div>
            <div class="col-md-4">
                <div class="info-label">Lugar de Entrega</div>
                <div class="info-value"><?php echo $pedido->LUGAR_ENTREGA ?? $pedido->DEPARTAMENTO ?? 'N/A'; ?></div>
            </div>
            <div class="col-md-4">
                <div class="info-label">Prepara</div>
                <div class="info-value" id="prepara"><?php echo $pedido->PREPARA ?? $pedido->WAREHOUSE ?? 'N/A'; ?></div>
            </div>
            <div class="col-md-4">
                <div class="info-label">Método de Envío</div>
                <div class="info-value"><?php echo $pedido->METODO_ENVIO ?? 'N/A'; ?></div>
            </div>
            <div class="col-md-4">
                <div class="info-label">Sucursal Entrega</div>
                <div class="info-value" id="warehouse"><?php echo $pedido->SUCURSAL_ENTREGA ?? $pedido->WAREHOUSE ?? 'N/A'; ?></div>
            </div>
        </div>
    </div>
</div>
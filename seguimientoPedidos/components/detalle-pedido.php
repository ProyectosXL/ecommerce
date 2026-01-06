
<!-- Detalle del Pedido -->
<div class="card mt-4">
    <div class="card-body">
        <h5 class="card-title mb-4">Detalle del Pedido</h5>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 35%">Producto</th>
                        <th style="width: 15%" class="text-end">Precio</th>
                        <th style="width: 10%" class="text-end">Cant.</th>
                        <th style="width: 15%" class="text-end">Total</th>
                        <th style="width: 15%" class="text-center">Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    
                    // Obtener devoluciones del pedido
                    $devolucionesArray = $pedidos->obtenerDevoluciones($pedido->NRO_PEDIDO);
                    $devolucionesPorArticulo = [];
                    $devolucionesPorArticuloBase = []; // Usando código base para mejor matching
                    
                    foreach ($devolucionesArray as $dev) {
                        // Por código exacto
                        if (!isset($devolucionesPorArticulo[$dev->COD_ARTICU])) {
                            $devolucionesPorArticulo[$dev->COD_ARTICU] = [];
                        }
                        $devolucionesPorArticulo[$dev->COD_ARTICU][] = $dev;
                        
                        // Por código base (primeros 13 caracteres)
                        if (!isset($devolucionesPorArticuloBase[$dev->COD_ARTICU_BASE])) {
                            $devolucionesPorArticuloBase[$dev->COD_ARTICU_BASE] = [];
                        }
                        $devolucionesPorArticuloBase[$dev->COD_ARTICU_BASE][] = $dev;
                    }
                    
                    $detalles = $pedidos->buscarDetallePedido($desde, $hasta, $numero);
                    if ($detalles) {
                        foreach($detalles as $detalle) {
                            $item = $detalle[0];
                            $subtotal = $item->CANT_PEDID * $item->IMPORTE;
                            $total += $subtotal;

                            // Lógica para las imágenes
                            $imageName = substr($item->COD_ARTICU, 0, 13);
                            $imageUrl = file_exists("../../Imagenes/".$imageName.".jpg") ? 
                                    "../../Imagenes/".$imageName.".jpg" : "";
                            
                            // Verificar si es SALE
                            $isSale = (substr($item->DESCRIPCIO, -11) == '-- SALE! --');
                            $description = $isSale ? substr($item->DESCRIPCIO, 0, -11) : $item->DESCRIPCIO;
                            
                            // Verificar si el artículo tiene devolución/reintegro
                            // Intentar match exacto primero, luego por código base
                            $codigoBase = substr($item->COD_ARTICU, 0, 13);
                            $tieneDevolucion = isset($devolucionesPorArticulo[$item->COD_ARTICU]) || 
                                              isset($devolucionesPorArticuloBase[$codigoBase]);
                            
                            $cantidadDevuelta = 0;
                            $cantidadNcr = 0;
                            $detallesDev = [];
                            
                            if ($tieneDevolucion) {
                                // Buscar por código exacto
                                if (isset($devolucionesPorArticulo[$item->COD_ARTICU])) {
                                    foreach ($devolucionesPorArticulo[$item->COD_ARTICU] as $dev) {
                                        $cantidadDevuelta += $dev->CANTIDAD;
                                        // Contar NCRs emitidas (no pendientes)
                                        if ($dev->TIPO == 'NCR' || $dev->ESTADO == 'NCR_EMITIDA' || $dev->ESTADO == 'EMITIDA') {
                                            $cantidadNcr += $dev->CANTIDAD;
                                        }
                                        $detallesDev[] = $dev;
                                    }
                                }
                                // Buscar por código base si no encontró match exacto
                                if ($cantidadDevuelta == 0 && isset($devolucionesPorArticuloBase[$codigoBase])) {
                                    foreach ($devolucionesPorArticuloBase[$codigoBase] as $dev) {
                                        $cantidadDevuelta += $dev->CANTIDAD;
                                        // Contar NCRs emitidas (no pendientes)
                                        if ($dev->TIPO == 'NCR' || $dev->ESTADO == 'NCR_EMITIDA' || $dev->ESTADO == 'EMITIDA') {
                                            $cantidadNcr += $dev->CANTIDAD;
                                        }
                                        $detallesDev[] = $dev;
                                    }
                                }
                            }
                            
                            $tieneNcrPendiente = ($cantidadDevuelta > 0 && $cantidadNcr < $cantidadDevuelta);
                    ?>
                    <tr class="<?php echo $item->FALTANTE == 1 ? 'faltante-row' : ''; ?>">
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="<?php echo $imageUrl ? $imageUrl : '/api/placeholder/50/50'; ?>" 
                                    alt="<?php echo $imageUrl ? $item->COD_ARTICU : 'Sin imagen'; ?>"
                                    class="product-image me-3"
                                    style="width: 50px; height: 50px; object-fit: contain;">
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-primary d-flex align-items-center">
                                        <?php echo $description; ?>
                                        <?php if ($isSale): ?>
                                            <span class="badge bg-danger ms-2">SALE</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-muted">
                                        <small>Código: <?php echo $item->COD_ARTICU; ?></small>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="text-end">$ <?php echo number_format($item->IMPORTE, 2, ',', '.'); ?></td>
                        <td class="text-end"><?php echo $item->CANT_PEDID; ?></td>
                        <td class="text-end">$ <?php echo number_format($subtotal, 2, ',', '.'); ?></td>
                        <td class="text-center">
                            <?php if ($tieneDevolucion): ?>
                                <div class="d-flex flex-column gap-1 align-items-center">
                                    <span class="badge <?php echo $tieneNcrPendiente ? 'bg-danger' : 'bg-warning text-dark'; ?>" 
                                          data-bs-toggle="tooltip" 
                                          title="<?php echo $cantidadDevuelta; ?> unidad(es) con reintegro - <?php echo $cantidadNcr; ?> con NCR emitida">
                                        <i class="fas fa-undo me-1"></i>Reintegro (<?php echo $cantidadDevuelta; ?>)
                                    </span>
                                    <?php if ($tieneNcrPendiente): ?>
                                        <span class="badge bg-danger" data-bs-toggle="tooltip" title="Falta emitir NCR">
                                            <i class="fas fa-exclamation-circle me-1"></i>NCR Pendiente
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-success" data-bs-toggle="tooltip" title="NCR emitida">
                                            <i class="fas fa-check-circle me-1"></i>NCR OK
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php elseif ($item->FALTANTE == 1): ?>
                                <span class="badge bg-danger">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Faltante
                                </span>
                            <?php elseif (isset($pedido->REINTEGRADO) && $pedido->REINTEGRADO == 1): ?>
                                <span class="badge bg-danger" data-bs-toggle="tooltip" 
                                      title="Pedido cancelado - Reintegro realizado<?php echo (isset($pedido->NCR) && !empty($pedido->NCR)) ? ' - NCR: ' . $pedido->NCR : ' - NCR pendiente'; ?>">
                                    <i class="fas fa-times-circle me-1"></i>Cancelado
                                </span>
                            <?php else: ?>
                                <span class="badge bg-success" data-bs-toggle="tooltip" 
                                      title="Artículo sin inconvenientes">
                                    <i class="fas fa-check me-1"></i>Normal
                                </span>
                            <?php endif; ?>
                        </td>
                        <?php if ($item->FALTANTE == 1): ?>
                        <td>
                            <button type="button" class="btn btn-outline-danger btn ms-2" 
                                onclick="abrirHistorial('<?php echo htmlspecialchars($item->COD_ARTICU); ?>', 
                                                        '<?php echo htmlspecialchars($description); ?>', 
                                                        '<?php echo $item->IMPORTE; ?>', 
                                                        '<?php echo $item->CANT_PEDID; ?>')">
                                <i class="fas fa-history"></i> Ver Historial
                            </button>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php
                        }
                    ?>
                    <tr>
                        <td colspan="3" class="text-end fw-bold">Total</td>
                        <td class="text-end fw-bold">$ <?php echo number_format($total, 2, ',', '.'); ?></td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modales para las imágenes -->
<?php
if ($detalles) {
    foreach($detalles as $detalle) {
        $item = $detalle[0];
        $imageName = substr($item->COD_ARTICU, 0, 13);
        $imageUrl = file_exists("../../Imagenes/".$imageName.".jpg") ? 
                "../../Imagenes/".$imageName.".jpg" : "";
?>
<div class="modal fade" id="imageModal<?php echo $imageName; ?>" tabindex="-1" 
    aria-labelledby="imageModalLabel<?php echo $imageName; ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel<?php echo $imageName; ?>">
                    <?php echo $item->DESCRIPCIO; ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="<?php echo $imageUrl; ?>" 
                    alt="<?php echo $imageName; ?>.jpg - imagen no encontrada" 
                    class="img-fluid">
            </div>
        </div>
    </div>
</div>
<?php
    }
}
?>
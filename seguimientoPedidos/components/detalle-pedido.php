
<!-- Detalle del Pedido -->
<div class="card mt-4">
    <div class="card-body">
        <h5 class="card-title mb-4">Detalle del Pedido</h5>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 35%">Producto</th>
                        <th style="width: 20%" class="text-end">Precio</th>
                        <th style="width: 10%" class="text-end">Cant.</th>
                        <th style="width: 20%" class="text-end">Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
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
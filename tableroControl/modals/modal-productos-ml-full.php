
<!-- Modal para el detalle de productos ML Full -->
<div class="modal fade" id="modalProductosMlFull" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title">
                    <i class="fas fa-shopping-bag"></i> Detalle de Productos Pausados en ML Full
                </h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" onclick="exportToExcelMlFull()">
                        <i class="fas fa-file-excel me-2"></i>Exportar
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="tablaProductosMlFull">
                        <thead class="table-light">
                            <tr>
                                <!-- Fila de filtros -->
                                <th><input type="text" class="form-control form-control-sm column-filter" placeholder="Código"></th>
                                <th><input type="text" class="form-control form-control-sm column-filter" placeholder="Descripción"></th>
                                <th><input type="text" class="form-control form-control-sm column-filter" placeholder="Stock"></th>
                                <th><input type="text" class="form-control form-control-sm column-filter" placeholder="Estado"></th>
                                <th colspan="2">Enlaces</th>
                            </tr>
                            <tr>
                                <th>Código</th>
                                <th>Descripción</th>
                                <th class="text-end">Stock Central</th>
                                <th>Estado Full</th>
                                <th>Link Full</th>
                                <th>Link Central</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $detalleProductos = $control->traerDetalleProductosMlFull();
                            if (!empty($detalleProductos)):
                                foreach ($detalleProductos as $detalle): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($detalle->CODIGO); ?></td>
                                        <td><?php echo htmlspecialchars($detalle->DESCRIPCIO); ?></td>
                                        <td class="text-end"><?php echo number_format($detalle->STOCK_CENTRAL, 0); ?></td>
                                        <td><?php echo htmlspecialchars($detalle->ESTADO_FULL); ?></td>
                                        <td>
                                            <?php if (!empty($detalle->URL_FULL)): ?>
                                                <a href="<?php echo htmlspecialchars($detalle->URL_FULL); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($detalle->URL_CENTRAL)): ?>
                                                <a href="<?php echo htmlspecialchars($detalle->URL_CENTRAL); ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach;
                            else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No hay productos para mostrar</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
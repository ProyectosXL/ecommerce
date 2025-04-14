
    <!-- Modal para el detalle de Ordenes Pend. Cierre -->
    <div class="modal fade" id="modalOrdenesPendientesCierre" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h5 class="modal-title">
                        <i class="fas fa-hourglass-half"></i> Detalle de Órdenes Pendientes de Cierre
                    </h5>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-success" onclick="exportToExcelOrdenesCierre()">
                            <i class="fas fa-file-excel me-2"></i>Exportar
                        </button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Order ID</th>
                                    <th>Cliente</th>
                                    <th>Sucursal</th>
                                    <th class="text-end">Días de Antigüedad</th>
                                    <th></th> <!-- Nueva columna para el ícono -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $detalleOrdenesCierre = $control->traerDetalleOrdenesPendientesCierre();
                                if (!empty($detalleOrdenesCierre)):
                                    foreach ($detalleOrdenesCierre as $detalle): 
                                        $excedeDias = $detalle->DIAS_ANTIGUEDAD > 10;
                                        ?>
                                        <tr class="<?php echo $excedeDias ? 'text-danger' : ''; ?>">
                                            <td><?php echo $detalle->FECHA->format('d/m/Y H:i'); ?></td>
                                            <td><?php echo htmlspecialchars($detalle->ORDER_ID); ?></td>
                                            <td><?php echo htmlspecialchars($detalle->CLIENTE); ?></td>
                                            <td><?php echo htmlspecialchars($detalle->SUCURSAL); ?></td>
                                            <td class="text-end"><?php echo number_format($detalle->DIAS_ANTIGUEDAD, 0); ?></td>
                                            <td class="text-center">
                                                <?php if ($excedeDias): ?>
                                                    <i class="fas fa-exclamation-circle text-danger" 
                                                    data-bs-toggle="tooltip" 
                                                    title="Excede los 10 días"></i>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach;
                                else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No hay datos para mostrar</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
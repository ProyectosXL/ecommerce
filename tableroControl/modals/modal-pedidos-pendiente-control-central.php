<!-- Modal para el detalle de Pedidos Pendientes de Control Central -->
<div class="modal fade" id="modalPedidosPendientesControlCentral" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title">
                    <i class="fas fa-search"></i> Detalle de Pedidos Pendientes de Control Central
                </h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" onclick="exportToExcelPedidosControlCentral()">
                        <i class="fas fa-file-excel me-2"></i>Exportar
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Pendiente implementación de consulta separada para Central</strong><br>
                    Actualmente muestra los mismos datos que el modal general hasta que se implemente la consulta específica para Central.
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="tablaPedidosControlCentral">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha Sincronizado</th>
                                <th>Canal</th>
                                <th>Nro. Pedido</th>
                                <th>Order ID</th>
                                <th>Cliente</th>
                                <th>Sucursal Prepara</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // TODO: Implementar consulta específica para Central
                            // Por ahora usar la consulta general filtrada
                            $detallePedidosControlCentral = $control->traerDetallePedidosPendientesControl();
                            if (!empty($detallePedidosControlCentral)):
                                foreach ($detallePedidosControlCentral as $detalle): 
                                    // Filtrar solo central (código sucursal 001 o similar)
                                    if ($detalle->SUCURSAL_PREPARA == '001' || strpos($detalle->SUCURSAL_PREPARA, 'CENTRAL') !== false): ?>
                                    <tr>
                                        <td><?php echo $detalle->FECHA_SINCRONIZADO->format('d/m/Y H:i'); ?></td>
                                        <td><?php echo htmlspecialchars($detalle->CANAL); ?></td>
                                        <td><?php echo htmlspecialchars($detalle->NRO_PEDIDO); ?></td>
                                        <td><?php echo htmlspecialchars($detalle->ORDER_ID); ?></td>
                                        <td><?php echo htmlspecialchars($detalle->CLIENTE); ?></td>
                                        <td><?php echo htmlspecialchars($detalle->SUCURSAL_PREPARA); ?></td>
                                    </tr>
                                    <?php endif;
                                endforeach;
                            else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No hay pedidos pendientes</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
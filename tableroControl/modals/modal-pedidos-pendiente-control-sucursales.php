<!-- Modal para el detalle de Pedidos Pendientes de Control Sucursales -->
<div class="modal fade" id="modalPedidosPendientesControlSucursales" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title">
                    <i class="fas fa-search"></i> Detalle de Pedidos Pendientes de Control Sucursales
                </h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" onclick="exportToExcelPedidosControlSucursales()">
                        <i class="fas fa-file-excel me-2"></i>Exportar
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Pendiente implementación de consulta separada para Sucursales</strong><br>
                    Actualmente muestra los mismos datos que el modal general hasta que se implemente la consulta específica para Sucursales.
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="tablaPedidosControlSucursales">
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
                            // TODO: Implementar consulta específica para Sucursales
                            // Por ahora usar la consulta general filtrada
                            $detallePedidosControlSucursales = $control->traerDetallePedidosPendientesControl();
                            if (!empty($detallePedidosControlSucursales)):
                                foreach ($detallePedidosControlSucursales as $detalle): 
                                    // Filtrar solo sucursales (excluir central)
                                    if ($detalle->SUCURSAL_PREPARA != '001' && strpos($detalle->SUCURSAL_PREPARA, 'CENTRAL') === false): ?>
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
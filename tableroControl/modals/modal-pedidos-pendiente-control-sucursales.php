<!-- Modal para el detalle de Pedidos Pendientes de Control Sucursales -->
<div class="modal fade" id="modalPedidosPendientesControlSucursales" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title">
                    <i class="fas fa-search"></i> Detalle de Pedidos Pendientes de Control Sucursales (Hasta Ayer)
                </h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" onclick="exportToExcelPedidosControlSucursales()">
                        <i class="fas fa-file-excel me-2"></i>Exportar
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
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
                            // Usar método específico para sucursales
                            $detallePedidosControlSucursales = $control->traerDetallePedidosPendientesControlSucursales();
                            
                            if (!empty($detallePedidosControlSucursales)):
                                foreach ($detallePedidosControlSucursales as $detalle): ?>
                                    <tr>
                                        <td><?php echo $detalle->FECHA_SINCRONIZADO->format('d/m/Y H:i'); ?></td>
                                        <td><?php echo htmlspecialchars($detalle->CANAL); ?></td>
                                        <td><?php echo htmlspecialchars($detalle->NRO_PEDIDO); ?></td>
                                        <td><?php echo htmlspecialchars($detalle->ORDER_ID); ?></td>
                                        <td><?php echo htmlspecialchars($detalle->CLIENTE); ?></td>
                                        <td><?php echo htmlspecialchars($detalle->SUCURSAL_PREPARA); ?></td>
                                    </tr>
                                <?php endforeach;
                            else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No hay pedidos pendientes de sucursales</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
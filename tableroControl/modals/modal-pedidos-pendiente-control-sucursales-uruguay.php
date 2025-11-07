
<!-- Modal para el detalle de Pedidos Pendientes de Control Sucursales - Uruguay -->
<div class="modal fade" id="modalPedidosPendientesControlSucursalesUruguay" tabindex="-1" aria-labelledby="modalPedidosPendientesControlSucursalesUruguayLabel">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title" id="modalPedidosPendientesControlSucursalesUruguayLabel">
                    <i class="fas fa-clipboard-check"></i> Detalle de Pedidos Pendientes de Control - Sucursales Uruguay
                </h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" onclick="exportToExcelPedidosControlSucursalesUruguay()">
                        <i class="fas fa-file-excel me-2"></i>Exportar
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="tablePedidosControlSucursalesUruguay" class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha Sincronizado</th>
                                <th>Canal</th>
                                <th>Nro. Pedido</th>
                                <th>Order ID</th>
                                <th>Cliente</th>
                                <th>Sucursal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            try {
                                $detallePedidosControl = $control->traerDetallePedidosPendientesControlSucursalesUruguay();
                                if (!empty($detallePedidosControl)):
                                    foreach ($detallePedidosControl as $detalle): ?>
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
                                        <td colspan="6" class="text-center">No hay pedidos pendientes de control</td>
                                    </tr>
                                <?php endif;
                            } catch (Exception $e) { ?>
                                <tr>
                                    <td colspan="6" class="text-center text-danger">
                                        Error al cargar los datos: <?php echo htmlspecialchars($e->getMessage()); ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
function exportToExcelPedidosControlSucursalesUruguay() {
    const table = document.getElementById('tablePedidosControlSucursalesUruguay');
    const wb = XLSX.utils.table_to_book(table, {sheet: "Pedidos Control Sucursales UY"});
    XLSX.writeFile(wb, 'pedidos_pendientes_control_sucursales_uruguay.xlsx');
}
</script>

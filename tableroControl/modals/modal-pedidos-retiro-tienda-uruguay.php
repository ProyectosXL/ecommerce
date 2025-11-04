
<!-- Modal para el detalle de Pedidos Pendientes de Retiro - Uruguay -->
<div class="modal fade" id="modalPedidosRetiroTiendaUruguay" tabindex="-1" aria-labelledby="modalPedidosRetiroTiendaUruguayLabel">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title" id="modalPedidosRetiroTiendaUruguayLabel">
                    <i class="fas fa-store-alt"></i> Detalle de Pedidos Pendientes de Retiro - Uruguay
                </h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" onclick="exportToExcelPedidosRetiroTiendaUruguay()">
                        <i class="fas fa-file-excel me-2"></i>Exportar
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="tablePedidosRetiroTiendaUruguay" class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Cod. Sucursal</th>
                                <th>Fecha Pedido</th>
                                <th>Nro. Pedido</th>
                                <th>Order ID</th>
                                <th>Cliente</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">Días Pendiente</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $detallePedidosRetiro = $control->traerDetallePedidosRetiroTiendaUruguay();
                            if (!empty($detallePedidosRetiro)):
                                foreach ($detallePedidosRetiro as $detalle): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($detalle->COD_SUCURS); ?></td>
                                        <td><?php echo $detalle->FECHA_HORA->format('d/m/Y H:i'); ?></td>
                                        <td><?php echo htmlspecialchars($detalle->NRO_PEDIDO); ?></td>
                                        <td><?php echo htmlspecialchars($detalle->ORDER_ID_TIENDA); ?></td>
                                        <td><?php echo htmlspecialchars($detalle->CLIENTE); ?></td>
                                        <td class="text-end">$<?php echo number_format($detalle->TOTAL_PEDI, 2); ?></td>
                                        <td class="text-end"><?php echo htmlspecialchars($detalle->DIAS_PENDIENTE); ?></td>
                                    </tr>
                                <?php endforeach;
                            else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No hay pedidos pendientes de retiro</td>
                                </tr>
                            <?php endif; ?>
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
function exportToExcelPedidosRetiroTiendaUruguay() {
    const table = document.getElementById('tablePedidosRetiroTiendaUruguay');
    const wb = XLSX.utils.table_to_book(table, {sheet: "Pedidos Retiro Tienda UY"});
    XLSX.writeFile(wb, 'pedidos_retiro_tienda_uruguay.xlsx');
}
</script>

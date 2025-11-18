
            <!-- Modal para el detalle de Pedidos Incompletos Central -->
            <div class="modal fade" id="modalPedidosIncompletosCentral" tabindex="-1" aria-labelledby="modalPedidosIncompletosCentralLabel">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                    <div class="modal-header d-flex justify-content-between align-items-center">
                        <h5 class="modal-title" id="modalPedidosIncompletosCentralLabel">
                            <i class="fas fa-exclamation-triangle"></i> Detalle de Pedidos Incompletos - Central
                        </h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-success" onclick="exportToExcelPedidosIncompletosCentral()">
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
                                            <th>Origen</th>
                                            <th>Nro. Orden Ecommerce</th>
                                            <th>Nro. Pedido</th>
                                            <th>Fecha Pedido</th>
                                            <th>Cliente</th>
                                            <th>Código Artículo</th>
                                            <th>Descripción</th>
                                            <th class="text-end">Cant. Pedida</th>
                                            <th class="text-end">Cant. Auditada</th>
                                            <th>Método Envío</th>
                                            <th>Tienda</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $detalleIncompletos = $control->traerDetallePedidosIncompletosCentral();
                                        if (!empty($detalleIncompletos)):
                                            foreach ($detalleIncompletos as $detalle): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($detalle->ORIGEN); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->NRO_ORDEN_ECOMMERCE); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->NRO_PEDIDO); ?></td>
                                                    <td><?php echo $detalle->FECHA_PEDID->format('d/m/Y'); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->CLIENTE); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->COD_ARTICU); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->DESCRIPCIO); ?></td>
                                                    <td class="text-end"><?php echo htmlspecialchars($detalle->CANT_PEDID); ?></td>
                                                    <td class="text-end"><?php echo htmlspecialchars($detalle->CANT_AUDITADO); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->METODO_ENVIO); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->TIENDA); ?></td>
                                                </tr>
                                            <?php endforeach;
                                        else: ?>
                                            <tr>
                                                <td colspan="11" class="text-center">No hay pedidos incompletos</td>
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

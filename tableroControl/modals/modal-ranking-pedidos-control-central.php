<!-- Modal para el ranking de Pedidos Pendientes de Control Central -->
<div class="modal fade" id="modalRankingPedidosControlCentral" tabindex="-1" aria-labelledby="modalRankingPedidosControlCentralLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRankingPedidosControlCentralLabel">
                    <i class="fas fa-trophy"></i> Ranking de Pedidos Pendientes de Control Central
                </h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" onclick="exportToExcelRankingControlCentral()">
                        <i class="fas fa-file-excel me-2"></i>Exportar
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Pendiente implementación de consulta separada para Central</strong><br>
                    Actualmente muestra datos simulados hasta que se implemente la consulta específica.
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Ranking basado en pedidos hasta ayer sin controlar - Solo Central</strong>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="tablaRankingControlCentral">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center" style="width: 80px;">#</th>
                                <th>Departamento Central</th>
                                <th class="text-center" style="width: 120px;">Cantidad</th>
                                <th class="text-center" style="width: 100px;">%</th>
                                <th style="width: 200px;">Progreso</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // TODO: Implementar consulta específica para ranking de Central
                            // Por ahora mostrar datos simulados
                            $rankingCentral = [
                                (object)['DEPARTAMENTO' => 'Picking', 'CANTIDAD_PEDIDOS' => 15, 'PORCENTAJE' => 60.0],
                                (object)['DEPARTAMENTO' => 'Packing', 'CANTIDAD_PEDIDOS' => 8, 'PORCENTAJE' => 32.0],
                                (object)['DEPARTAMENTO' => 'Despacho', 'CANTIDAD_PEDIDOS' => 2, 'PORCENTAJE' => 8.0]
                            ];
                            
                            if (!empty($rankingCentral)):
                                $posicion = 1;
                                $maxCantidad = $rankingCentral[0]->CANTIDAD_PEDIDOS;
                                
                                foreach ($rankingCentral as $ranking):
                                    $progressWidth = ($ranking->CANTIDAD_PEDIDOS / $maxCantidad) * 100;
                                    
                                    $progressColor = '';
                                    if ($posicion == 1) {
                                        $progressColor = 'bg-danger';
                                    } elseif ($posicion <= 3) {
                                        $progressColor = 'bg-warning';
                                    } else {
                                        $progressColor = 'bg-success';
                                    }
                                    
                                    $icono = '';
                                    if ($posicion == 1) {
                                        $icono = '<i class="fas fa-crown text-warning"></i>';
                                    } elseif ($posicion == 2) {
                                        $icono = '<i class="fas fa-medal text-secondary"></i>';
                                    } elseif ($posicion == 3) {
                                        $icono = '<i class="fas fa-award text-warning"></i>';
                                    } else {
                                        $icono = $posicion;
                                    }
                                    ?>
                                    <tr>
                                        <td class="text-center fw-bold">
                                            <?php echo $icono; ?>
                                        </td>
                                        <td class="fw-semibold"><?php echo htmlspecialchars($ranking->DEPARTAMENTO); ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-primary fs-6"><?php echo number_format($ranking->CANTIDAD_PEDIDOS, 0); ?></span>
                                        </td>
                                        <td class="text-center">
                                            <span class="fw-bold"><?php echo number_format($ranking->PORCENTAJE, 1); ?>%</span>
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 25px;">
                                                <div class="progress-bar <?php echo $progressColor; ?> progress-bar-striped" 
                                                     role="progressbar" 
                                                     style="width: <?php echo $progressWidth; ?>%;" 
                                                     aria-valuenow="<?php echo $ranking->CANTIDAD_PEDIDOS; ?>" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="<?php echo $maxCantidad; ?>">
                                                    <?php if ($progressWidth > 20): ?>
                                                        <?php echo number_format($ranking->PORCENTAJE, 1); ?>%
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php 
                                    $posicion++;
                                endforeach;
                            else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No hay datos para mostrar</td>
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
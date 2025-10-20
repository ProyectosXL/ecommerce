<!-- Modal para el ranking de Pedidos Pendientes de Control Sucursales -->
<div class="modal fade" id="modalRankingPedidosControlSucursales" tabindex="-1" aria-labelledby="modalRankingPedidosControlSucursalesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRankingPedidosControlSucursalesLabel">
                    <i class="fas fa-trophy"></i> Ranking de Pedidos Pendientes de Control por Sucursal (Hasta Ayer)
                </h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" onclick="exportToExcelRankingControlSucursales()">
                        <i class="fas fa-file-excel me-2"></i>Exportar
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Ranking basado en pedidos sin controlar hasta ayer - Solo Sucursales (excluye Central)</strong>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="tablaRankingControlSucursales">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center" style="width: 80px;">#</th>
                                <th>Sucursal</th>
                                <th class="text-center" style="width: 120px;">Cantidad</th>
                                <th class="text-center" style="width: 100px;">%</th>
                                <th style="width: 200px;">Progreso</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Usar método específico para sucursales y generar ranking
                            $detallePedidosControlSucursales = $control->traerDetallePedidosPendientesControlSucursales();
                            
                            $rankingSucursales = [];
                            if (!empty($detallePedidosControlSucursales)):
                                $contadorPorSucursal = [];
                                
                                foreach ($detallePedidosControlSucursales as $detalle):
                                    $sucursal = $detalle->SUCURSAL_PREPARA;
                                    if (!isset($contadorPorSucursal[$sucursal])):
                                        $contadorPorSucursal[$sucursal] = 0;
                                    endif;
                                    $contadorPorSucursal[$sucursal]++;
                                endforeach;
                                
                                // Convertir a objetos y ordenar
                                foreach ($contadorPorSucursal as $sucursal => $cantidad):
                                    $rankingSucursales[] = (object)[
                                        'SUCURSAL' => $sucursal,
                                        'CANTIDAD_PEDIDOS' => $cantidad
                                    ];
                                endforeach;
                                
                                // Ordenar por cantidad descendente
                                usort($rankingSucursales, function($a, $b) {
                                    return $b->CANTIDAD_PEDIDOS - $a->CANTIDAD_PEDIDOS;
                                });
                                
                                // Calcular porcentajes
                                $totalPedidos = array_sum(array_column($rankingSucursales, 'CANTIDAD_PEDIDOS'));
                                foreach ($rankingSucursales as $ranking):
                                    $ranking->PORCENTAJE = $totalPedidos > 0 ? ($ranking->CANTIDAD_PEDIDOS / $totalPedidos) * 100 : 0;
                                endforeach;
                            endif;
                            
                            if (!empty($rankingSucursales)):
                                $posicion = 1;
                                $maxCantidad = $rankingSucursales[0]->CANTIDAD_PEDIDOS;
                                
                                foreach ($rankingSucursales as $ranking):
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
                                        <td class="fw-semibold"><?php echo htmlspecialchars($ranking->SUCURSAL); ?></td>
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
                
                <?php if (!empty($rankingSucursales)): ?>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="card border-primary">
                            <div class="card-body text-center">
                                <h6 class="card-title text-primary">Total Sucursales</h6>
                                <h4 class="text-primary"><?php echo array_sum(array_column($rankingSucursales, 'CANTIDAD_PEDIDOS')); ?></h4>
                                <small class="text-muted">pedidos pendientes</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-warning">
                            <div class="card-body text-center">
                                <h6 class="card-title text-warning">Sucursales Afectadas</h6>
                                <h4 class="text-warning"><?php echo count($rankingSucursales); ?></h4>
                                <small class="text-muted">con pedidos pendientes</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-danger">
                            <div class="card-body text-center">
                                <h6 class="card-title text-danger">Mayor Concentración</h6>
                                <h4 class="text-danger"><?php echo number_format($rankingSucursales[0]->PORCENTAJE, 1); ?>%</h4>
                                <small class="text-muted"><?php echo htmlspecialchars($rankingSucursales[0]->SUCURSAL); ?></small>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
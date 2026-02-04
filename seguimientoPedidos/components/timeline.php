
<!-- Timeline de Estados -->
<div class="card">
    <div class="card-body">
        <h5 class="card-title mb-4">Estado del Pedido</h5>
        <div class="timeline">
            <div class="row timeline-row">
                <div class="col timeline-step">
                    <div class="timeline-icon <?php echo ($pedido->SINCRONIZADO ?? 0) ? 'active' : ''; ?>">
                        <i class="fas fa-sync-alt icon"></i>
                    </div>
                    <div>Sincronizado</div>
                    <div class="timeline-date">
                        <?php 
                        echo ($pedido->FECHA_SINCRONIZADO ?? null) ? 
                            (($pedido->FECHA_SINCRONIZADO ?? null) instanceof DateTime ? 
                                $pedido->FECHA_SINCRONIZADO->format('d/m/Y H:i') : 
                                date('d/m/Y H:i', strtotime($pedido->FECHA_SINCRONIZADO))) : 
                            'Pendiente'; 
                        ?>
                    </div>
                </div>
                <?php if (($pedido->PREPARA ?? $pedido->WAREHOUSE ?? '') == 'CENTRAL'): ?>
                <div class="col timeline-step">
                    <div class="timeline-icon <?php echo ($pedido->PREPARADO ?? 0) ? 'active' : ''; ?>">
                        <i class="bi bi-cart-check-fill icon"></i>
                    </div>
                    <div>Preparado</div>
                    <div class="timeline-date">
                        <?php 
                        echo ($pedido->FECHA_PREPARADO ?? null) ? 
                            (($pedido->FECHA_PREPARADO ?? null) instanceof DateTime ? 
                                $pedido->FECHA_PREPARADO->format('d/m/Y H:i') : 
                                date('d/m/Y H:i', strtotime($pedido->FECHA_PREPARADO))) : 
                            'Pendiente'; 
                        ?>
                    </div>
                </div>
                <?php endif; ?>
                <div class="col timeline-step">
                    <div class="timeline-icon <?php echo ($pedido->FACTURADO ?? 0) ? 'active' : ''; ?>">
                        <i class="fas fa-file-invoice icon"></i>
                    </div>
                    <div>Facturado</div>
                    <div class="timeline-date">
                        <?php 
                        echo ($pedido->FECHA_FACTURADO ?? null) ? 
                            (($pedido->FECHA_FACTURADO ?? null) instanceof DateTime ? 
                                $pedido->FECHA_FACTURADO->format('d/m/Y H:i') : 
                                date('d/m/Y H:i', strtotime($pedido->FECHA_FACTURADO))) : 
                            'Pendiente'; 
                        ?>
                    </div>
                </div>
                <div class="col timeline-step">
                    <div class="timeline-icon <?php echo ($pedido->CONTROLADO ?? 0) ? 'active' : ''; ?>">
                        <i class="fas fa-clipboard-check icon"></i>
                    </div>
                    <div>Controlado</div>
                    <div class="timeline-date">
                        <?php 
                        echo ($pedido->FECHA_CONTROLADO ?? null) ? 
                            (($pedido->FECHA_CONTROLADO ?? null) instanceof DateTime ? 
                                $pedido->FECHA_CONTROLADO->format('d/m/Y H:i') : 
                                date('d/m/Y H:i', strtotime($pedido->FECHA_CONTROLADO))) : 
                            'Pendiente'; 
                        ?>
                    </div>
                </div>
                <div class="col timeline-step">
                    <div class="timeline-icon <?php echo ($pedido->DESPACHADO ?? 0) ? 'active' : ''; ?>">
                        <i class="fas fa-truck icon"></i>
                    </div>
                    <div>Despachado</div>
                    <div class="timeline-date">
                        <?php 
                        echo ($pedido->FECHA_DESPACHADO ?? null) ? 
                            (($pedido->FECHA_DESPACHADO ?? null) instanceof DateTime ? 
                                $pedido->FECHA_DESPACHADO->format('d/m/Y H:i') : 
                                date('d/m/Y H:i', strtotime($pedido->FECHA_DESPACHADO))) : 
                            'Pendiente'; 
                        ?>
                    </div>
                </div>
                <?php if (($pedido->RET_TIENDA_CENT ?? 0) == 1): ?>
                <div class="col timeline-step">
                    <div class="timeline-icon <?php echo ($pedido->RECIBIDO_TIENDA ?? 0) ? 'active' : ''; ?>">
                        <i class="fas fa-store icon"></i>
                    </div>
                    <div>Recibido Tienda</div>
                    <div class="timeline-date">
                        <?php 
                        echo ($pedido->FECHA_RECIBIDO_TIENDA ?? null) ? 
                            (($pedido->FECHA_RECIBIDO_TIENDA ?? null) instanceof DateTime ? 
                                $pedido->FECHA_RECIBIDO_TIENDA->format('d/m/Y H:i') : 
                                date('d/m/Y H:i', strtotime($pedido->FECHA_RECIBIDO_TIENDA))) : 
                            'Pendiente'; 
                        ?>
                    </div>
                </div>
                <?php endif; ?>
                <div class="col timeline-step">
                    <div class="timeline-icon <?php echo ($pedido->ENTREGADO ?? 0) ? 'active' : ''; ?>">
                        <i class="fas fa-check icon"></i>
                    </div>
                    <div>Entregado</div>
                    <div class="timeline-date">
                        <?php 
                        echo ($pedido->FECHA_ENTREGADO ?? null) ? 
                            (($pedido->FECHA_ENTREGADO ?? null) instanceof DateTime ? 
                                $pedido->FECHA_ENTREGADO->format('d/m/Y H:i') : 
                                date('d/m/Y H:i', strtotime($pedido->FECHA_ENTREGADO))) : 
                            'Pendiente'; 
                        ?>
                    </div>
                </div>
                <?php if ((($pedido->REINTEGRADO ?? 0) == 1) || (($pedido->CANCELADO ?? 0) == 1)): ?>
                <div class="col timeline-step">
                    <div class="timeline-icon active cancelled">
                        <i class="fas fa-times icon"></i>
                    </div>
                    <div>Cancelado</div>
                    <div class="timeline-date">
                        <?php 
                        echo (($pedido->FECHA_NCR ?? null)) ? 
                            (($pedido->FECHA_NCR ?? null) instanceof DateTime ? 
                                $pedido->FECHA_NCR->format('d/m/Y H:i') : 
                                date('d/m/Y H:i', strtotime($pedido->FECHA_NCR))) : 
                            ((($pedido->FECHA_PEDI ?? null) && $pedido->FECHA_PEDI) ?
                                (($pedido->FECHA_PEDI ?? null) instanceof DateTime ? 
                                    $pedido->FECHA_PEDI->format('d/m/Y') : 
                                    date('d/m/Y', strtotime($pedido->FECHA_PEDI))) : 
                                'Pendiente'); 
                        ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Timeline de Estados -->
<div class="card">
    <div class="card-body">
        <h5 class="card-title mb-4">Estado del Pedido</h5>
        <div class="timeline">
            <div class="row timeline-row">
                <div class="col timeline-step">
                    <div class="timeline-icon <?php echo $pedido->SINCRONIZADO ? 'active' : ''; ?>">
                        <i class="fas fa-sync-alt icon"></i>
                    </div>
                    <div>Sincronizado</div>
                    <div class="timeline-date">
                        <?php 
                        echo $pedido->FECHA_SINCRONIZADO ? 
                            ($pedido->FECHA_SINCRONIZADO instanceof DateTime ? 
                                $pedido->FECHA_SINCRONIZADO->format('d/m/Y H:i') : 
                                date('d/m/Y H:i', strtotime($pedido->FECHA_SINCRONIZADO))) : 
                            'Pendiente'; 
                        ?>
                    </div>
                </div>
                <div class="col timeline-step">
                    <div class="timeline-icon <?php echo $pedido->FACTURADO ? 'active' : ''; ?>">
                        <i class="fas fa-file-invoice icon"></i>
                    </div>
                    <div>Facturado</div>
                    <div class="timeline-date">
                        <?php 
                        echo $pedido->FECHA_FACTURADO ? 
                            ($pedido->FECHA_FACTURADO instanceof DateTime ? 
                                $pedido->FECHA_FACTURADO->format('d/m/Y H:i') : 
                                date('d/m/Y H:i', strtotime($pedido->FECHA_FACTURADO))) : 
                            'Pendiente'; 
                        ?>
                    </div>
                </div>
                <div class="col timeline-step">
                    <div class="timeline-icon <?php echo $pedido->CONTROLADO ? 'active' : ''; ?>">
                        <i class="fas fa-clipboard-check icon"></i>
                    </div>
                    <div>Controlado</div>
                    <div class="timeline-date">
                        <?php 
                        echo $pedido->FECHA_CONTROLADO ? 
                            ($pedido->FECHA_CONTROLADO instanceof DateTime ? 
                                $pedido->FECHA_CONTROLADO->format('d/m/Y H:i') : 
                                date('d/m/Y H:i', strtotime($pedido->FECHA_CONTROLADO))) : 
                            'Pendiente'; 
                        ?>
                    </div>
                </div>
                <div class="col timeline-step">
                    <div class="timeline-icon <?php echo $pedido->DESPACHADO ? 'active' : ''; ?>">
                        <i class="fas fa-truck icon"></i>
                    </div>
                    <div>Despachado</div>
                    <div class="timeline-date">
                        <?php 
                        echo $pedido->FECHA_DESPACHADO ? 
                            ($pedido->FECHA_DESPACHADO instanceof DateTime ? 
                                $pedido->FECHA_DESPACHADO->format('d/m/Y H:i') : 
                                date('d/m/Y H:i', strtotime($pedido->FECHA_DESPACHADO))) : 
                            'Pendiente'; 
                        ?>
                    </div>
                </div>
                <?php if ($pedido->RET_TIENDA_CENT == 1): ?>
                <div class="col timeline-step">
                    <div class="timeline-icon <?php echo $pedido->RECIBIDO_TIENDA ? 'active' : ''; ?>">
                        <i class="fas fa-store icon"></i>
                    </div>
                    <div>Recibido Tienda</div>
                    <div class="timeline-date">
                        <?php 
                        echo $pedido->FECHA_RECIBIDO_TIENDA ? 
                            ($pedido->FECHA_RECIBIDO_TIENDA instanceof DateTime ? 
                                $pedido->FECHA_RECIBIDO_TIENDA->format('d/m/Y H:i') : 
                                date('d/m/Y H:i', strtotime($pedido->FECHA_RECIBIDO_TIENDA))) : 
                            'Pendiente'; 
                        ?>
                    </div>
                </div>
                <?php endif; ?>
                <div class="col timeline-step">
                    <div class="timeline-icon <?php echo $pedido->ENTREGADO ? 'active' : ''; ?>">
                        <i class="fas fa-check icon"></i>
                    </div>
                    <div>Entregado</div>
                    <div class="timeline-date">
                        <?php 
                        echo $pedido->FECHA_ENTREGADO ? 
                            ($pedido->FECHA_ENTREGADO instanceof DateTime ? 
                                $pedido->FECHA_ENTREGADO->format('d/m/Y H:i') : 
                                date('d/m/Y H:i', strtotime($pedido->FECHA_ENTREGADO))) : 
                            'Pendiente'; 
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
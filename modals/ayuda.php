
<!-- Modal de Ayuda -->
<div class="modal fade" id="modalAyuda" tabindex="-1" role="dialog" aria-labelledby="modalAyudaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title" id="modalAyudaLabel">
                    <i class="fas fa-question-circle mr-2"></i>
                    Guía de Uso - Estado Pedidos Ecommerce
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
                <!-- Navegación por pestañas -->
                <ul class="nav nav-pills mb-4" id="ayudaTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="estados-tab" data-toggle="pill" href="#estados" role="tab">
                            <i class="fas fa-traffic-light mr-1"></i>Estados del Pedido
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="botones-tab" data-toggle="pill" href="#botones" role="tab">
                            <i class="fas fa-mouse-pointer mr-1"></i>Botones y Filtros
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="iconos-tab" data-toggle="pill" href="#iconos" role="tab">
                            <i class="fas fa-icons mr-1"></i>Iconos y Colores
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="busqueda-tab" data-toggle="pill" href="#busqueda" role="tab">
                            <i class="fas fa-search mr-1"></i>Búsqueda y Filtros
                        </a>
                    </li>
                </ul>

                <!-- Contenido de las pestañas -->
                <div class="tab-content" id="ayudaTabContent">
                    
                    <!-- Pestaña Estados del Pedido -->
                    <div class="tab-pane fade show active" id="estados" role="tabpanel">
                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-primary mb-3"><i class="fas fa-list-ol mr-2"></i>Flujo de Estados del Pedido</h5>
                                <p class="text-muted mb-4">Los pedidos pasan por diferentes estados durante su procesamiento. Aquí se explica cada uno:</p>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-left-warning shadow-sm mb-3">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                <i class="fas fa-exclamation-triangle text-warning" style="font-size: 24px;"></i>
                                            </div>
                                            <div>
                                                <h6 class="card-title text-warning mb-1">SIN CONTROLAR</h6>
                                                <p class="card-text small mb-0">Pedido recibido pero no verificado aún.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card border-left-info shadow-sm mb-3">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                <i class="bi bi-cart-check-fill text-success" style="font-size: 24px;"></i>
                                            </div>
                                            <div>
                                                <h6 class="card-title text-success mb-1">PREPARADO</h6>
                                                <p class="card-text small mb-0">Productos listos para facturación.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card border-left-secondary shadow-sm mb-3">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                <i class="bi bi-file-earmark-text-fill text-secondary" style="font-size: 24px;"></i>
                                            </div>
                                            <div>
                                                <h6 class="card-title text-secondary mb-1">FACTURADO</h6>
                                                <p class="card-text small mb-0">Pedido facturado, listo para control.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card border-left-success shadow-sm mb-3">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                <i class="bi bi-clipboard2-check-fill text-success" style="font-size: 24px;"></i>
                                            </div>
                                            <div>
                                                <h6 class="card-title text-success mb-1">CONTROLADO</h6>
                                                <p class="card-text small mb-0">Pedido verificado y controlado.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card border-left-info shadow-sm mb-3">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                <i class="fas fa-truck text-info" style="font-size: 24px;"></i>
                                            </div>
                                            <div>
                                                <h6 class="card-title text-info mb-1">DESPACHADO</h6>
                                                <p class="card-text small mb-0">Pedido enviado desde el depósito.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card border-left-primary shadow-sm mb-3">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                <i class="bi bi-box-seam-fill text-primary" style="font-size: 24px;"></i>
                                            </div>
                                            <div>
                                                <h6 class="card-title text-primary mb-1">ENTREGADO</h6>
                                                <p class="card-text small mb-0">Pedido entregado al cliente final.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card border-left-danger shadow-sm mb-3">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                <i class="bi bi-cart-x-fill text-danger" style="font-size: 24px;"></i>
                                            </div>
                                            <div>
                                                <h6 class="card-title text-danger mb-1">CANCELADO</h6>
                                                <p class="card-text small mb-0">Pedido cancelado por el cliente o sistema.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card border-left-warning shadow-sm mb-3">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                <i class="bi bi-cart-dash-fill text-warning" style="font-size: 24px;"></i>
                                            </div>
                                            <div>
                                                <h6 class="card-title text-warning mb-1">FALTANTE</h6>
                                                <p class="card-text small mb-0">Pedido con productos faltantes.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pestaña Botones y Filtros -->
                    <div class="tab-pane fade" id="botones" role="tabpanel">
                        <h5 class="text-primary mb-3"><i class="fas fa-mouse-pointer mr-2"></i>Funcionalidad de Botones</h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card shadow-sm mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-filter mr-2"></i>Botones de Filtro Rápido</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <button class="btn btn-sm btn-outline-warning mr-2" disabled>Pendientes</button>
                                            <p class="small text-muted mb-2">Muestra pedidos que están pendientes de procesamiento (sin facturar).</p>
                                        </div>
                                        <div class="mb-3">
                                            <button class="btn btn-sm btn-outline-purple mr-2" disabled>Sin NC</button>
                                            <p class="small text-muted mb-2">Filtra pedidos cancelados que no tienen nota de crédito asociada.</p>
                                        </div>
                                        <div class="mb-3">
                                            <button class="btn btn-sm btn-outline-orange mr-2" disabled>Incompletos</button>
                                            <p class="small text-muted mb-0">Muestra pedidos que tienen productos faltantes.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card shadow-sm mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-tools mr-2"></i>Botones de Acción</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <button class="btn btn-sm btn-success mr-2" disabled>
                                                <i class="bi bi-filetype-xls"></i> Exportar
                                            </button>
                                            <p class="small text-muted mb-2">Exporta los datos filtrados a un archivo Excel (.xls).</p>
                                        </div>
                                        <div class="mb-3">
                                            <button class="btn btn-sm btn-primary mr-2" disabled>
                                                <i class="bi bi-search"></i> Buscar
                                            </button>
                                            <p class="small text-muted mb-0">Aplica los filtros seleccionados en el formulario.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pestaña Iconos y Colores -->
                    <div class="tab-pane fade" id="iconos" role="tabpanel">
                        <h5 class="text-primary mb-3"><i class="fas fa-palette mr-2"></i>Guía de Iconos y Colores</h5>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <strong>Interpretación de Colores:</strong> Cada color representa un estado específico del proceso.
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-success mb-3">Estados Completados</h6>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-cart-check-fill text-success mr-3" style="font-size: 20px;"></i>
                                    <span>Verde - Preparado</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-clipboard2-check-fill text-success mr-3" style="font-size: 20px;"></i>
                                    <span>Verde - Controlado</span>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-clipboard-check-fill text-info mr-3" style="font-size: 20px;"></i>
                                    <span>Azul claro - Con NC (Nota de Crédito)</span>
                                </div>
                                
                                <h6 class="text-primary mb-3">Estados en Proceso</h6>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-file-earmark-text-fill text-secondary mr-3" style="font-size: 20px;"></i>
                                    <span>Gris - Facturado</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-truck text-info mr-3" style="font-size: 20px;"></i>
                                    <span>Azul - Despachado</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-store text-info mr-3" style="font-size: 20px;"></i>
                                    <span>Azul - Recibido en Tienda</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-box-seam-fill text-primary mr-3" style="font-size: 20px;"></i>
                                    <span>Azul oscuro - Entregado</span>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h6 class="text-warning mb-3">Estados de Atención</h6>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-cart-dash-fill text-warning mr-3" style="font-size: 20px;"></i>
                                    <span>Naranja - Incompleto/Faltante</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-clipboard-x-fill text-purple mr-3" style="font-size: 20px;"></i>
                                    <span>Violeta - Cancelado sin NC</span>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-square text-muted mr-3" style="font-size: 20px;"></i>
                                    <span>Blanco - Estado no alcanzado</span>
                                </div>
                                
                                <h6 class="text-danger mb-3">Estados Críticos</h6>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-cart-x-fill text-danger mr-3" style="font-size: 20px;"></i>
                                    <span>Rojo - Cancelado</span>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted">
                                        <strong>Texto en rojo y negrita:</strong> Pedidos sin facturar que requieren atención inmediata.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pestaña Búsqueda y Filtros -->
                    <div class="tab-pane fade" id="busqueda" role="tabpanel">
                        <h5 class="text-primary mb-3"><i class="fas fa-search mr-2"></i>Cómo Usar la Búsqueda y Filtros</h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card shadow-sm mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-calendar-alt mr-2"></i>Filtros de Fecha</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="small"><strong>Desde/Hasta:</strong> Filtra pedidos por rango de fechas de pedido.</p>
                                        <p class="small text-muted">Por defecto muestra los pedidos del día actual.</p>
                                    </div>
                                </div>
                                
                                <div class="card shadow-sm mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-store mr-2"></i>Filtros de Origen</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="small"><strong>Tienda:</strong> ICBC, VTEX, Mercado Libre</p>
                                        <p class="small"><strong>Origen:</strong> Depósito o warehouse de origen</p>
                                        <p class="small text-muted">Deja vacío para mostrar todos.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card shadow-sm mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-search mr-2"></i>Búsqueda Rápida</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="small"><strong>Campo de búsqueda:</strong> Busca en cualquier columna de la tabla.</p>
                                        <p class="small">Puedes buscar por:</p>
                                        <ul class="small">
                                            <li>Número de orden</li>
                                            <li>Nombre del cliente</li>
                                            <li>Código de artículo</li>
                                            <li>Número de factura</li>
                                            <li>Cualquier otro campo visible</li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <div class="card shadow-sm mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-lightbulb mr-2"></i>Consejos de Uso</h6>
                                    </div>
                                    <div class="card-body">
                                        <ul class="small mb-0">
                                            <li>Usa los filtros rápidos para ver casos específicos</li>
                                            <li>Combina filtros de fecha con estado para análisis</li>
                                            <li>Exporta los resultados filtrados para reportes</li>
                                            <li>Los contadores se actualizan según los filtros aplicados</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos personalizados para el modal */
.border-left-warning {
    border-left: 4px solid #ffc107 !important;
}
.border-left-info {
    border-left: 4px solid #17a2b8 !important;
}
.border-left-secondary {
    border-left: 4px solid #6c757d !important;
}
.border-left-success {
    border-left: 4px solid #28a745 !important;
}
.border-left-primary {
    border-left: 4px solid #007bff !important;
}
.border-left-danger {
    border-left: 4px solid #dc3545 !important;
}

.text-purple {
    color: #6610f2 !important;
}

.btn-outline-purple {
    color: #6610f2;
    border-color: #6610f2;
}

.btn-outline-orange {
    color: #fd7e14;
    border-color: #fd7e14;
}

.nav-pills .nav-link {
    border-radius: 25px;
    margin-right: 10px;
}

.nav-pills .nav-link.active {
    background-color: #007bff;
}

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.modal-xl {
    max-width: 1200px;
}

@media (max-width: 768px) {
    .modal-xl {
        max-width: 95%;
        margin: 10px auto;
    }
}
</style>
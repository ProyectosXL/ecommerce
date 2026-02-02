<div id="reporte-container">
    <!-- Overlay de Carga -->
    <div id="loading-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999; justify-content: center; align-items: center;">
        <div style="text-align: center; color: white;">
            <div class="spinner-border text-light" role="status" style="width: 4rem; height: 4rem; border-width: 0.4rem;">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <h4 class="mt-3" id="loading-text">Cargando datos...</h4>
            <p id="loading-country" class="mb-0"></p>
        </div>
    </div>
    
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h4 class="mb-2">Dashboard de Incidentes por Faltantes</h4>
            <!-- Indicador de País Activo -->
            <div id="pais-indicator" class="badge bg-info" style="font-size: 0.9rem; padding: 0.5rem 1rem;">
                <span id="pais-flag" class="me-2"></span>
                <span id="pais-nombre">Consultando: Argentina</span>
            </div>
        </div>
        
        <!-- Info SLA -->
        <div class="alert alert-info mb-0 p-2 px-3" style="max-width: 600px;">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-info-circle"></i>
                <div>
                    <strong>SLA (Service Level Agreement):</strong>
                    <span id="sla-info-text">Acuerdo de nivel de servicio de <strong id="sla-dias-value">10</strong> días para resolver incidentes.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card search-container mb-4">
        <div class="card-body">
            <style>
                /* Estilos personalizados para los botones del toggle de país */
                /* Argentina = Celeste cuando está activo */
                #pais-ar:checked + label {
                    background-color: #0dcaf0 !important;
                    border-color: #0dcaf0 !important;
                    color: white !important;
                }
                
                /* Uruguay = Azul cuando está activo */
                #pais-uy:checked + label {
                    background-color: #0d6efd !important;
                    border-color: #0d6efd !important;
                    color: white !important;
                }
                
                /* Hover states */
                label[for="pais-ar"]:hover {
                    background-color: rgba(13, 202, 240, 0.1);
                    border-color: #0dcaf0;
                }
                
                label[for="pais-uy"]:hover {
                    background-color: rgba(13, 110, 253, 0.1);
                    border-color: #0d6efd;
                }
            </style>
            <div class="row g-3 align-items-end">
                <!-- NUEVO: Toggle de País con Banderas -->
                <div class="col-md-2">
                    <label class="form-label d-block">País</label>
                    <div class="btn-group w-100" role="group" aria-label="Selector de país">
                        <input type="radio" class="btn-check" name="reporte-pais" id="pais-ar" value="AR" autocomplete="off" checked>
                        <label class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-2" for="pais-ar" style="height: 38px;">
                            <img src="https://flagcdn.com/w20/ar.png" srcset="https://flagcdn.com/w40/ar.png 2x" width="20" alt="Argentina">
                            <span>AR</span>
                        </label>
                        
                        <input type="radio" class="btn-check" name="reporte-pais" id="pais-uy" value="UY" autocomplete="off">
                        <label class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-2" for="pais-uy" style="height: 38px;">
                            <img src="https://flagcdn.com/w20/uy.png" srcset="https://flagcdn.com/w40/uy.png 2x" width="20" alt="Uruguay">
                            <span>UY</span>
                        </label>
                    </div>
                </div>
                <div class="col-md-2">
                    <label for="reporte-desde" class="form-label">Desde</label>
                    <input type="date" id="reporte-desde" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="reporte-hasta" class="form-label">Hasta</label>
                    <input type="date" id="reporte-hasta" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="reporte-estado" class="form-label">Estado del Reclamo</label>
                    <select id="reporte-estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="abierto">Abierto</option>
                        <option value="proceso">En Curso</option>
                        <option value="resuelto">Finalizado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button id="btn-aplicar-filtros" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>Aplicar Filtros
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- KPIs -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="kpi-card text-white bg-primary h-100">
                <div class="kpi-icon"><i class="fas fa-clipboard-list"></i></div>
                <div class="kpi-value" id="kpi-total">0</div>
                <div class="kpi-label">Total Incidentes</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="kpi-card text-white bg-danger h-100">
                <div class="kpi-icon"><i class="fas fa-folder-open"></i></div>
                <div class="kpi-value" id="kpi-abiertos">0</div>
                <div class="kpi-label">Abiertos</div>
                <div class="kpi-sublabel" id="kpi-abiertos-fuera-sla" style="font-size: 0.85em; margin-top: 5px;">
                    <i class="fas fa-exclamation-triangle"></i> 0 fuera de SLA
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="kpi-card text-dark bg-warning h-100">
                <div class="kpi-icon"><i class="fas fa-cogs"></i></div>
                <div class="kpi-value" id="kpi-proceso">0</div>
                <div class="kpi-label">En Curso</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div id="kpi-card-finalizados" class="kpi-card text-white bg-success h-100" style="cursor: pointer;">
                <div class="kpi-icon"><i class="fas fa-check-circle"></i></div>
                <div class="kpi-value" id="kpi-resueltos">0</div>
                <div class="kpi-label">Finalizados (clic para ver detalle)</div>
                <div class="kpi-sublabel" id="kpi-finalizados-sla" style="font-size: 0.85em; margin-top: 5px;">
                    <i class="fas fa-check"></i> 0% cumplió SLA
                </div>
            </div>
        </div>
    </div>

    <!-- NUEVOS KPIs SLA -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="kpi-card-sla h-100">
                <div class="sla-header">
                    <i class="fas fa-bullseye text-primary me-2"></i>
                    <h5 class="mb-0">% Cumplimiento SLA</h5>
                </div>
                <div class="sla-body">
                    <div class="sla-value-container">
                        <span class="sla-value" id="sla-cumplimiento">0%</span>
                        <div class="sla-objetivo-info">
                            <small>Objetivo: <strong id="sla-proximo-objetivo">70%</strong></small>
                            <small class="text-danger">Gap: <strong id="sla-gap">-70 pts</strong></small>
                        </div>
                    </div>
                    <div class="progress-sla-container mt-3">
                        <div class="progress" style="height: 30px; position: relative;">
                            <div id="sla-cumplimiento-bar" class="progress-bar" role="progressbar" 
                                 style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            </div>
                            <!-- Marcadores de objetivos -->
                            <div class="sla-marker" style="left: 70%;" data-bs-toggle="tooltip" title="Objetivo 1: 70%">
                                <span class="marker-line"></span>
                                <span class="marker-label">70%</span>
                            </div>
                            <div class="sla-marker" style="left: 80%;" data-bs-toggle="tooltip" title="Objetivo 2: 80%">
                                <span class="marker-line"></span>
                                <span class="marker-label">80%</span>
                            </div>
                            <div class="sla-marker" style="left: 90%;" data-bs-toggle="tooltip" title="Objetivo 3: 90%">
                                <span class="marker-line"></span>
                                <span class="marker-label">90%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="kpi-card-sla kpi-warning h-100">
                <div class="sla-icon-badge bg-warning">
                    <i class="fas fa-exclamation-triangle text-dark"></i>
                </div>
                <div class="sla-value text-warning" id="sla-casos-riesgo">0</div>
                <div class="sla-label">Casos en Riesgo</div>
                <div class="sla-tooltip-info">
                    <i class="fas fa-info-circle"></i> ≤ 1 día para llegar al SLA
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="kpi-card-sla kpi-danger h-100">
                <div class="sla-icon-badge bg-danger">
                    <i class="fas fa-times-circle text-white"></i>
                </div>
                <div class="sla-value text-danger" id="sla-casos-fuera">0</div>
                <div class="sla-label">Casos Fuera de SLA</div>
                <div class="sla-percentage" id="sla-casos-fuera-pct">0% del total</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="kpi-card-sla h-100">
                <div class="sla-icon-badge bg-info">
                    <i class="fas fa-clock text-white"></i>
                </div>
                <div class="sla-value text-info" id="sla-brecha-promedio">0</div>
                <div class="sla-label">Brecha Promedio vs SLA</div>
                <div class="sla-sublabel">días de demora</div>
            </div>
        </div>
    </div>
    
    <div class="row justify-content-center mb-4">
        <div class="col-md-6">
             <div id="grafico-resolucion-container" class="card" style="display: none;">
                <div class="card-body">
                    <h5 class="card-title text-center">Desglose de Resoluciones</h5>
                    <div style="height: 250px;">
                        <canvas id="graficoDesgloseResolucion"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-center">Cumplimiento del SLA</h5>
                    <div style="height: 250px;">
                        <canvas id="graficoCumplimientoSLA"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- NUEVO: Gráficos Comparativos por Depósito -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-3 mb-lg-0">
            <div class="card h-100">
                <div class="card-body position-relative">
                    <!-- Badge informativo superior derecho -->
                    <span class="badge bg-primary position-absolute top-0 end-0 m-3" style="z-index: 10;">
                        <i class="fas fa-chart-bar me-1"></i> Volumen
                    </span>
                    
                    <h5 class="card-title">
                        <i class="fas fa-chart-bar text-primary me-2"></i>
                        Incidencias por Depósito
                    </h5>
                    <p class="text-muted small mb-3">Ordenado por mayor cantidad de incidentes</p>
                    <div style="height: 350px;">
                        <canvas id="graficoIncidenciasPorDeposito"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body position-relative">
                    <!-- Badge informativo superior derecho -->
                    <span class="badge bg-danger position-absolute top-0 end-0 m-3" style="z-index: 10;">
                        <i class="fas fa-exclamation-triangle me-1"></i> % Fuera SLA
                    </span>
                    
                    <h5 class="card-title">
                        <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                        % Incumplimiento SLA por Depósito
                    </h5>
                    
                    <!-- Leyenda Dinámica Interactiva -->
                    <div class="legend-container mb-3">
                        <div class="legend-item legend-critico" data-nivel="critico">
                            <span class="legend-color"></span>
                            <span class="legend-text">
                                <strong id="count-critico">0</strong> críticos 
                                (<span id="pct-critico">0%</span>)
                            </span>
                        </div>
                        <div class="legend-item legend-alerta" data-nivel="alerta">
                            <span class="legend-color"></span>
                            <span class="legend-text">
                                <strong id="count-alerta">0</strong> alerta 
                                (<span id="pct-alerta">0%</span>)
                            </span>
                        </div>
                        <div class="legend-item legend-aceptable" data-nivel="aceptable">
                            <span class="legend-color"></span>
                            <span class="legend-text">
                                <strong id="count-aceptable">0</strong> aceptable 
                                (<span id="pct-aceptable">0%</span>)
                            </span>
                        </div>
                    </div>
                    
                    <div style="height: 280px;">
                        <canvas id="graficoIncumplimientoSLA"></canvas>
                    </div>
                    
                    <!-- Insight Automático -->
                    <div id="insight-container" class="alert alert-info mt-3" style="display: none;">
                        <i class="fas fa-lightbulb me-2"></i>
                        <small id="insight-text"></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPIs Adicionales -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="kpi-card-secondary h-100">
                <h5><i class="fas fa-tasks text-success me-2"></i>Tasa de Resolución</h5>
                <div class="d-flex align-items-center">
                    <div class="progress flex-grow-1" style="height: 25px;">
                        <div id="kpi-tasa-resolucion-bar" class="progress-bar bg-success" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <span id="kpi-tasa-resolucion-text" class="kpi-progress-text ms-3">0%</span>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="kpi-card-secondary h-100">
                <h5><i class="fas fa-clock text-info me-2"></i>Tiempo Promedio de Resolución</h5>
                <p class="h3" id="kpi-tiempo-promedio">N/A</p>
            </div>
        </div>
    </div>
    
    <!-- Tabla de Datos -->
    <div class="card mt-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h5 class="card-title mb-0">Detalle de Incidentes</h5>
                <div class="d-flex gap-2 align-items-center">
                    <button id="btn-filtrar-fuera-sla" class="btn btn-outline-danger" style="height: 38px; display: flex; align-items: center;">
                        <i class="fas fa-filter me-1"></i>Ver solo casos fuera de SLA
                    </button>
                    <button id="btn-limpiar-filtro-sla" class="btn btn-outline-secondary" style="height: 38px; display: none; align-items: center;">
                        <i class="fas fa-times me-1"></i>Limpiar filtro
                    </button>
                    <div id="export-buttons-container"></div>
                </div>
            </div>
            <div class="table-responsive">
                <table id="tabla-reporte-incidentes" class="table table-striped table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>Estado SLA</th>
                            <th>Nro. Pedido</th>
                            <th>Nro. Orden</th>
                            <th>Fecha de Pedido</th>
                            <th>Fecha Aviso Incompleto</th>
                            <th>Cliente</th>
                            <th>Artículo Faltante</th>
                            <th>Descripción Artículo</th>
                            <th>Rubro</th>
                            <th>Artículo Reemplazante</th>
                            <th>Depósito Origen</th>
                            <th>Nombre Origen</th>
                            <th>Warehouse Reclamo</th>
                            <th>Estado</th>
                            <th>Resolución</th>
                            <th>Días Transcurridos</th>
                            <th>Días sobre SLA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Los datos se cargarán por AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
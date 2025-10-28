<div id="reporte-container">
    <h4 class="mb-4">Dashboard de Incidentes por Faltantes</h4>

    <!-- Filtros -->
    <div class="card search-container mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="reporte-desde" class="form-label">Desde</label>
                    <input type="date" id="reporte-desde" class="form-control">
                </div>
                <div class="col-md-3">
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
    
    <!-- NUEVO: Contenedor para el Gráfico de Ranking -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Ranking de Depósitos Origen con más Incidentes</h5>
                    <div style="height: 300px;">
                        <canvas id="graficoRankingDepositos"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Tabla de Datos -->
    <div class="card mt-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Detalle de Incidentes</h5>
                <div id="export-buttons-container"></div>
            </div>
            <div class="table-responsive">
                <table id="tabla-reporte-incidentes" class="table table-striped table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>Nro. Pedido</th>
                            <th>Nro. Orden</th>
                            <th>Fecha Incidente</th>
                            <th>Cliente</th>
                            <th>Artículo Faltante</th>
                            <th>Depósito Origen</th>
                            <th>Nombre Origen</th>
                            <th>Warehouse Reclamo</th>
                            <th>Estado</th>
                            <th>Resolución</th>
                            <th>Días Abierto</th>
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
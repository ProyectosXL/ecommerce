  

        class HistorialFaltantesManager {
            constructor() {
                this.dataTable = null;
                this.originalData = [];
                this.filteredData = [];
                this.charts = {};
                this.init();
            }

            async init() {
                try {
                    this.setupDateFilters();
                    this.setupEventListeners();
                    await this.loadInitialData();
                    this.initializeDataTable();
                    this.initializeCharts();
                    this.updateStats();
                } catch (error) {
                    console.error('Error initializing:', error);
                    this.showError('Error al inicializar la aplicación');
                }
            }

            setupDateFilters() {
                const today = new Date();
                const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
                
                document.getElementById('fechaInicio').value = thirtyDaysAgo.toISOString().split('T')[0];
                document.getElementById('fechaFin').value = today.toISOString().split('T')[0];
                document.getElementById('fechaInicio').max = today.toISOString().split('T')[0];
                document.getElementById('fechaFin').max = today.toISOString().split('T')[0];
            }

            setupEventListeners() {
                document.getElementById('aplicarFiltros').addEventListener('click', () => {
                    this.applyFilters();
                });

                document.getElementById('limpiarFiltros').addEventListener('click', () => {
                    this.clearFilters();
                });

                // Auto-aplicar filtros cuando cambian las fechas
                document.getElementById('fechaInicio').addEventListener('change', () => {
                    this.applyFilters();
                });

                document.getElementById('fechaFin').addEventListener('change', () => {
                    this.applyFilters();
                });
            }

            async loadInitialData() {
                this.showLoading(true);
                
                try {
                    // Simular datos de pedidos con faltantes
                    this.originalData = await this.generateFaltantesData();
                    this.filteredData = [...this.originalData];
                    
                    // Cargar datos para los dropdowns
                    await this.loadDropdownData();
                    
                } catch (error) {
                    console.error('Error loading data:', error);
                    this.showError('Error al cargar los datos');
                } finally {
                    this.showLoading(false);
                }
            }

            async generateFaltantesData() {
                // Datos simulados enfocados en pedidos con faltantes
                const warehouses = ['CENTRAL', 'SUC 001', 'SUC 002', 'SUC 003', 'SUC 004'];
                const estados = ['abierto', 'proceso', 'resuelto'];
                const resoluciones = ['cambio', 'cancelado', 'completado', 'pendiente'];

                const data = [];
                for (let i = 1; i <= 100; i++) {
                    const fechaIncidente = new Date(Date.now() - Math.random() * 60 * 24 * 60 * 60 * 1000);
                    const ultimaModif = new Date(fechaIncidente.getTime() + Math.random() * 7 * 24 * 60 * 60 * 1000);
                    
                    data.push({
                        nro_pedido: `PED${String(i).padStart(6, '0')}`,
                        nro_orden: `ORD${String(i).padStart(8, '0')}`,
                        fecha_incidente: fechaIncidente.toISOString().split('T')[0],
                        fecha_incidente_full: fechaIncidente,
                        cliente: `Cliente ${i}`,
                        articulo_faltante: `ART${String(Math.floor(Math.random() * 1000) + 1).padStart(4, '0')}`,
                        descripcion_articulo: `Producto ${i} - Artículo con faltante de stock`,
                        cantidad: Math.floor(Math.random() * 5) + 1,
                        warehouse: warehouses[Math.floor(Math.random() * warehouses.length)],
                        estado: estados[Math.floor(Math.random() * estados.length)],
                        resolucion: resoluciones[Math.floor(Math.random() * resoluciones.length)],
                        ultima_modificacion: ultimaModif.toISOString().split('T')[0],
                        ultima_modificacion_full: ultimaModif
                    });
                }
                
                return data;
            }

            async loadDropdownData() {
                // Cargar warehouses únicos
                const warehouses = [...new Set(this.originalData.map(item => item.warehouse))].sort();
                const warehouseSelect = document.getElementById('warehouse');
                warehouses.forEach(warehouse => {
                    const option = document.createElement('option');
                    option.value = warehouse;
                    option.textContent = warehouse;
                    warehouseSelect.appendChild(option);
                });
            }

            initializeDataTable() {
                const tableData = this.filteredData.map(item => [
                    item.nro_pedido,
                    item.nro_orden,
                    this.formatDate(item.fecha_incidente),
                    item.cliente,
                    item.articulo_faltante,
                    item.descripcion_articulo,
                    item.cantidad,
                    item.warehouse,
                    this.formatEstado(item.estado),
                    this.formatResolucion(item.resolucion),
                    this.formatDate(item.ultima_modificacion)
                ]);

                if (this.dataTable) {
                    this.dataTable.destroy();
                }

                this.dataTable = $('#faltantesTable').DataTable({
                    data: tableData,
                    responsive: true,
                    pageLength: 25,
                    order: [[2, 'desc']], // Ordenar por fecha descendente
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'excel',
                            text: '<i class="fas fa-file-excel me-1"></i>Excel',
                            className: 'btn btn-success btn-sm',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
                            }
                        },
                        {
                            extend: 'pdf',
                            text: '<i class="fas fa-file-pdf me-1"></i>PDF',
                            className: 'btn btn-danger btn-sm',
                            orientation: 'landscape',
                            pageSize: 'A4',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 6, 7, 8, 9]
                            }
                        },
                        {
                            extend: 'print',
                            text: '<i class="fas fa-print me-1"></i>Imprimir',
                            className: 'btn btn-info btn-sm'
                        }
                    ],
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                    },
                    columnDefs: [
                        {
                            targets: [8, 9], // Columnas con HTML (Estado, Resolución)
                            orderable: true,
                            searchable: true
                        },
                        {
                            targets: '_all',
                            className: 'text-center'
                        },
                        {
                            targets: [0, 1, 3, 4, 5], // Pedido, Orden, Cliente, Artículo, Descripción
                            className: 'text-start'
                        }
                    ],
                    initComplete: function() {
                        // Mover botones al contenedor personalizado
                        $('.dt-buttons').appendTo('#tableControls');
                    }
                });
            }

            applyFilters() {
                const filters = {
                    fechaInicio: document.getElementById('fechaInicio').value,
                    fechaFin: document.getElementById('fechaFin').value,
                    warehouse: document.getElementById('warehouse').value,
                    estado: document.getElementById('estado').value,
                    resolucion: document.getElementById('resolucion').value
                };

                this.filteredData = this.originalData.filter(item => {
                    // Filtro por fecha
                    if (filters.fechaInicio && item.fecha_incidente < filters.fechaInicio) return false;
                    if (filters.fechaFin && item.fecha_incidente > filters.fechaFin) return false;
                    
                    // Filtros por dropdown
                    if (filters.warehouse && item.warehouse !== filters.warehouse) return false;
                    if (filters.estado && item.estado !== filters.estado) return false;
                    if (filters.resolucion && item.resolucion !== filters.resolucion) return false;

                    return true;
                });

                this.initializeDataTable();
                this.updateStats();
                this.updateCharts();

                // Mostrar mensaje si no hay resultados
                if (this.filteredData.length === 0) {
                    this.showInfo('No se encontraron pedidos con faltantes con los filtros aplicados');
                }
            }

            clearFilters() {
                document.getElementById('filtrosForm').reset();
                this.setupDateFilters();
                this.filteredData = [...this.originalData];
                this.initializeDataTable();
                this.updateStats();
                this.updateCharts();
            }

            updateStats() {
                const data = this.filteredData;
                const today = new Date().toISOString().split('T')[0];
                
                document.getElementById('totalFaltantes').textContent = data.length;
                document.getElementById('faltantesHoy').textContent = data.filter(item => item.fecha_incidente === today).length;
                document.getElementById('articulosFaltantes').textContent = new Set(data.map(item => item.articulo_faltante)).size;
                document.getElementById('warehousesAfectados').textContent = new Set(data.map(item => item.warehouse)).size;
            }

            initializeCharts() {
                this.createEstadosChart();
                this.createTendenciaChart();
            }

            updateCharts() {
                this.updateEstadosChart();
                this.updateTendenciaChart();
            }

            createEstadosChart() {
                const ctx = document.getElementById('chartEstados').getContext('2d');
                const data = this.getFaltantesPorEstado();
                
                this.charts.estados = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.values,
                            backgroundColor: [
                                '#f44336', // Abierto - Rojo
                                '#ff9800', // Proceso - Naranja
                                '#4caf50'  // Resuelto - Verde
                            ],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    usePointStyle: true
                                }
                            }
                        }
                    }
                });
            }

            createTendenciaChart() {
                const ctx = document.getElementById('chartTendencia').getContext('2d');
                const data = this.getTendenciaDiaria();
                
                this.charts.tendencia = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Faltantes por Día',
                            data: data.values,
                            borderColor: '#1976d2',
                            backgroundColor: 'rgba(25, 118, 210, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#1976d2',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0,0,0,0.1)'
                                }
                            },
                            x: {
                                grid: {
                                    color: 'rgba(0,0,0,0.1)'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }

            updateEstadosChart() {
                const data = this.getFaltantesPorEstado();
                this.charts.estados.data.labels = data.labels;
                this.charts.estados.data.datasets[0].data = data.values;
                this.charts.estados.update();
            }

            updateTendenciaChart() {
                const data = this.getTendenciaDiaria();
                this.charts.tendencia.data.labels = data.labels;
                this.charts.tendencia.data.datasets[0].data = data.values;
                this.charts.tendencia.update();
            }

            getFaltantesPorEstado() {
                const estadoMap = {};
                this.filteredData.forEach(item => {
                    estadoMap[item.estado] = (estadoMap[item.estado] || 0) + 1;
                });

                const estadosTexto = {
                    'abierto': 'Abierto',
                    'proceso': 'En Proceso',
                    'resuelto': 'Resuelto'
                };

                return {
                    labels: Object.keys(estadoMap).map(estado => estadosTexto[estado] || estado),
                    values: Object.values(estadoMap)
                };
            }

            getTendenciaDiaria() {
                const fechaMap = {};
                this.filteredData.forEach(item => {
                    fechaMap[item.fecha_incidente] = (fechaMap[item.fecha_incidente] || 0) + 1;
                });

                const fechasOrdenadas = Object.keys(fechaMap).sort();
                return {
                    labels: fechasOrdenadas.map(fecha => {
                        const date = new Date(fecha);
                        return date.toLocaleDateString('es-AR', { day: '2-digit', month: '2-digit' });
                    }),
                    values: fechasOrdenadas.map(fecha => fechaMap[fecha])
                };
            }

            formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('es-AR', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
            }

            formatEstado(estado) {
                const estados = {
                    'abierto': '<span class="status-badge badge-abierto">Abierto</span>',
                    'proceso': '<span class="status-badge badge-proceso">En Proceso</span>',
                    'resuelto': '<span class="status-badge badge-resuelto">Resuelto</span>'
                };
                return estados[estado] || estado;
            }

            formatResolucion(resolucion) {
                const resoluciones = {
                    'cambio': '<span class="badge bg-primary">Cambio</span>',
                    'cancelado': '<span class="badge bg-danger">Cancelado</span>',
                    'completado': '<span class="badge bg-success">Completado</span>',
                    'pendiente': '<span class="badge bg-warning">Pendiente</span>'
                };
                return resoluciones[resolucion] || resolucion;
            }

            showLoading(show) {
                const overlay = document.getElementById('loadingOverlay');
                overlay.style.display = show ? 'flex' : 'none';
            }

            showError(message) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message,
                    confirmButtonColor: '#1976d2'
                });
            }

            showInfo(message) {
                Swal.fire({
                    icon: 'info',
                    title: 'Información',
                    text: message,
                    confirmButtonColor: '#1976d2'
                });
            }

            showSuccess(message) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: message,
                    confirmButtonColor: '#1976d2'
                });
            }
        }

        // Inicializar la aplicación cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            // Verificar autenticación (implementar según el sistema de autenticación existente)
            if (!checkAuthentication()) {
                window.location.href = '/login.php';
                return;
            }

            // Inicializar el manager
            window.faltantesManager = new HistorialFaltantesManager();
        });

        // Función básica de verificación de autenticación
        function checkAuthentication() {
            // En una implementación real, esto verificaría el token de sesión
            // Por ahora retorna true para la demo
            return true;
        }

        // Función para exportar datos personalizados
        function exportCustomData(format) {
            if (!window.faltantesManager || !window.faltantesManager.filteredData) {
                alert('No hay datos para exportar');
                return;
            }

            const data = window.faltantesManager.filteredData;
            
            if (format === 'json') {
                const jsonData = JSON.stringify(data, null, 2);
                const blob = new Blob([jsonData], { type: 'application/json' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `historial_faltantes_${new Date().toISOString().split('T')[0]}.json`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            }
        }

        // Función para refrescar datos
        async function refreshData() {
            if (window.faltantesManager) {
                await window.faltantesManager.loadInitialData();
                window.faltantesManager.applyFilters();
                window.faltantesManager.showSuccess('Datos actualizados correctamente');
            }
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl + R para refrescar
            if (e.ctrlKey && e.key === 'r') {
                e.preventDefault();
                refreshData();
            }
            
            // Ctrl + F para enfocar la búsqueda
            if (e.ctrlKey && e.key === 'f') {
                e.preventDefault();
                const searchInput = document.querySelector('.dataTables_filter input');
                if (searchInput) {
                    searchInput.focus();
                }
            }
        });
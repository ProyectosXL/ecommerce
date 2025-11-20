$(document).ready(function() {
    let tablaIncidentes;
    let graficoResolucion;
    let graficoCumplimientoSLA;
    let graficoIncidencias;  // NUEVO: Gráfico A
    let graficoIncumplimiento;  // NUEVO: Gráfico B
    let filtroSLAActivo = false;
    let filtrosLeyenda = {  // NUEVO: Estado de filtros de leyenda
        critico: true,
        alerta: true,
        aceptable: true
    };

    const mainContainer = $('.container.py-4');
    const seguimientoTab = $('#seguimiento-tab');
    const reporteTab = $('#reporte-tab');

    reporteTab.on('shown.bs.tab', function() {
        mainContainer.removeClass('container').addClass('container-fluid');
        if (tablaIncidentes) {
            tablaIncidentes.columns.adjust().draw();
        }
    });

    seguimientoTab.on('shown.bs.tab', function() {
        mainContainer.removeClass('container-fluid').addClass('container');
    });

    // NUEVO: Función para renderizar estado SLA con semáforo
    function formatEstadoSLA(estadoSLA, diasSobreSLA) {
        if (estadoSLA === 'fuera') {
            return `<span class="badge-sla badge-sla-fuera" title="Fuera de SLA">
                        <i class="fas fa-circle"></i> Fuera SLA (+${diasSobreSLA}d)
                    </span>`;
        } else if (estadoSLA === 'riesgo') {
            return `<span class="badge-sla badge-sla-riesgo" title="En riesgo de incumplir SLA">
                        <i class="fas fa-circle"></i> Riesgo
                    </span>`;
        } else {
            return `<span class="badge-sla badge-sla-dentro" title="Dentro del SLA">
                        <i class="fas fa-circle"></i> Dentro SLA
                    </span>`;
        }
    }

    function formatEstado(estado) {
        if (estado === 'abierto') return '<span class="badge bg-danger">Abierto</span>';
        if (estado === 'proceso') return '<span class="badge bg-warning text-dark">En Curso</span>';
        if (estado === 'resuelto') return '<span class="badge bg-success">Finalizado</span>';
        return estado;
    }

    // NUEVO: Función para obtener color de fila según estado SLA
    function getRowClass(estadoSLA) {
        if (estadoSLA === 'fuera') return 'table-danger-sla';
        if (estadoSLA === 'riesgo') return 'table-warning-sla';
        return '';
    }

    tablaIncidentes = $('#tabla-reporte-incidentes').DataTable({
        "language": {
            "decimal": "",
            "emptyTable": "No hay información",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
            "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
            "infoFiltered": "(Filtrado de _MAX_ total entradas)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ Entradas",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "Sin resultados encontrados",
            "paginate": {
                "first": "Primero",
                "last": "Ultimo",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        },
"columns": [
            { 
                "data": "estado_sla",
                "render": function(data, type, row) {
                    return formatEstadoSLA(data, row.dias_sobre_sla);
                }
            },
            { "data": "nro_pedido" },
            { "data": "nro_orden" },
            { "data": "fecha_incidente" },
            { "data": "cliente" },
            // --- CAMBIO AQUÍ: Agregamos 'render' para filtrar 'Discrepancia Ge' ---
            { 
                "data": "articulo_original", 
                "render": function(data) {
                    // Si dice "Discrepancia" (Ge o General) o es nulo, mostrar N/A
                    if (!data || data.indexOf('Discrepancia') !== -1) {
                        return 'N/A';
                    }
                    return data;
                }
            },
            // ---------------------------------------------------------------------

            { 
                "data": "articulo_reemplazante",
                "render": function(data) {
                    if (!data || data.trim() === '' || data.indexOf('Discrepancia') !== -1) {
                        return 'N/A';
                    }
                    return data;
                }
            },
            { "data": "deposito_origen" },
            { "data": "nombre_origen" },
            { "data": "warehouse" },
            { "data": "estado", "render": formatEstado },
            { "data": "resolucion" },
            { "data": "dias_abierto" },
            { 
                "data": "dias_sobre_sla",
                "render": function(data) {
                    return data > 0 ? `<span class="text-danger fw-bold">+${data}</span>` : '-';
                }
            }
        ],
        "order": [[12, 'desc'], [11, 'desc']], // Ordenar por días sobre SLA, luego días transcurridos
        "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
               "<'row'<'col-sm-12'tr>>" +
               "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        "rowCallback": function(row, data) {
            // Aplicar clase a toda la fila según estado SLA
            $(row).addClass(getRowClass(data.estado_sla));
        }
    });
    
    new $.fn.dataTable.Buttons(tablaIncidentes, {
        buttons: [
            {
                extend: 'excelHtml5', 
                text: '<i class="fas fa-file-excel me-1"></i> Exportar',
                className: 'btn btn-success', 
                titleAttr: 'Exportar a Excel', 
                title: 'Reporte_de_Incidentes',
                attr: {
                    style: 'height: 38px; display: flex; align-items: center;'
                }
            }
        ]
    });
    tablaIncidentes.buttons().container().appendTo('#export-buttons-container');

    // NUEVO: Funcionalidad de filtro por casos fuera de SLA
    $('#btn-filtrar-fuera-sla').on('click', function() {
        filtroSLAActivo = true;
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            const estadoSLA = tablaIncidentes.row(dataIndex).data().estado_sla;
            return estadoSLA === 'fuera';
        });
        tablaIncidentes.draw();
        $(this).hide();
        $('#btn-limpiar-filtro-sla').css('display', 'flex');
    });

    $('#btn-limpiar-filtro-sla').on('click', function() {
        filtroSLAActivo = false;
        $.fn.dataTable.ext.search.pop();
        tablaIncidentes.draw();
        $('#btn-filtrar-fuera-sla').css('display', 'flex');
        $(this).hide();
    });

    function cargarReporte() {
        const desde = $('#reporte-desde').val();
        const hasta = $('#reporte-hasta').val();
        const estado = $('#reporte-estado').val();

        if (typeof showSpinner === 'function') showSpinner();

        $.ajax({
            url: 'Controller/obtenerReporteIncidentes.php', type: 'GET', data: { desde, hasta, estado },
            success: function(response) {
                if (response.success) {
                    // --- ACTUALIZAR INFO DE SLA DINÁMICAMENTE ---
                    if (response.sla_config) {
                        const slaDias = response.sla_config.sla_dias;
                        const diasRiesgo = response.sla_config.dias_riesgo;
                        $('#sla-dias-value').text(slaDias);
                        $('#sla-info-text').html(
                            `Acuerdo de nivel de servicio de <strong>${slaDias}</strong> días para resolver incidentes. ` +
                            `Alerta cuando quedan ≤ ${diasRiesgo} día(s).`
                        );
                    }
                    
                    // --- KPIs GENERALES ---
                    $('#kpi-total').text(response.kpis.total);
                    $('#kpi-abiertos').text(response.kpis.abierto);
                    $('#kpi-proceso').text(response.kpis.proceso);
                    $('#kpi-resueltos').text(response.kpis.resuelto);

                    // --- NUEVOS KPIs SLA ---
                    const slaMetrics = response.sla_metrics;
                    
                    // Actualizar tarjeta de Abiertos con info de SLA
                    $('#kpi-abiertos-fuera-sla').html(
                        `<i class="fas fa-exclamation-triangle"></i> ${slaMetrics.abiertos_fuera_sla} fuera de SLA`
                    );

                    // Actualizar tarjeta de Finalizados con % cumplimiento SLA
                    if (response.kpis.resuelto > 0) {
                        const pctFinalizadosSLA = ((slaMetrics.resueltos_dentro_sla / response.kpis.resuelto) * 100).toFixed(1);
                        $('#kpi-finalizados-sla').html(
                            `<i class="fas fa-check"></i> ${pctFinalizadosSLA}% cumplió SLA`
                        );
                    } else {
                        $('#kpi-finalizados-sla').html('<i class="fas fa-check"></i> N/A');
                    }

                    // Tarjeta de Cumplimiento SLA
                    const cumplimientoSLA = slaMetrics.porcentaje_cumplimiento;
                    $('#sla-cumplimiento').text(cumplimientoSLA + '%');
                    $('#sla-proximo-objetivo').text(slaMetrics.proximo_objetivo + '%');
                    
                    const gapTexto = slaMetrics.gap_objetivo > 0 
                        ? `-${slaMetrics.gap_objetivo} pts` 
                        : `+${Math.abs(slaMetrics.gap_objetivo)} pts`;
                    $('#sla-gap').text(gapTexto);

                    // Barra de progreso con color dinámico
                    const barraProgreso = $('#sla-cumplimiento-bar');
                    barraProgreso.css('width', cumplimientoSLA + '%').attr('aria-valuenow', cumplimientoSLA);
                    
                    // Aplicar color según el nivel de cumplimiento
                    barraProgreso.removeClass('bg-danger bg-warning bg-success bg-info');
                    if (cumplimientoSLA >= 90) {
                        barraProgreso.addClass('bg-success');
                    } else if (cumplimientoSLA >= 80) {
                        barraProgreso.addClass('bg-info');
                    } else if (cumplimientoSLA >= 70) {
                        barraProgreso.addClass('bg-warning');
                    } else {
                        barraProgreso.addClass('bg-danger');
                    }

                    // Casos en riesgo
                    $('#sla-casos-riesgo').text(slaMetrics.casos_en_riesgo);

                    // Casos fuera de SLA
                    $('#sla-casos-fuera').text(slaMetrics.casos_fuera_sla);
                    $('#sla-casos-fuera-pct').text(`${slaMetrics.porcentaje_fuera_sla}% del total`);

                    // Brecha promedio
                    $('#sla-brecha-promedio').text(slaMetrics.brecha_promedio > 0 
                        ? `+${slaMetrics.brecha_promedio} días` 
                        : '0 días');

                    // --- KPIs ADICIONALES EXISTENTES ---
                    const tasaResolucion = response.kpis.tasa_resolucion.toFixed(1);
                    $('#kpi-tasa-resolucion-bar').css('width', tasaResolucion + '%').attr('aria-valuenow', tasaResolucion);
                    $('#kpi-tasa-resolucion-text').text(tasaResolucion + '%');
                    
                    const tiempoPromedio = response.kpis.tiempo_promedio;
                    $('#kpi-tiempo-promedio').text(tiempoPromedio > 0 ? `${tiempoPromedio} días` : 'N/A');

                    // --- TABLA ---
                    tablaIncidentes.clear().rows.add(response.tablaData).draw();

                    // --- GRÁFICO DE DESGLOSE DE RESOLUCIONES ---
                    const desgloseData = response.desgloseResolucion;
                    const totalResueltos = response.kpis.resuelto;
                    if (graficoResolucion) graficoResolucion.destroy();
                    if (totalResueltos > 0 && Object.keys(desgloseData).length > 0) {
                        $('#grafico-resolucion-container').show();
                        graficoResolucion = new Chart($('#graficoDesgloseResolucion').get(0).getContext('2d'), {
                            type: 'doughnut', data: {
                                labels: Object.keys(desgloseData).map(k => k.charAt(0).toUpperCase() + k.slice(1)),
                                datasets: [{ data: Object.values(desgloseData), backgroundColor: ['#198754', '#dc3545', '#0d6efd', '#ffc107'], borderColor: '#fff', borderWidth: 2 }]
                            }, options: {
                                responsive: true, maintainAspectRatio: false, plugins: {
                                    legend: { display: true, position: 'bottom' },
                                    tooltip: { callbacks: { label: function(c) { let l = c.label || '', v = c.raw, p = totalResueltos > 0 ? ((v / totalResueltos) * 100).toFixed(1) + '%' : '0%'; return `${l}: ${v} (${p})`; } } }
                                }
                            }
                        });
                    } else {
                        $('#grafico-resolucion-container').hide();
                    }

                    // --- NUEVO GRÁFICO: CUMPLIMIENTO DEL SLA ---
                    if (graficoCumplimientoSLA) graficoCumplimientoSLA.destroy();
                    const ctxSLA = $('#graficoCumplimientoSLA').get(0).getContext('2d');
                    graficoCumplimientoSLA = new Chart(ctxSLA, {
                        type: 'doughnut',
                        data: {
                            labels: ['Dentro de SLA', 'Fuera de SLA'],
                            datasets: [{
                                data: [slaMetrics.casos_dentro_sla, slaMetrics.casos_fuera_sla],
                                backgroundColor: ['#28a745', '#dc3545'],
                                borderColor: '#fff',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'bottom'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.raw;
                                            const total = slaMetrics.casos_dentro_sla + slaMetrics.casos_fuera_sla;
                                            const porcentaje = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '0%';
                                            return `${label}: ${value} (${porcentaje})`;
                                        }
                                    }
                                }
                            }
                        }
                    });

                    // --- NUEVO: GRÁFICO A - INCIDENCIAS POR DEPÓSITO ---
                    const rankingIncidencias = response.rankingIncidencias;
                    if (graficoIncidencias) graficoIncidencias.destroy();
                    if (rankingIncidencias && rankingIncidencias.length > 0) {
                        const ctxIncidencias = $('#graficoIncidenciasPorDeposito').get(0).getContext('2d');
                        
                        const labels = rankingIncidencias.map(d => d.nombre);
                        const totales = rankingIncidencias.map(d => d.total);
                        const totalIncidentes = totales.reduce((a, b) => a + b, 0);

                        graficoIncidencias = new Chart(ctxIncidencias, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Incidentes',
                                    data: totales,
                                    backgroundColor: 'rgba(13, 110, 253, 0.7)',  // Color corporativo azul
                                    borderColor: 'rgba(13, 110, 253, 1)',
                                    borderWidth: 2
                                }]
                            },
                            options: {
                                indexAxis: 'y',
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    x: {
                                        beginAtZero: true,
                                        ticks: {
                                            stepSize: 1
                                        },
                                        title: {
                                            display: true,
                                            text: 'Cantidad de Incidentes'
                                        }
                                    }
                                },
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                const dataIndex = context.dataIndex;
                                                const deposito = rankingIncidencias[dataIndex];
                                                const porcentaje = totalIncidentes > 0 
                                                    ? ((deposito.total / totalIncidentes) * 100).toFixed(1) 
                                                    : 0;
                                                return [
                                                    `Total: ${deposito.total} incidentes`,
                                                    `${porcentaje}% del total`,
                                                    `Fuera de SLA: ${deposito.fuera_sla}`,
                                                    `Dentro de SLA: ${deposito.dentro_sla}`
                                                ];
                                            }
                                        }
                                    }
                                },
                                onHover: function(event, activeElements) {
                                    if (activeElements.length > 0) {
                                        const index = activeElements[0].index;
                                        const depositoNombre = labels[index];
                                        resaltarDepositoEnGraficoB(depositoNombre);
                                    } else {
                                        limpiarResaltadoGraficoB();
                                    }
                                }
                            }
                        });
                    }

                    // --- NUEVO: GRÁFICO B - % INCUMPLIMIENTO SLA POR DEPÓSITO ---
                    const rankingDepositos = response.rankingDepositos;
                    const contadoresRiesgo = response.contadores_riesgo;
                    const contadoresRiesgoPct = response.contadores_riesgo_pct;
                    
                    // Actualizar leyenda dinámica
                    $('#count-critico').text(contadoresRiesgo.critico);
                    $('#pct-critico').text(contadoresRiesgoPct.critico + '%');
                    $('#count-alerta').text(contadoresRiesgo.alerta);
                    $('#pct-alerta').text(contadoresRiesgoPct.alerta + '%');
                    $('#count-aceptable').text(contadoresRiesgo.aceptable);
                    $('#pct-aceptable').text(contadoresRiesgoPct.aceptable + '%');

                    if (graficoIncumplimiento) graficoIncumplimiento.destroy();
                    if (rankingDepositos && rankingDepositos.length > 0) {
                        const ctxIncumplimiento = $('#graficoIncumplimientoSLA').get(0).getContext('2d');
                        
                        const labels = rankingDepositos.map(d => d.nombre);
                        const porcentajes = rankingDepositos.map(d => d.porcentaje_incumplimiento);
                        const colores = rankingDepositos.map(d => {
                            if (d.nivel_riesgo === 'critico') return 'rgba(220, 53, 69, 0.8)';
                            if (d.nivel_riesgo === 'alerta') return 'rgba(255, 193, 7, 0.8)';
                            return 'rgba(40, 167, 69, 0.8)';
                        });

                        graficoIncumplimiento = new Chart(ctxIncumplimiento, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: '% Incumplimiento',
                                    data: porcentajes,
                                    backgroundColor: colores,
                                    borderColor: colores.map(c => c.replace('0.8', '1')),
                                    borderWidth: 2
                                }]
                            },
                            options: {
                                indexAxis: 'y',
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    x: {
                                        beginAtZero: true,
                                        max: 100,
                                        ticks: {
                                            callback: function(value) {
                                                return value + '%';
                                            }
                                        },
                                        title: {
                                            display: true,
                                            text: '% Incumplimiento SLA'
                                        },
                                        grid: {
                                            drawBorder: true,
                                            color: function(context) {
                                                // Línea de referencia en 50%
                                                if (context.tick.value === 50) {
                                                    return 'rgba(255, 0, 0, 0.5)';
                                                }
                                                return 'rgba(0, 0, 0, 0.1)';
                                            },
                                            lineWidth: function(context) {
                                                if (context.tick.value === 50) {
                                                    return 2;
                                                }
                                                return 1;
                                            }
                                        }
                                    }
                                },
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                const dataIndex = context.dataIndex;
                                                const deposito = rankingDepositos[dataIndex];
                                                return [
                                                    `% Fuera de SLA: ${deposito.porcentaje_incumplimiento}%`,
                                                    `Incidentes fuera SLA: ${deposito.fuera_sla}`,
                                                    `Incidentes totales: ${deposito.total}`,
                                                    `% Cumplimiento: ${deposito.porcentaje_cumplimiento}%`
                                                ];
                                            }
                                        }
                                    }
                                },
                                onHover: function(event, activeElements) {
                                    if (activeElements.length > 0) {
                                        const index = activeElements[0].index;
                                        const depositoNombre = labels[index];
                                        resaltarDepositoEnGraficoA(depositoNombre);
                                    } else {
                                        limpiarResaltadoGraficoA();
                                    }
                                }
                            }
                        });
                    }

                    // Mostrar insight automático
                    if (response.insight) {
                        $('#insight-text').text(response.insight.mensaje);
                        $('#insight-container').show();
                    } else {
                        $('#insight-container').hide();
                    }

                } else {
                    Swal.fire('Error', 'No se pudieron cargar los datos del reporte.', 'error');
                }
            },
            error: function() { Swal.fire('Error', 'Ocurrió un error en la comunicación con el servidor.', 'error'); },
            complete: function() { if (typeof hideSpinner === 'function') hideSpinner(); }
        });
    }

    // --- FUNCIONES DE SINCRONIZACIÓN ENTRE GRÁFICOS ---
    function resaltarDepositoEnGraficoA(depositoNombre) {
        if (!graficoIncidencias) return;
        const labels = graficoIncidencias.data.labels;
        const index = labels.indexOf(depositoNombre);
        if (index !== -1) {
            // Resaltar la barra correspondiente
            const coloresOriginales = graficoIncidencias.data.datasets[0].backgroundColor;
            const nuevosColores = labels.map((label, i) => 
                i === index ? 'rgba(13, 110, 253, 1)' : 'rgba(13, 110, 253, 0.3)'
            );
            graficoIncidencias.data.datasets[0].backgroundColor = nuevosColores;
            graficoIncidencias.update('none');
        }
    }

    function limpiarResaltadoGraficoA() {
        if (!graficoIncidencias) return;
        const labels = graficoIncidencias.data.labels;
        graficoIncidencias.data.datasets[0].backgroundColor = labels.map(() => 'rgba(13, 110, 253, 0.7)');
        graficoIncidencias.update('none');
    }

    function resaltarDepositoEnGraficoB(depositoNombre) {
        if (!graficoIncumplimiento) return;
        const labels = graficoIncumplimiento.data.labels;
        const index = labels.indexOf(depositoNombre);
        if (index !== -1) {
            const coloresOriginales = graficoIncumplimiento.data.datasets[0].backgroundColor;
            const nuevosColores = coloresOriginales.map((color, i) => 
                i === index ? color.replace('0.8', '1') : color.replace('0.8', '0.3')
            );
            graficoIncumplimiento.data.datasets[0].backgroundColor = nuevosColores;
            graficoIncumplimiento.update('none');
        }
    }

    function limpiarResaltadoGraficoB() {
        if (!graficoIncumplimiento) return;
        // Restaurar colores originales según nivel de riesgo
        aplicarFiltrosLeyenda();
    }

    function aplicarFiltrosLeyenda() {
        if (!graficoIncumplimiento || !graficoIncumplimiento.data) return;
        
        const labels = graficoIncumplimiento.data.labels;
        const datos = graficoIncumplimiento.data.datasets[0].data;
        
        // Obtener datos originales del último response
        const nuevosColores = [];
        const nuevosDatos = [];
        const nuevasLabels = [];
        
        labels.forEach((label, index) => {
            const porcentaje = datos[index];
            let nivelRiesgo = 'aceptable';
            if (porcentaje > 50) nivelRiesgo = 'critico';
            else if (porcentaje >= 20) nivelRiesgo = 'alerta';
            
            // Solo mostrar si el filtro está activo
            if (filtrosLeyenda[nivelRiesgo]) {
                nuevasLabels.push(label);
                nuevosDatos.push(porcentaje);
                
                let color = 'rgba(40, 167, 69, 0.8)'; // Verde
                if (nivelRiesgo === 'critico') color = 'rgba(220, 53, 69, 0.8)';
                else if (nivelRiesgo === 'alerta') color = 'rgba(255, 193, 7, 0.8)';
                nuevosColores.push(color);
            }
        });
        
        graficoIncumplimiento.data.labels = nuevasLabels;
        graficoIncumplimiento.data.datasets[0].data = nuevosDatos;
        graficoIncumplimiento.data.datasets[0].backgroundColor = nuevosColores;
        graficoIncumplimiento.data.datasets[0].borderColor = nuevosColores.map(c => c.replace('0.8', '1'));
        graficoIncumplimiento.update();
    }

    // --- LISTENERS DE LA LEYENDA INTERACTIVA ---
    $('.legend-item').on('click', function() {
        const nivel = $(this).data('nivel');
        filtrosLeyenda[nivel] = !filtrosLeyenda[nivel];
        
        // Toggle visual en la leyenda
        $(this).toggleClass('legend-inactive', !filtrosLeyenda[nivel]);
        
        // Aplicar filtros a los gráficos
        aplicarFiltrosLeyenda();
    });

    const hoy = new Date();
    const hace30Dias = new Date();
    hace30Dias.setDate(hoy.getDate() - 30);
    $('#reporte-hasta').val(hoy.toISOString().split('T')[0]);
    $('#reporte-desde').val(hace30Dias.toISOString().split('T')[0]);

    if ($('#reporte-tab').hasClass('active')) {
        cargarReporte();
        mainContainer.removeClass('container').addClass('container-fluid');
    }
    reporteTab.on('shown.bs.tab', function() { cargarReporte(); });
    $('#btn-aplicar-filtros').on('click', cargarReporte);
    $('#kpi-card-finalizados').on('click', function() { $('#grafico-resolucion-container').slideToggle(); });
});
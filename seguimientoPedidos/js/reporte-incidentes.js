$(document).ready(function() {
    let tablaIncidentes;
    let graficoResolucion;
    let graficoRanking; // NUEVA: Variable para el gráfico de ranking

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

    function formatEstado(estado) {
        if (estado === 'abierto') return '<span class="badge bg-danger">Abierto</span>';
        if (estado === 'proceso') return '<span class="badge bg-warning text-dark">En Curso</span>';
        if (estado === 'resuelto') return '<span class="badge bg-success">Finalizado</span>';
        return estado;
    }

    tablaIncidentes = $('#tabla-reporte-incidentes').DataTable({
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.7/i1n/es-ES.json"
        },
        "columns": [
            { "data": "nro_pedido" }, { "data": "nro_orden" }, { "data": "fecha_incidente" },
            { "data": "cliente" }, { "data": "articulo_faltante" }, { "data": "deposito_origen" },
            { "data": "nombre_origen" }, { "data": "warehouse" },
            { "data": "estado", "render": formatEstado }, { "data": "resolucion" },
            { "data": "dias_abierto" }
        ],
        "order": [[2, 'desc']],
        "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
               "<'row'<'col-sm-12'tr>>" +
               "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    });
    
    new $.fn.dataTable.Buttons(tablaIncidentes, {
        buttons: [
            {
                extend: 'excelHtml5', text: '<i class="fas fa-file-excel"></i> Exportar Excel',
                className: 'btn btn-success', titleAttr: 'Exportar a Excel', title: 'Reporte_de_Incidentes'
            }
        ]
    });
    tablaIncidentes.buttons().container().appendTo('#export-buttons-container');

    function cargarReporte() {
        const desde = $('#reporte-desde').val();
        const hasta = $('#reporte-hasta').val();
        const estado = $('#reporte-estado').val();

        if (typeof showSpinner === 'function') showSpinner();

        $.ajax({
            url: 'Controller/obtenerReporteIncidentes.php', type: 'GET', data: { desde, hasta, estado },
            success: function(response) {
                if (response.success) {
                    $('#kpi-total').text(response.kpis.total);
                    $('#kpi-abiertos').text(response.kpis.abierto);
                    $('#kpi-proceso').text(response.kpis.proceso);
                    $('#kpi-resueltos').text(response.kpis.resuelto);

                    const tasaResolucion = response.kpis.tasa_resolucion.toFixed(1);
                    $('#kpi-tasa-resolucion-bar').css('width', tasaResolucion + '%').attr('aria-valuenow', tasaResolucion);
                    $('#kpi-tasa-resolucion-text').text(tasaResolucion + '%');
                    
                    const tiempoPromedio = response.kpis.tiempo_promedio;
                    $('#kpi-tiempo-promedio').text(tiempoPromedio > 0 ? `${tiempoPromedio} días` : 'N/A');

                    tablaIncidentes.clear().rows.add(response.tablaData).draw();

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

                    // --- NUEVA LÓGICA PARA EL GRÁFICO DE RANKING ---
                    const rankingData = response.rankingDepositos;
                    if (graficoRanking) graficoRanking.destroy();
                    if (rankingData && Object.keys(rankingData).length > 0) {
                        const ctxRanking = $('#graficoRankingDepositos').get(0).getContext('2d');
                        graficoRanking = new Chart(ctxRanking, {
                            type: 'bar', // Gráfico de barras
                            data: {
                                labels: Object.keys(rankingData),
                                datasets: [{
                                    label: 'Nº de Incidentes',
                                    data: Object.values(rankingData),
                                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                indexAxis: 'y', // Hace que el gráfico sea horizontal
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    x: {
                                        beginAtZero: true, // El eje X (conteo) empieza en 0
                                        ticks: {
                                            stepSize: 1 // Asegura que los números sean enteros
                                        }
                                    }
                                },
                                plugins: {
                                    legend: {
                                        display: false // Ocultamos la leyenda, es redundante
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                return ` Incidentes: ${context.raw}`;
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }
                    // --- FIN DE LA NUEVA LÓGICA ---

                } else {
                    Swal.fire('Error', 'No se pudieron cargar los datos del reporte.', 'error');
                }
            },
            error: function() { Swal.fire('Error', 'Ocurrió un error en la comunicación con el servidor.', 'error'); },
            complete: function() { if (typeof hideSpinner === 'function') hideSpinner(); }
        });
    }

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
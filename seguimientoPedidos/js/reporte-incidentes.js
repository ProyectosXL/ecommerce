$(document).ready(function() {
    let tablaIncidentes;

    // Función para formatear el estado con badges
    function formatEstado(estado) {
        if (estado === 'abierto') return '<span class="badge bg-danger">Abierto</span>';
        if (estado === 'proceso') return '<span class="badge bg-warning text-dark">En Curso</span>';
        if (estado === 'resuelto') return '<span class="badge bg-success">Finalizado</span>';
        return estado;
    }

    // Inicializar DataTable
    tablaIncidentes = $('#tabla-reporte-incidentes').DataTable({
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
        },
        "columns": [
            { "data": "nro_pedido" },
            { "data": "nro_orden" },
            { "data": "fecha_incidente" },
            { "data": "cliente" },
            { "data": "articulo_faltante" },
            { "data": "warehouse" },
            { "data": "estado", "render": formatEstado },
            { "data": "resolucion" },
            { "data": "dias_abierto" }
        ],
        "order": [[1, 'desc']]
    });

    // Función para cargar los datos del reporte
    function cargarReporte() {
        const desde = $('#reporte-desde').val();
        const hasta = $('#reporte-hasta').val();
        const estado = $('#reporte-estado').val();

        // Mostrar spinner (si tienes una función global)
        if (typeof showSpinner === 'function') showSpinner();

        $.ajax({
            url: 'Controller/obtenerReporteIncidentes.php',
            type: 'GET',
            data: {
                desde: desde,
                hasta: hasta,
                estado: estado
            },
            success: function(response) {
                if (response.success) {
                    // Actualizar KPIs
                    $('#kpi-total').text(response.kpis.total);
                    $('#kpi-abiertos').text(response.kpis.abierto);
                    $('#kpi-proceso').text(response.kpis.proceso);
                    $('#kpi-resueltos').text(response.kpis.resuelto);

                    const tasaResolucion = response.kpis.tasa_resolucion.toFixed(1);
// LÍNEAS NUEVAS Y CORREGIDAS
// 1. Actualiza solo el ancho de la barra
$('#kpi-tasa-resolucion-bar').css('width', tasaResolucion + '%');

// 2. Actualiza el nuevo elemento de texto
$('#kpi-tasa-resolucion-text').text(tasaResolucion + '%');                    $('#kpi-tasa-resolucion-bar').attr('aria-valuenow', tasaResolucion);
                    
                    const tiempoPromedio = response.kpis.tiempo_promedio.toFixed(1);
                    $('#kpi-tiempo-promedio').text(tiempoPromedio > 0 ? `${tiempoPromedio} días` : 'N/A');

                    // Actualizar Tabla
                    tablaIncidentes.clear();
                    tablaIncidentes.rows.add(response.tablaData);
                    tablaIncidentes.draw();
                } else {
                    Swal.fire('Error', 'No se pudieron cargar los datos del reporte.', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Ocurrió un error en la comunicación con el servidor.', 'error');
            },
            complete: function() {
                // Ocultar spinner
                if (typeof hideSpinner === 'function') hideSpinner();
            }
        });
    }

    // Configurar fechas por defecto
    const hoy = new Date();
    const hace30Dias = new Date();
    hace30Dias.setDate(hoy.getDate() - 30);
    $('#reporte-hasta').val(hoy.toISOString().split('T')[0]);
    $('#reporte-desde').val(hace30Dias.toISOString().split('T')[0]);

    // Cargar reporte al cambiar de pestaña
    $('#reporte-tab').on('shown.bs.tab', function() {
        cargarReporte();
    });

    // Event listener para el botón de filtros
    $('#btn-aplicar-filtros').on('click', function() {
        cargarReporte();
    });
});
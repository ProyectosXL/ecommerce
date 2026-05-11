
// Variables globales
window.paisSeleccionado = 'AR'; // Variable global para el país seleccionado

let articuloReclamado = {
    codigo: '',
    descripcion: '',
    precio: '',
    cantidad: ''
};

let seccionCounter = 1;
let estadoActual = 'abierto';

// Función para abrir el modal de historial - Definida globalmente
function abrirHistorial(codigo, descripcion, precio, cantidad) {
    articuloReclamado.codigo = codigo;
    articuloReclamado.descripcion = descripcion;
    articuloReclamado.precio = precio;
    articuloReclamado.cantidad = cantidad;
    
    document.getElementById('modalArticulo').textContent = descripcion;
    document.getElementById('modalCodigo').textContent = `Código: ${codigo}`;
    document.getElementById('modalPrecio').textContent = `$ ${parseFloat(precio).toLocaleString('es-AR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    })}`;
    document.getElementById('modalCantidad').textContent = cantidad;

    // Limpiar secciones dinámicas
    document.getElementById('seccionesHistorial').innerHTML = '';

    // Verificar el estado actual basado en el badge que viene del servidor
    const estadoBadge = document.querySelector('#estado .badge');
    let currentState = 'abierto'; // default
    
    if (estadoBadge) {
        const badgeText = estadoBadge.textContent.trim().toLowerCase();
        if (badgeText === 'finalizado') {
            currentState = 'resuelto'; // Internamente seguimos usando 'resuelto'
        } else if (badgeText === 'en curso') {
            currentState = 'proceso';
        } else {
            currentState = 'abierto';
        }
    }
    
    estadoActual = currentState;
    
    // Configurar elementos según el estado
    const agregarBtn = document.getElementById('agregarSeccion');
    const btnResolucion = document.getElementById('btnResolucion');
    const seccionResolucion = document.getElementById('seccionResolucion');
    
    if (currentState === 'resuelto') {
        // Reclamo completado - ocultar botones
        if (agregarBtn) agregarBtn.style.display = 'none';
        if (btnResolucion) btnResolucion.style.display = 'none';
    } else {
        // Reclamo abierto o en proceso - mostrar botones
        if (agregarBtn) agregarBtn.style.display = 'block';
        if (btnResolucion) btnResolucion.style.display = 'block';
        if (seccionResolucion) seccionResolucion.style.display = 'none';
    }

    const modal = new bootstrap.Modal(document.getElementById('historialModal'));
    modal.show();
}

// Hacer disponible globalmente
window.abrirHistorial = abrirHistorial;

// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Inicializar Select2
    initializeSelect2();
    
    // Event listener para el toggle de país GLOBAL
    const countryRadios = document.querySelectorAll('input[name="country-global"]');
    countryRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const paisAnterior = window.paisSeleccionado;
            window.paisSeleccionado = this.value;
            
            // Actualizar el campo oculto del formulario de búsqueda (Tab 1)
            const paisInput = document.getElementById('pais-input');
            if (paisInput) {
                paisInput.value = this.value;
            }
            
            console.log('País cambiado de', paisAnterior, 'a', window.paisSeleccionado);
            
            // CORRECCIÓN: Limpiar resultados sin recargar página al cambiar de país (Tab 1)
            const seguimientoTab = document.getElementById('seguimiento-content');
            if (seguimientoTab && seguimientoTab.classList.contains('show', 'active')) {
                const resultsContainer = document.getElementById('search-results-container');
                const numeroInput = document.querySelector('input[name="numero"]');
                
                // Si hay resultados visibles, limpiarlos y resetear el formulario
                if (resultsContainer && resultsContainer.children.length > 0) {
                    console.log('Limpiando resultados de búsqueda al cambiar a', window.paisSeleccionado);
                    
                    // Limpiar resultados
                    resultsContainer.innerHTML = '';
                    
                    // Limpiar campo de búsqueda
                    if (numeroInput) {
                        numeroInput.value = '';
                    }
                    
                    // Mostrar mensaje informativo
                    resultsContainer.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>Se cambió el país a <strong>' + 
                        (this.value === 'AR' ? 'Argentina' : 'Uruguay') + 
                        '</strong>. Por favor, realice una nueva búsqueda.</div>';
                }
            }
            
            // Si estamos en la pestaña de Reporte de Incidentes (Tab 2), recargar el reporte
            const reporteTab = document.getElementById('reporte-content');
            if (reporteTab && reporteTab.classList.contains('show', 'active')) {
                console.log('Recargando reporte con país:', window.paisSeleccionado);
                // Llamar a la función cargarReporte() que está en reporte-incidentes.js
                if (typeof window.cargarReportePorCambioPais === 'function') {
                    window.cargarReportePorCambioPais();
                }
            }
        });
    });
    
    // Agregar event listener al formulario de búsqueda para mostrar spinner
    const searchForm = document.querySelector('form[method="POST"]');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            // Validar que los campos requeridos estén llenos
            const desde = document.querySelector('input[name="desde"]');
            const hasta = document.querySelector('input[name="hasta"]');
            const numero = document.querySelector('input[name="numero"]');
            
            if (desde.value && hasta.value && numero.value.trim()) {
                showSpinner();
            }
        });
    }
});

// Funciones para formatear los resultados de Select2
function formatArticuloResult(articulo) {
    if (!articulo.id || !articulo.element) return articulo.text;
    const dataset = articulo.element.dataset;
    if (!dataset) return articulo.text;
    return `<div class="select2-result-article">
        <strong>${dataset.codigo || ''}</strong>
        ${dataset.descripcion || ''}<br>
        <small>Stock: ${dataset.stock || '0'}</small>
    </div>`;
}

function formatArticuloSelection(articulo) {
    if (!articulo.id || !articulo.element) return articulo.text;
    const dataset = articulo.element.dataset;
    if (!dataset) return articulo.text;
    return `${dataset.codigo || ''} - ${dataset.descripcion || ''}`;
}

// Inicializar Select2
function initializeSelect2() {
    $('#selectArticulo').select2({
        width: '100%',
        placeholder: 'Buscar artículo...',
        dropdownParent: $('#historialModal'),
        language: 'es',
        templateResult: formatArticuloResult,
        templateSelection: formatArticuloSelection,
        escapeMarkup: function(markup) {
            return markup;
        }
    });
}

// Función para actualizar el badge del estado
function actualizarBadgeEstado() {
    const badge = document.querySelector('.estado-actual');
    if (!badge) return;

    badge.classList.remove('bg-danger', 'bg-warning', 'bg-success');
    switch (estadoActual) {
        case 'abierto':
            badge.classList.add('bg-danger');
            badge.textContent = 'Abierto';
            break;
        case 'proceso':
            badge.classList.add('bg-warning');
            badge.textContent = 'En Curso';
            break;
        case 'resuelto':
        case 'finalizado':
            badge.classList.add('bg-success');
            badge.textContent = 'Finalizado';
            break;
    }
}

// Función para mostrar/ocultar spinner
function showSpinner() {
    document.getElementById('spinner').classList.remove('spinner-hidden');
}

function hideSpinner() {
    document.getElementById('spinner').classList.add('spinner-hidden');
}

// Función para validar finalización del reclamo
const checkFinalizar = () => {
    const resolucion = document.getElementById('tipoResolucion')?.value;
    const sucursal = document.getElementById('selectSucursal')?.value;
    const articulo = document.getElementById('selectArticulo')?.value;

    if (!resolucion) {
        alert('Debe seleccionar una resolución.');
        return false;
    }

    // Solo validar sucursal y artículo para "cambio" y "completado"
    if (['cambio', 'completado'].includes(resolucion)) {
        if (!sucursal || !articulo) {
            alert('Debe completar los campos de Sucursal y Artículo.');
            return false;
        }
    }
    
    // Deshabilitar controles
    document.getElementById('agregarSeccion').style.display = 'none';
    document.getElementById('tipoResolucion').disabled = true;
    
    // Solo deshabilitar sucursal y artículo si existen y están visibles
    const selectSucursal = document.getElementById('selectSucursal');
    const selectArticulo = document.getElementById('selectArticulo');
    
    if (selectSucursal && selectSucursal.style.display !== 'none') {
        selectSucursal.disabled = true;
    }
    if (selectArticulo && selectArticulo.style.display !== 'none') {
        selectArticulo.disabled = true;
    }

    return true;
};
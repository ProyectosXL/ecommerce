
// Variables globales
let articuloReclamado = {
    codigo: '',
    descripcion: '',
    precio: '',
    cantidad: ''
};

let seccionCounter = 1;
let estadoActual = 'abierto';

// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Inicializar Select2
    initializeSelect2();
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
            badge.textContent = 'En Proceso';
            break;
        case 'resuelto':
            badge.classList.add('bg-success');
            badge.textContent = 'Resuelto';
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

    if (['cambio', 'completado'].includes(resolucion)) {
        if (!resolucion || !sucursal || !articulo) {
            alert('Debe completar los campos de Resolución, Sucursal y Artículo.');
            return false;
        }
    } else if (resolucion === 'cancelado') {
        if (!resolucion) {
            alert('Debe seleccionar una resolución.');
            return false;
        }
    } else {
        alert('Debe seleccionar una opción válida de resolución.');
        return false;
    }
    
    document.getElementById('agregarSeccion').style.display = 'none';
    document.getElementById('tipoResolucion').disabled = true;
    document.getElementById('selectSucursal').disabled = true;
    document.getElementById('selectArticulo').disabled = true;

    return true;
};
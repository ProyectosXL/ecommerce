let articuloReclamado = {
    codigo: '',
    descripcion: '',
    precio: '',
    cantidad: ''
};
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Variables globales
    let seccionCounter = 1;
    let estadoActual = 'abierto';

    // Inicializar Select2
    $('#selectArticulo').select2({
        width: '100%',
        placeholder: 'Buscar artículo...',
        dropdownParent: $('#historialModal'), // Importante para modales
        language: 'es',
        templateResult: formatArticuloResult,
        templateSelection: formatArticuloSelection,
        escapeMarkup: function(markup) {
            return markup;
        }
    });

    // Funciones para formatear los resultados
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

  
    const btnFinalizar = document.getElementById('botonFinalizar');


});


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

// Spinner
function showSpinner() {
    document.getElementById('spinner').classList.remove('spinner-hidden');
}

function hideSpinner() {
    document.getElementById('spinner').classList.add('spinner-hidden');
}

window.abrirHistorial = function(codigo, descripcion, precio, cantidad) {
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

    estadoActual = 'abierto';
    const nroOrden = document.getElementById('nroOrden').textContent;

    $.ajax({
        url: 'Controller/consultarEstado.php',
        method: 'POST',
        data: {
            nroOrder: nroOrden
        },
        success: function(response) {
        
            response = JSON.parse(response);
            
            if (response) {
                estadoActual = response;
            } 
            actualizarBadgeEstado();

            document.getElementById('seccionesHistorial').innerHTML = '';
            // document.getElementById('seccionResolucion').style.display = '';
            document.getElementById('agregarSeccion').style.display = 'block';
            document.getElementById('btnResolucion').style.display = 'block';

            if(estadoActual == 'resuelto') {
                document.getElementById('seccionResolucion').style.display = ''
                const sucursalSeleccionada = document.getElementById('sucursalSeleccionada')?.textContent;
                const articuloCambioCod = document.getElementById('articuloCambioCod')?.textContent;

                const seccionSucursal = document.getElementById('seccionSucursal');
                seccionSucursal.style.display = 'block';
                selectSucursal.disabled = true;
                selectSucursal.innerHTML = `<option value="${sucursalSeleccionada}">${sucursalSeleccionada}</option>`;

                const seccionArticulo = document.getElementById('seccionArticulo');
                seccionArticulo.style.display = 'block';
                seccionArticulo.disabled = true;

                if ($('#selectArticulo').hasClass('select2-hidden-accessible')) {
                    $('#selectArticulo').select2('destroy');
                }  
                
                const selectArticulo = document.getElementById('selectArticulo');
                selectArticulo.innerHTML = `<option value="${articuloCambioCod}">${articuloCambioCod}</option>`;
                selectArticulo.disabled = true;
                

                document.getElementById('tipoResolucion').disabled = true;
                document.getElementById('agregarSeccion').style.display = 'none';
                document.getElementById('btnResolucion').style.display = 'none';
    
    
            }
    
            const modal = new bootstrap.Modal(document.getElementById('historialModal'));
            modal.show();

        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error en la solicitud AJAX:', textStatus, errorThrown);
        }
    });
    
    
}

// Guardar sección
function guardarSeccion(seccionElement) {
    const comentario = seccionElement.querySelector('.comentario').value;
    if (!comentario.trim()) {
        alert('Debe ingresar un comentario');
        return;
    }
    seccionElement.classList.add('seccion-guardada');
    seccionElement.querySelectorAll('select, textarea').forEach(elem => elem.disabled = true);
    seccionElement.querySelector('.btn-guardar-seccion').style.display = 'none';
    document.getElementById('agregarSeccion').style.display = 'block';

    if (estadoActual === 'abierto') {
        estadoActual = 'proceso';
        actualizarBadgeEstado();
    }

    const fechaCreacion = seccionElement.querySelector('.fecha-creacion');
    if (fechaCreacion) fechaCreacion.textContent = new Date().toLocaleString();
}

// Crear nueva sección
function crearNuevaSeccion() {
    const seccionesContainer = document.getElementById('seccionesHistorial');
    const nuevaSeccionHTML = `
        <div class="seccion-historial border-start border-4 border-primary ps-3 mt-4">
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label"><i class="fas fa-comments me-2"></i>Tipo de Contacto</label>
                    <select class="form-select tipo-contacto">
                        <option value="mail">Mail</option>
                        <option value="whatsapp">WhatsApp</option>
                        <option value="facebook">Facebook</option>
                        <option value="instagram">Instagram</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label"><i class="fas fa-user me-2"></i>Agente</label>
                    <select class="form-select agente">
                        <option value="at">Agustina Taboada</option>
                        <option value="fc">Florencia Consoli</option>
                        <option value="jd">Julieta Dalmeida</option>
                        <option value="ls">Leonel Segovia</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label"><i class="fas fa-comment me-2"></i>Comentario</label>
                    <textarea class="form-control comentario" rows="4"></textarea>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted"><i class="far fa-clock me-1"></i>Creado: <span class="fecha-creacion">${new Date().toLocaleString()}</span></small>
                <button type="button" class="btn btn-primary btn-guardar-seccion" onclick="guardarComentario(this)"><i class="fas fa-save me-1"></i>Guardar Sección</button>
            </div>
        </div>`;
    seccionesContainer.insertAdjacentHTML('beforeend', nuevaSeccionHTML);
    document.getElementById('agregarSeccion').style.display = 'none';
}

// Inicialización del botón Agregar Seguimiento
const agregarSeccion = document.getElementById('agregarSeccion');

// Evento para crear una nueva sección
agregarSeccion?.addEventListener('click', function () {
    crearNuevaSeccion();
});

// Funcionalidad botones
const btnResolucion = document.getElementById('btnResolucion');
const seccionResolucion = document.getElementById('seccionResolucion');
if (btnResolucion) {
    btnResolucion.addEventListener('click', function() {
        seccionResolucion.style.display = 'block';
        btnResolucion.style.display = 'none';
        document.getElementById('agregarSeccion').style.display = 'none';
        document.getElementById('botonFinalizar').style.display = '';
    });
}

    // Evento tipoResolucion
const tipoResolucion = document.getElementById('tipoResolucion');
const seccionSucursal = document.getElementById('seccionSucursal');
const seccionArticulo = document.getElementById('seccionArticulo');
const selectSucursal = document.getElementById('selectSucursal');

tipoResolucion?.addEventListener('change', async function () {
    const resolucion = this.value;

    // Mostrar Sucursal y ocultar Artículo por defecto
    if (['cambio', 'completado'].includes(resolucion)) {
        seccionSucursal.style.display = 'block';
        seccionArticulo.style.display = 'none';
        selectSucursal.innerHTML = '<option value="">Seleccione sucursal...</option>';

        try {
            showSpinner();
            
            const response = await fetch('Controller/traerWarehouse.php');
            const sucursales = await response.json();

            // Poblar selectSucursal
            sucursales.forEach(suc => {
                if (suc[0]?.WAREHOUSE) {
                    selectSucursal.innerHTML += `
                        <option value="${suc[0].WAREHOUSE}">${suc[0].WAREHOUSE}</option>`;
                }
            });
        } catch (error) {
            console.error('Error al cargar sucursales:', error);
            alert('Error al cargar sucursales.');
        } finally {
            hideSpinner();
        }
    } else { 
        seccionSucursal.style.display = 'none';
        seccionArticulo.style.display = 'none';
    }

});
    // Evento para cambio en selectSucursal
selectSucursal?.addEventListener('change', async function () {
    const sucursalSeleccionada = this.value;
    const resolucionSeleccionada = document.getElementById('tipoResolucion').value;

    if (sucursalSeleccionada) {
        try {
            showSpinner();


            if(resolucionSeleccionada != 'completado') {
                $('#selectArticulo').prop('disabled', false);
                // limpiar 
                $('#selectArticulo').val('').trigger('change');

                // Petición al servidor para cargar artículos según la sucursal seleccionada
                const response = await fetch(`Controller/buscarStock.php?sucursal=${sucursalSeleccionada}`);
                const articulos = await response.json();

                // Limpiar y poblar selectArticulo
                const selectArticulo = document.getElementById('selectArticulo');
                selectArticulo.innerHTML = '<option value="">Seleccione artículo...</option>';
                articulos.forEach(art => {
                    if (art[0]?.ARTICULO) {
                        const option = document.createElement('option');
                        option.value = art[0].ARTICULO;
                        option.textContent = `${art[0].ARTICULO} - ${art[0].DESC_CTA_ARTICULO}`;
                        option.dataset.codigo = art[0].ARTICULO;
                        option.dataset.descripcion = art[0].DESC_CTA_ARTICULO;
                        option.dataset.stock = art[0].CANT_STOCK || '0';
                        selectArticulo.appendChild(option);
                    }
                });
            } else if (resolucionSeleccionada === 'completado' && articuloReclamado.codigo) {
                $('#selectArticulo').prop('disabled', true);
                $('#selectArticulo').val('').trigger('change');

                const option = document.createElement('option');
                option.value = articuloReclamado.codigo;
                option.textContent = `${articuloReclamado.codigo} - ${articuloReclamado.descripcion}`;
                option.dataset.codigo = articuloReclamado.codigo;
                option.dataset.descripcion = articuloReclamado.descripcion;
                option.dataset.stock = articuloReclamado.stock || '1';
                selectArticulo.appendChild(option);
                selectArticulo.value = articuloReclamado.codigo;
                $('#selectArticulo').trigger('change');

                // Mostrar el artículo reclamado en el modal
                document.getElementById('modalArticulo').textContent = articuloReclamado.descripcion;
                document.getElementById('modalCodigo').textContent = `Código: ${articuloReclamado.codigo}`;
                document.getElementById('modalPrecio').textContent = `$ ${parseFloat(articuloReclamado.precio).toLocaleString('es-AR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })}`;

                document.getElementById('modalCantidad').textContent = articuloReclamado.cantidad;
            }


            $('#selectArticulo').select2({
                width: '100%',
                placeholder: 'Buscar artículo...',
                dropdownParent: $('#historialModal'),
                language: 'es',
                templateResult: formatArticuloResult,
                templateSelection: formatArticuloSelection,
                escapeMarkup: function (markup) {
                    return markup;
                }
            });



            seccionArticulo.style.display = 'block';


        } catch (error) {
            console.error('Error al cargar artículos:', error);
            alert('Error al cargar los artículos.');
        } finally {
            hideSpinner();
        }
    } else {
        seccionArticulo.style.display = 'none';
    }
});


document.getElementById('seccionesHistorial').addEventListener('click', function (e) {
    if (e.target.classList.contains('btn-guardar-seccion')) {
        const seccionElement = e.target.closest('.seccion-historial');
        guardarSeccion(seccionElement);
    }
});

const guardarComentario = (div) => {
    let seccion = div.parentElement.parentElement
    const nroPedido = $('#nroPedido').text().trim();

    let dataSecciones = [];

    dataSecciones.push({
        comentario: seccion.querySelector('.comentario').value,
        tipo_contacto: seccion.querySelector('.tipo-contacto').value,
        agente: seccion.querySelector('.agente').value
    });
    
    dataSecciones = JSON.stringify(dataSecciones);

    $.ajax({
        url: 'guardarComentario.php', 
        method: 'POST',
        data: {
            dataSecciones: dataSecciones,
            nroPedido: nroPedido,
        },
        success: function(response) {
            response = JSON.parse(response);
        
            if (response.success) {
            Swal.fire({
                icon: "success",
                title: "Comentario guardado exitosamente.",
                showConfirmButton: true,
            }).then(function () {
                // console.log('ok')
            });

            } else {
                alert('Error: ' + (response.error || 'No se pudo guardar el comentario.'));
                console.error(response.sqlsrv_error); 
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert('Error en la solicitud AJAX.');
            console.error('AJAX Error:', textStatus, errorThrown);
        }
    })
}



function guardarReclamo(estado = 'abierto') {
    const resolucion = $('#tipoResolucion').val();
    const sucursal = $('#selectSucursal').val();
    const articulo = $('#selectArticulo').val();
    const selectedText = $('#selectArticulo option:selected').text();
    const textAfterDash = selectedText.split('-')[1]?.trim();
    const seccion = document.querySelectorAll('.seccion-historial')

    let dataSecciones = [];

    seccion.forEach(element => {
        dataSecciones.push({
            comentario: element.querySelector('.comentario').value,
            tipo_contacto: element.querySelector('.tipo-contacto').value,
            agente: element.querySelector('.agente').value
        });
    });
        
    dataSecciones = JSON.stringify(dataSecciones);
    
    const nroPedido = $('#nroPedido').text().trim();
    const fechaHora = $('#fechaHora').text().trim();
    const nroOrden = $('#nroOrden').text().trim();
    const cliente = $('#cliente').text().trim();
    const prepara = $('#prepara').text().trim();
    const modalCantidad = $('#modalCantidad').text().trim();
    const modalCodigo = $('#modalCodigo').text().trim().replace('Código:', '').trim();

    let res = checkFinalizar();

    if (!res) {
        return;
    }

    if (!resolucion || !sucursal || !articulo) {
        alert('Debe completar Resolución, Sucursal y Artículo.');
        return;
    }

    $.ajax({
        url: 'guardarReclamo.php', 
        method: 'POST',
        data: {
            resolucion: resolucion,
            sucursal: sucursal,
            articulo: articulo,
            descripcion: textAfterDash,
            dataSecciones: dataSecciones,
            estado: estado,
            nroPedido: nroPedido,
            fechaHora: fechaHora,
            nroOrden: nroOrden,
            cliente: cliente,
            prepara: prepara,
            modalCantidad: modalCantidad,
            estado: estado,
            modalCodigo: modalCodigo
        },
        success: function(response) {
            response = JSON.parse(response);
        
            if (response.success) {
                Swal.fire({
                    icon: "success",
                    title: "Reclamo guardado exitosamente.",
                    showConfirmButton: true,
                }).then(function () {
                });
                    
                if (estado === 'resuelto') {
                    $('#finalizarReclamo').hide();
                }
                // redirigir
                window.location.href = 'consultaPedido.php';
            } else {
                alert('Error: ' + (response.error || 'No se pudo guardar el reclamo.'));
                console.error(response.sqlsrv_error); 
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert('Error en la solicitud AJAX.');
            console.error('AJAX Error:', textStatus, errorThrown);
        }
    });
}

$('#finalizarReclamo').on('click', function() {
    guardarReclamo('resuelto');

});


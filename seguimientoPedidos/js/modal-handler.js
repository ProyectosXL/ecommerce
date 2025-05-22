
// Función para abrir el modal de historial
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
    actualizarBadgeEstado();
    document.getElementById('seccionesHistorial').innerHTML = '';
    document.getElementById('agregarSeccion').style.display = 'block';
    document.getElementById('seccionResolucion').style.display = 'none';
    document.getElementById('btnResolucion').style.display = 'block';

    const modal = new bootstrap.Modal(document.getElementById('historialModal'));
    modal.show();
};

// Función para guardar una sección
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

// Función para crear una nueva sección
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

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Botón agregar sección
    const agregarSeccion = document.getElementById('agregarSeccion');
    agregarSeccion?.addEventListener('click', function () {
        crearNuevaSeccion();
    });

    // Botón resolución
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

    // Cambio en tipo de resolución
    const tipoResolucion = document.getElementById('tipoResolucion');
    const seccionSucursal = document.getElementById('seccionSucursal');
    const seccionArticulo = document.getElementById('seccionArticulo');
    const selectSucursal = document.getElementById('selectSucursal');

    tipoResolucion?.addEventListener('change', async function () {
        const resolucion = this.value;

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

    // Cambio en sucursal
    selectSucursal?.addEventListener('change', async function () {
        const sucursalSeleccionada = this.value;
        const resolucionSeleccionada = document.getElementById('tipoResolucion').value;

        if (sucursalSeleccionada) {
            try {
                showSpinner();

                if(resolucionSeleccionada != 'completado') {
                    $('#selectArticulo').prop('disabled', false);
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

                    const selectArticulo = document.getElementById('selectArticulo');
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

    // Event listener para botones de guardar sección
    document.getElementById('seccionesHistorial').addEventListener('click', function (e) {
        if (e.target.classList.contains('btn-guardar-seccion')) {
            const seccionElement = e.target.closest('.seccion-historial');
            guardarSeccion(seccionElement);
        }
    });

    // Event listener para finalizar reclamo
    $('#finalizarReclamo').on('click', function() {
        guardarReclamo('resuelto');
    });
});
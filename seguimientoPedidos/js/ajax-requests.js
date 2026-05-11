// Función para guardar comentarios
const guardarComentario = (div) => {
    let seccion = div.parentElement.parentElement;
    const nroPedido = $('#nroPedido').text().trim();
    const nroOrden = $('#nroOrden').text().trim();
    const pais = window.paisSeleccionado || 'AR'; // Obtener país desde variable global

    let dataSecciones = [];

    dataSecciones.push({
        comentario: seccion.querySelector('.comentario').value,
        tipo_contacto: seccion.querySelector('.tipo-contacto').value,
        agente: seccion.querySelector('.agente').value
    });
    
    dataSecciones = JSON.stringify(dataSecciones);

    $.ajax({
        url: 'Controller/guardarComentario.php', 
        method: 'POST',
        data: {
            dataSecciones: dataSecciones,
            nroPedido: nroPedido,
            nroOrden: nroOrden,
            pais: pais // Enviar país
        },
        success: function(response) {
            response = JSON.parse(response);
        
            if (response.success) {
                Swal.fire({
                    icon: "success",
                    title: "Comentario guardado exitosamente.",
                    showConfirmButton: true,
                }).then(function () {
                    // Actualizamos el estado visual a "En Curso" inmediatamente
                    if (typeof actualizarBadgeEstado === 'function') {
                        estadoActual = 'proceso';
                        actualizarBadgeEstado();
                    }
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
    });
};

// Función para guardar reclamo
function guardarReclamo(estado = 'abierto') {
    const resolucion = $('#tipoResolucion').val();
    const sucursal = $('#selectSucursal').val();
    const articulo = $('#selectArticulo').val();
    // La descripción debe ser del artículo FALTANTE (el original), no del artículo de reemplazo
    const descripcionFaltante = $('#modalArticulo').text().trim();
    const seccion = document.querySelectorAll('.seccion-historial');

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
    const warehouse = $('#prepara').text().trim(); // WAREHOUSE viene del campo "Prepara"
    const pais = window.paisSeleccionado || 'AR'; // Obtener país desde variable global

    // Debug temporal - mostrar qué datos se están obteniendo
    console.log('Datos obtenidos del DOM:', {
        nroPedido: nroPedido,
        nroOrden: nroOrden,
        cliente: cliente,
        warehouse: warehouse, // Este es el campo "Prepara"
        modalCodigo: modalCodigo,
        modalCantidad: modalCantidad,
        sucursal: sucursal // Esta es la sucursal seleccionada en el modal
    });

    let res = checkFinalizar();

    if (!res) {
        return;
    }

    // Solo validar sucursal y artículo para resoluciones que lo requieran
    if (['cambio', 'completado'].includes(resolucion)) {
        if (!resolucion || !sucursal || !articulo) {
            alert('Debe completar Resolución, Sucursal y Artículo.');
            return;
        }
    } else if (resolucion === 'cancelado') {
        if (!resolucion) {
            alert('Debe seleccionar una resolución.');
            return;
        }
    }

    $.ajax({
        url: 'Controller/guardarReclamo.php', 
        method: 'POST',
        data: {
            resolucion: resolucion,
            sucursal: sucursal || '',
            articulo: articulo || '',
            descripcion: descripcionFaltante || '',
            dataSecciones: dataSecciones,
            estado: estado,
            nroPedido: nroPedido,
            fechaHora: fechaHora,
            nroOrden: nroOrden,
            cliente: cliente,
            prepara: prepara,
            modalCantidad: modalCantidad,
            modalCodigo: modalCodigo,
            warehouse: warehouse,
            pais: pais // Enviar país
        },
        success: function(response) {
            try {
                // Verificar si la respuesta ya es un objeto (algunas veces jQuery lo parsea automáticamente)
                let parsedResponse;
                if (typeof response === 'string') {
                    parsedResponse = JSON.parse(response);
                } else {
                    parsedResponse = response;
                }
            
                if (parsedResponse.success) {
                    Swal.fire({
                        icon: "success",
                        title: "Reclamo guardado exitosamente.",
                        showConfirmButton: true,
                    }).then(function () {
                        // Redireccionar después de guardar
                        window.location.href = 'index.php';
                    });
                        
                    if (estado === 'resuelto') {
                        $('#finalizarReclamo').hide();
                    }
                } else {
                    alert('Error: ' + (parsedResponse.error || 'No se pudo guardar el reclamo.'));
                    console.error('Server error:', parsedResponse); 
                }
            } catch (parseError) {
                console.error('JSON Parse Error:', parseError);
                console.error('Raw response:', response);
                alert('Error: La respuesta del servidor no es válida. Revisa la consola para más detalles.');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert('Error en la solicitud AJAX.');
            console.error('AJAX Error:', textStatus, errorThrown);
        }
    });
}

// Hacer disponibles las funciones globalmente
window.guardarComentario = guardarComentario;
window.guardarReclamo = guardarReclamo;
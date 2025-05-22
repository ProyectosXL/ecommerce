
// Función para guardar comentarios
const guardarComentario = (div) => {
    let seccion = div.parentElement.parentElement;
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
    });
};

// Función para guardar reclamo
function guardarReclamo(estado = 'abierto') {
    const resolucion = $('#tipoResolucion').val();
    const sucursal = $('#selectSucursal').val();
    const articulo = $('#selectArticulo').val();
    const selectedText = $('#selectArticulo option:selected').text();
    const textAfterDash = selectedText.split('-')[1]?.trim();
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
                    // Redireccionar después de guardar
                    window.location.href = 'consultaPedido.php';
                });
                    
                if (estado === 'resuelto') {
                    $('#finalizarReclamo').hide();
                }
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

// Hacer disponibles las funciones globalmente
window.guardarComentario = guardarComentario;
window.guardarReclamo = guardarReclamo;
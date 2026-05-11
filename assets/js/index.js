function pulsar(e) {
  tecla = (document.all) ? e.keyCode : e.which;
  return (tecla != 13);
  }

function mostrarDatos(){
 var textEnMensaje = document.getElementById("textBox").value;
 document.getElementById("prueba").innerHTML = textEnMensaje;
}

function busquedaRapida() {
	var input, filter, table, tr, td, td2, i, txtValue;
	input = document.getElementById('textBox');
	filter = input.value.toUpperCase();
	table = document.getElementById("table");
	tr = table.getElementsByTagName('tr');
	//tr = document.getElementById('tr');
	
	 for (i = 0; i < tr.length; i++) {
    visible = false;
    /* Obtenemos todas las celdas de la fila, no sólo la primera */
    td = tr[i].getElementsByTagName("td");
	
    for (j = 0; j < td.length; j++) {
      if (td[j] && td[j].innerHTML.toUpperCase().indexOf(filter) > -1) {
        visible = true;
      }
    }
    if (visible === true) {
      tr[i].style.display = "";
    } else {
      tr[i].style.display = "none";
    }
  }
  contar();
}

function filterIncompletos(){
  var input, filter, table, tr, td, td2, i, txtValue;
	table = document.getElementById("table");
	tr = table.getElementsByTagName('tr');
	
	 for (i = 0; i < tr.length; i++) {
    visible = false;
    td = tr[i].querySelectorAll("td#incompleto");

    // console.log($('#buttonIncompletos').css("color"));

    if(document.querySelector("#buttonIncompletos").textContent != 'Todos'){
     
      for (j = 0; j < td.length; j++) {
        if (td[0].querySelector(".incompleto") ) {
          visible = true;
        }
      }

    }else{
      for (j = 0; j < td.length; j++) {
        visible = true;
      }
    }
	

    if (visible === true) {
      tr[i].style.display = "";
    } else {
      tr[i].style.display = "none";
    }
  }
 
  if($('#buttonIncompletos').css("background-color") == 'rgb(220, 53, 69)'){
    $('#buttonIncompletos').css("color", '#dc3545').css("background-color",'#f7f7f7').css("border-style",'#dc3545').css("width",'10vh').css("hover:outline",'none');
    $('#buttonIncompletos').text("Todos");
  }else{
    $('#buttonIncompletos').css("color", '#f7f7f7').css("background-color",'#dc3545').css("border-style",'#dc3545');
    $('#buttonIncompletos').text("Incompletos");
  } 
  contar();
}

function filterCancelados(){
  var input, filter, table, tr, td, td2, i, txtValue;
	table = document.getElementById("table");
	tr = table.getElementsByTagName('tr');
	//console.log(1);
	 for (i = 0; i < tr.length; i++) {
    //console.log(2);
    visible = false;
    td = tr[i].querySelectorAll("td#cancelado");

    //console.log($('#buttonCancelados').css("color"));

    if($('#buttonCancelados').css("background-color") == 'rgb(0, 123, 255)'){
      //console.log(3);
      for (j = 0; j < td.length; j++) {
        if (td[0].querySelector(".cancelado") ) {
          visible = true;
          //console.log(6);
        }
      }
    }else{
     
      for (j = 0; j < td.length; j++) {
        visible = true;
        //console.log(4);
      }
    }
	

    if (visible === true) {
      tr[i].style.display = "";
    } else {
      tr[i].style.display = "none";
    }
  }
 
  if($('#buttonCancelados').css("background-color") == 'rgb(0, 123, 255)'){
    $('#buttonCancelados').css("color", '#007bff').css("background-color",'#f7f7f7').css("border-style",'#007bff').css("width",'10vh').css("hover:outline",'none');
    $('#buttonCancelados').text("Todos");
  }else{
    $('#buttonCancelados').css("color", '#f7f7f7').css("background-color",'#007bff').css("border-style",'#007bff');
    $('#buttonCancelados').text("Sin NC");
  } 
  contar();
}

function filterPendientes(){
  var input, filter, table, tr, td, td2, i, txtValue;
	table = document.getElementById("table");
	tr = table.getElementsByTagName('tr');
	//console.log(1);
	 for (i = 0; i < tr.length; i++) {
    //console.log(2);
    visible = false;
    td = tr[i].querySelectorAll("td#cancelado");

    console.log($('#buttonPendientes').css("background-color"));

    if($('#buttonPendientes').css("background-color") == 'rgb(255, 193, 7)'){
      //console.log(3);
      for (j = 0; j < td.length; j++) {
        if (td[0].querySelector(".pendiente") ) {
          visible = true;
          //console.log(6);
        }
      }
    }else{
     
      for (j = 0; j < td.length; j++) {
        visible = true;
        //console.log(4);
      }
    }
	

    if (visible === true) {
      tr[i].style.display = "";
    } else {
      tr[i].style.display = "none";
    }
  }
 
  if($('#buttonPendientes').css("background-color") == 'rgb(255, 193, 7)'){
    $('#buttonPendientes').css("color", '#ffc107').css("background-color",'#f7f7f7').css("border-style",'#ffc107').css("width",'10vh').css("hover:outline",'none');
    $('#buttonPendientes').text("Todos");
  }else{
    $('#buttonPendientes').css("color", '#f7f7f7').css("background-color",'#ffc107').css("border-style",'#ffc107');
    $('#buttonPendientes').text("Pendientes");
  } 
  contar();
}

var	valores = [];
//usuario = document.getElementById("usuario").value;

function total() {

  //usuario = document.getElementById("usuario").value;
	var x = document.querySelectorAll("#id_tabla input[name='nro_pedido[]']"); //tomo todos los input con name='cantProd[]'
	valor = x.value;
	var i;
	for (i = 0; i < x.length; i++) {
		if (x[i].checked) {
			valores.push(x[i].value);
		}
  }
  // console.log(valores);
  agregar(valores);
  
}



function agregar(a) {
  // console.log(a);


  $.ajax({
    url: 'Controlador/procesarImprimir.php',
    method: 'POST',
    data: {
      
      pedidos: a
    },
    success: function(data) {
      // console.log(data);
    }
  });
  
 valores = [];
 ponerCero();
}


function ponerCero() {
  swal("Listo!", "Ya podes imprimir la etiqueta!", "success");
	var x = document.querySelectorAll("#id_tabla input[name='nro_pedido[]']"); 
	var i;
	for (i = 0; i < x.length; i++) {
		if (x[i].checked) {
			x[i].checked = false;
		}
  }
  
}

const exportar = async () => {
  const params = new URLSearchParams(window.location.search);
  params.delete('pagina');

  const overlay = document.getElementById('exportOverlay');
  const overlayText = overlay.querySelector('p');
  const overlaySmall = overlay.querySelector('small');

  // Validar rango de fechas y determinar si necesita división automática
  const desde = params.get('desde');
  const hasta = params.get('hasta');
  
  if (!desde || !hasta) {
    alert('Por favor seleccione un rango de fechas válido.');
    return;
  }

  const fechaDesde = new Date(desde);
  const fechaHasta = new Date(hasta);
  const diasDiferencia = Math.ceil((fechaHasta - fechaDesde) / (1000 * 60 * 60 * 24));
  const DIAS_POR_CHUNK = 5; // Límite seguro de días por archivo
  
  // Si el rango es mayor a DIAS_POR_CHUNK, dividir automáticamente
  if (diasDiferencia > DIAS_POR_CHUNK) {
    const numChunks = Math.ceil(diasDiferencia / DIAS_POR_CHUNK);
    const confirmar = confirm(
      `📊 Exportación de ${diasDiferencia} días\n\n` +
      `Para evitar errores de timeout, se dividirá automáticamente en ${numChunks} archivos de máximo ${DIAS_POR_CHUNK} días cada uno.\n\n` +
      `Se descargarán ${numChunks} archivos CSV:\n` +
      `• pedidos_parte_1.csv\n` +
      `• pedidos_parte_2.csv\n` +
      (numChunks > 2 ? `• ... (${numChunks - 2} más)\n` : '') +
      `• pedidos_parte_${numChunks}.csv\n\n` +
      `¿Desea continuar?`
    );
    
    if (!confirmar) return;
    
    // Exportación dividida
    overlay.style.display = 'flex';
    
    try {
      const chunks = [];
      let currentDate = new Date(fechaDesde);
      
      // Crear chunks de fechas
      for (let i = 0; i < numChunks; i++) {
        const chunkStart = new Date(currentDate);
        const chunkEnd = new Date(currentDate);
        chunkEnd.setDate(chunkEnd.getDate() + DIAS_POR_CHUNK - 1);
        
        // El último chunk termina en la fecha final
        if (chunkEnd > fechaHasta) {
          chunkEnd.setTime(fechaHasta.getTime());
        }
        
        chunks.push({
          desde: chunkStart.toISOString().split('T')[0],
          hasta: chunkEnd.toISOString().split('T')[0],
          numero: i + 1
        });
        
        currentDate.setDate(currentDate.getDate() + DIAS_POR_CHUNK);
      }
      
      console.log('División en chunks:', chunks);
      
      // Descargar cada chunk
      for (let i = 0; i < chunks.length; i++) {
        const chunk = chunks[i];
        
        if (overlayText) {
          overlayText.innerHTML = `<i class="bi bi-file-earmark-excel-fill" style="color:#28a745;"></i> Descargando parte ${chunk.numero} de ${chunks.length}...`;
        }
        if (overlaySmall) {
          overlaySmall.textContent = `Fechas: ${chunk.desde} al ${chunk.hasta}`;
        }
        
        // Crear parámetros para este chunk
        const chunkParams = new URLSearchParams(params);
        chunkParams.set('desde', chunk.desde);
        chunkParams.set('hasta', chunk.hasta);
        
        console.log(`Descargando chunk ${chunk.numero}/${chunks.length}:`, chunkParams.toString());
        
        try {
          const controller = new AbortController();
          const timeoutId = setTimeout(() => controller.abort(), 300000); // 5 minutos por chunk
          
          const response = await fetch('Controlador/exportarExcel.php?' + chunkParams.toString(), {
            signal: controller.signal,
            cache: 'no-store',
            headers: {
              'Accept': 'text/csv'
            }
          });
          
          clearTimeout(timeoutId);
          
          if (!response.ok) {
            throw new Error(`Error en parte ${chunk.numero}: Código ${response.status}`);
          }
          
          const blob = await response.blob();
          
          if (blob.size === 0) {
            console.warn(`Chunk ${chunk.numero} vacío, omitiendo...`);
            continue;
          }
          
          // Descargar el archivo con nombre de parte
          const filename = `pedidos_${chunk.desde}_al_${chunk.hasta}.csv`;
          const url = URL.createObjectURL(blob);
          const a = document.createElement('a');
          a.href = url;
          a.download = filename;
          document.body.appendChild(a);
          a.click();
          document.body.removeChild(a);
          URL.revokeObjectURL(url);
          
          console.log(`Chunk ${chunk.numero} descargado: ${blob.size} bytes`);
          
          // Pequeña pausa entre descargas para no saturar el navegador
          if (i < chunks.length - 1) {
            await new Promise(resolve => setTimeout(resolve, 1500));
          }
          
        } catch (chunkError) {
          console.error(`Error en chunk ${chunk.numero}:`, chunkError);
          throw new Error(`Error al descargar parte ${chunk.numero} (${chunk.desde} al ${chunk.hasta}): ${chunkError.message}`);
        }
      }
      
      // Éxito
      if (overlayText) {
        overlayText.innerHTML = '<i class="bi bi-check-circle-fill" style="color:#28a745;"></i> ¡Exportación completada!';
      }
      if (overlaySmall) {
        overlaySmall.textContent = `${chunks.length} archivos descargados exitosamente.`;
      }
      
      setTimeout(() => {
        overlay.style.display = 'none';
      }, 2000);
      
    } catch (err) {
      console.error('Error en exportación dividida:', err);
      alert('Error al exportar:\n\n' + err.message + '\n\nAlgunos archivos pueden haberse descargado correctamente.');
      overlay.style.display = 'none';
    }
    
    return;
  }
  
  // Exportación simple (≤ 5 días)
  overlay.style.display = 'flex';
  if (overlayText) overlayText.innerHTML = '<i class="bi bi-file-earmark-excel-fill" style="color:#28a745;"></i> Generando archivo...';
  if (overlaySmall) overlaySmall.textContent = 'Exportando ' + diasDiferencia + ' día' + (diasDiferencia > 1 ? 's' : '') + '...';
  
  console.log('Iniciando exportación con parámetros:', params.toString());

  try {
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 300000); // 5 minutos

    const response = await fetch('Controlador/exportarExcel.php?' + params.toString(), {
      signal: controller.signal,
      cache: 'no-store',
      headers: {
        'Accept': 'text/csv'
      }
    });

    clearTimeout(timeoutId);

    if (!response.ok) {
      throw new Error('El servidor devolvió un error. Código: ' + response.status);
    }

    const blob = await response.blob();
    
    if (blob.size === 0) {
      throw new Error('El archivo está vacío. No hay datos para exportar con los filtros seleccionados.');
    }

    console.log('Archivo descargado exitosamente:', blob.size, 'bytes');

    // Obtener nombre del archivo
    const disposition = response.headers.get('Content-Disposition');
    let filename = 'pedidos.csv';
    if (disposition) {
      const match = disposition.match(/filename="?([^"]+)"?/);
      if (match) filename = match[1];
    }

    // Descargar el archivo
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);

  } catch (err) {
    console.error('Error en exportación:', err);
    
    let mensajeError = 'Error al exportar:\n\n';
    
    if (err.name === 'AbortError') {
      mensajeError += '⏱️ TIEMPO DE ESPERA AGOTADO\n\n';
      mensajeError += 'La exportación tardó más de 5 minutos.\n\n';
      mensajeError += '✅ SOLUCIÓN: Intente con un rango de fechas más pequeño.';
    } else if (err.message && err.message.includes('504')) {
      mensajeError += '⏱️ TIMEOUT DEL SERVIDOR (Error 504)\n\n';
      mensajeError += 'El servidor canceló la conexión.\n\n';
      mensajeError += '✅ SOLUCIÓN: Intente con un rango de fechas más pequeño (1-5 días).';
    } else if (err.message) {
      mensajeError += err.message;
    } else {
      mensajeError += 'Error desconocido. Revise los filtros e intente nuevamente.';
    }
    
    alert(mensajeError);
  } finally {
    document.getElementById('exportOverlay').style.display = 'none';
  }
};

$( document ).ready(function() {
    var btn = document.querySelectorAll('.btn-buscar');
    btn.forEach(el => {
        el.addEventListener("click", ()=>{$("#boxLoading").addClass("loading")});
    })

  $(function () {
  $('[data-toggle="tooltip"]').tooltip()
  })

  $("#busqueda").show();
  $("#textBox").focus();
});


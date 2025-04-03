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

const exportar = () => {

  $("#id_tabla").table2excel({
    // exclude CSS class
    exclude: ".noExl",
    name: "Worksheet Name",
    filename: "Remitos", //do not include extension
    fileext: ".xls", // file extension
  });
  
}

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


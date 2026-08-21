const btnEditar = document.getElementById("btn_edit");
const btnActivar = document.getElementById("btn_active");
const btnAgregarRubro = document.querySelector("#agregarRubro");
const btnAgregarRubroUy = document.querySelector("#agregarRubroUy");


let conexion1;



function guardarForm(db = 'central') {
  conexion1 = new XMLHttpRequest();
  conexion1.onreadystatechange = procesarEventos;
  conexion1.open("POST", `Controller/guardarForm.php?db=${db}`, true);
  conexion1.setRequestHeader(
    "Content-Type",
    "application/x-www-form-urlencoded"
  );
  conexion1.send(retornarDatos(db));
}

function retornarDatos(db = null) {
  let cad = "";
  let rubros = [];
  let cantidad = [];

  let tablaRubros = document.querySelectorAll(".Rubro");
  tablaRubros.forEach((rubro) => {
    rubros.push(rubro.dataset.path);
  });
  let colCantidad = document.querySelectorAll(".cantidad");
  colCantidad.forEach((cant) => {
    cantidad.push(parseInt(cant.value));
  });

  let warehouses = [];
  let checkClass = db == 'uy' ? ".checkWarehouseUy2:checked" : ".checkWarehouse2:checked";
  document.querySelectorAll(checkClass).forEach((chk) => {
    warehouses.push({ warehouse: chk.value, cuenta: chk.dataset.cuenta });
  });

  cad =
    "warehouses=" +
    encodeURIComponent(JSON.stringify(warehouses)) +
    "&rubros=" +
    JSON.stringify(rubros) +
    "&cantidad=" +
    JSON.stringify(cantidad);

  console.log("cadena: " + cad);
  return cad;
}

function procesarEventos() {
  if (conexion1.readyState == 4 && conexion1.status == 200) {
    if (conexion1.responseText.includes("ok")) {
      console.log("eeeee:" + conexion1.responseText);
      Swal.fire({
        icon: "success",
        title: "La matriz de stock de seguridad fue actualizada exitosamente!",
        showConfirmButton: true,
      }).then(function () {
        window.location.reload();
        // console.log('ok')
      });
    } else {
      if (conexion1.responseText.includes("Error")) {
        alert("Los datos se guardaron correctamente");
      }
    }
  }
}

btnAgregarRubro.addEventListener("click", () => {
  if (!document.getElementById("inputRubro").value.includes("Seleccione rubro")) {
    let inputRubro = document.getElementById("inputRubro");
    let rubroSeleccionado = inputRubro.value;
    let nombreRubro = inputRubro.selectedOptions[0].text;
    let tablaRubro = document.getElementById("tablaRubroStockSeguridad");
    let newRow = tablaRubro.insertRow(1);
    let newCell = newRow.insertCell(0);
    newCell.classList = "Rubro";
    newCell.dataset.path = rubroSeleccionado;
    let newText = document.createTextNode(`${nombreRubro}`);
    newCell.appendChild(newText);
    newCell = newRow.insertCell(1);
    let newInput = document.createElement("input");
    newInput.type = "number";
    newInput.classList = "cantidad";
    newCell.appendChild(newInput);
  }
});


btnAgregarRubroUy.addEventListener("click", () => {
  if (!document.getElementById("inputRubroUy").value.includes("Seleccione rubro")) {
    let inputRubroUy = document.getElementById("inputRubroUy");
    let rubroSeleccionado = inputRubroUy.value;
    let nombreRubro = inputRubroUy.selectedOptions[0].text;
    let tablaRubro = document.getElementById("tablaRubroStockSeguridadUy");
    let newRow = tablaRubro.insertRow(1);
    let newCell = newRow.insertCell(0);
    newCell.classList = "Rubro";
    newCell.dataset.path = rubroSeleccionado;
    let newText = document.createTextNode(`${nombreRubro}`);
    newCell.appendChild(newText);
    newCell = newRow.insertCell(1);
    let newInput = document.createElement("input");
    newInput.type = "number";
    newInput.classList = "cantidad";
    newCell.appendChild(newInput);
  }
});

document
  .getElementById("inputWarehouse")
  .addEventListener("change", completarModal);

document
  .getElementById("checkTodosWarehouse")
  .addEventListener("change", (e) => {
    document.querySelectorAll(".checkWarehouse2").forEach((chk) => {
      chk.checked = e.target.checked;
    });
  });

document
  .getElementById("checkTodosWarehouseUy")
  .addEventListener("change", (e) => {
    document.querySelectorAll(".checkWarehouseUy2").forEach((chk) => {
      chk.checked = e.target.checked;
    });
  });

let cuentas;

function cargarCuentas(tipoCuenta) {}

function completarModal(e) {
  conexion1 = new XMLHttpRequest();
  let cuenta;
  let idModal = e.target.id;
  console.log("idModal: " + idModal);
  let inputCuenta;
  let warehouse = document.getElementById(`${idModal}`).value;
  console.log("id: " + warehouse);
  let descripcionWarehouse=((document.getElementById(`${idModal}`).selectedOptions[0].innerHTML).split(" - "))[1];//obtengo el local del warehouse

  inputCuenta = document.getElementById("inputCuenta");
  tipoCuenta = "Activar";

  conexion1.onreadystatechange = () => {
    if (conexion1.readyState == 4 && conexion1.status == 200) {

      console.log(cuentas);

      cuentas = JSON.parse(conexion1.responseText);
      cuenta = cuentas[0];
      warehouse += cuenta["DESCRIPCION"];
      inputCuenta.value = cuenta["VTEX_CUENTA"];
      inputCuenta.textContent = cuenta["VTEX_CUENTA"];
    } else {
      console.log("aguanta");
    }
  };

  conexion1.open(
    "GET",
    "Class/cuenta.php?traerCuenta=1&tipo=" + tipoCuenta + "&warehouse=" + warehouse + "&descripcionWarehouse=" + descripcionWarehouse,
    true
  );
  conexion1.send();
}

//Búsqueda rápida table//
function myFunction() {
  var input, filter, table, tr, td, td2, i, txtValue;
  input = document.getElementById("textBox");
  filter = input.value.toUpperCase();
  table = document.getElementById("table");
  tr = table.getElementsByTagName("tr");
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
}

//Limpiar formulario modal al cerrar//
var btnClose = document.querySelectorAll(".btnClose");

btnClose.forEach((el) => el.addEventListener("click", limpiarForm));

function limpiarForm() {
  document.querySelectorAll(".checkWarehouse2").forEach((chk) => (chk.checked = false));
  document.getElementById("checkTodosWarehouse").checked = false;
  document.getElementById("inputRubro").options.selectedIndex = 0;
  let filas = document
    .getElementById("tablaRubroStockSeguridad")
    .getElementsByTagName("tr");
  for (let i = filas.length - 1; i > 0; i--) {
    filas[i].remove();
  }

  document.querySelectorAll(".checkWarehouseUy2").forEach((chk) => (chk.checked = false));
  document.getElementById("checkTodosWarehouseUy").checked = false;
  document.getElementById("inputRubroUy").options.selectedIndex = 0;
  let filasUy = document
    .getElementById("tablaRubroStockSeguridadUy")
    .getElementsByTagName("tr");
  for (let i = filasUy.length - 1; i > 0; i--) {
    filasUy[i].remove();
  }
}

//Capturar valores de formulario modal y enviar//

function activarWarehouse(db = 'central') {

  if(db == 'central'){

    var inputWarehouse = document.getElementById("inputWarehouse").value;
    var inputCuenta = document.getElementById("inputCuenta").textContent;

  }else{

    var inputWarehouse = document.querySelector("#inputWarehouseUy").value;
    var inputCuenta = document.querySelector("#inputCuentaUy").value;

  }

  if (inputWarehouse != "" && inputCuenta != "") {
    Swal.fire({
      icon: "info",
      title: "Desea guardar el formulario?",
      zoom: 1.1,
      showDenyButton: true,
      showCancelButton: true,
      confirmButtonText: "Guardar",
      denyButtonText: `No guardar`,
    }).then((result) => {
      /* Read more about isConfirmed, isDenied below */
      if (result.isConfirmed) {
        let env = 1;
        let url = env == 1 ? `guardarForm.php?db=${db}` : "test.php";
        $.ajax({
          url: "controller/" + url,
          method: "POST",
          data: {
            warehouse: inputWarehouse,
            cuenta: inputCuenta,
          },
        });
        Swal.fire("Warehouse activado correctamente!", "", "success").then(
          function () {
            window.location = "index.php";
          }
        );
      } else if (result.isDenied) {
        Swal.fire("El formulario no fue guardado", "", "info").then(
          function () {
            window.location = "index.php";
          }
        );
      }
    });
  } else {
    Swal.fire({
      icon: "info",
      title: "Atención",
      text: "Debe llenar todos los campos!",
    });
  }
}

const cambiarEntorno = (t) =>{
  let spinner = document.querySelector("#boxLoading");

  spinner.classList.add("loading");

  if(t.checked == false){

    document.querySelector("#btn_active").setAttribute("data-target","#modalActiveUruguay")

    document.querySelector("#btn_edit").setAttribute("data-target","#modalParametersUy")

    $.ajax({
      url: "Controller/stockDeSeguridadController.php?accion=cambiarEntornoUy",
      method: "GET",
      success: function (data) {
        data = JSON.parse(data);
        let tabla = document.getElementById('table');

        tabla.innerHTML = '';

       

        data.forEach((row,x) => {
          const keys = Object.keys(row);
      
          let text  = `
          <tr>
          <td style="display:none;">${row['ID']}</td>
          <td style="display:none;">${row['WAREHOUSE_ID']}</td>
          <td>${row['VTEX_CUENTA']}</td>
          <td>${row['DESC_SUCURSAL']}</td>
           `;


          keys.forEach(element => {
            if(isNaN(element)){
              if (element.trim() != 'ID' && element != 'WAREHOUSE_ID'  && element != 'VTEX_CUENTA' && element != 'DESC_SUCURSAL') {        
                text += `<td><input type="number" class="inputNumber" name="${element}" value="${row[element]}" disabled></td>`;
              }
            }
          });


          text += `</tr>`;
          tabla.innerHTML += text;
        });
        
       
        spinner.classList.remove("loading")
      }


    });
  }else{

    document.querySelector("#btn_active").setAttribute("data-target","#modalActive")
    document.querySelector("#btn_edit").setAttribute("data-target","#modalParameters")

    $.ajax({
      url: "Controller/stockDeSeguridadController.php?accion=cambiarEntornoArg",
      method: "GET",
      success: function (data) {
        data = JSON.parse(data);
        let tabla = document.getElementById('table');
        tabla.innerHTML = '';

        data.forEach((row) => {
  
          const keys = Object.keys(row);

          
          let text  = `
          <tr>
          <td style="display:none;">${row['ID']}</td>
          <td style="display:none;">${row['WAREHOUSE_ID']}</td>
          <td>${row['VTEX_CUENTA']}</td>
          <td>${row['DESC_SUCURSAL']}</td>
           `;

           
          keys.forEach(element => {
            if(isNaN(element)){
              if (element.trim() != 'ID' && element != 'WAREHOUSE_ID'  && element != 'VTEX_CUENTA' && element != 'DESC_SUCURSAL') {   
                text += `<td><input type="number" class="inputNumber" name="${element}" value="${row[element]}" disabled></td>`;
              }
            }
          });


          text += `</tr>`;
          tabla.innerHTML += text;

        });
        spinner.classList.remove("loading")
      }


    });

  }
}

const completarModalUY = (e) => {

  let valor  = e.querySelectorAll("option")[e.selectedIndex].getAttribute("valor-cuenta")
  document.querySelector("#inputCuentaUy").value = valor;

}


function modificar(){
    var pedido = document.getElementById("pedidoModificar").value;
    var viejo = document.getElementById("articuloViejoModificar").value;
    var nuevo = document.getElementById("articuloNuevoModificar").value;
    modificar2(pedido, viejo, nuevo);

}

function modificar2(pedido, viejo, nuevo) {
    console.log(pedido+viejo+nuevo);
  

    Swal.fire({
      title: "¿Estás seguro de modificar el pedido?",
      text: "Vas a modificar los artículos del pedido: " + pedido,
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Sí, modificar pedido",
      cancelButtonText: "No, cancelar",
      dangerMode: true,
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: 'Controlador2/modificar.php',
          method: 'POST',
          data: {
            pedido: pedido,
            viejo: viejo,
            nuevo: nuevo
          },
          success: function(data) {
            console.log(data);
            Swal.fire("Pedido modificado", "", "success");
          },
          error: function() {
            Swal.fire("Error al modificar el pedido", "", "error");
          }
        });
      } else {
        Swal.fire("Pedido NO modificado", "", "info");
      }
    });
    



      ponerCero()
    
   
}

function cantidad(){
    var pedido = document.getElementById("pedidoCantidad").value;
    var viejo = document.getElementById("articuloViejoCantidad").value;
    var cant = document.getElementById("cantidadCantidad").value;

    cantidad2(pedido, viejo, cant);

}

function cantidad2(pedido, viejo, cant) {
    console.log(pedido+viejo+cant);
  

    Swal.fire({
      title: "¿Estás seguro de modificar el pedido?",
      text: "Vas a modificar la cantidad del pedido: " + pedido,
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Sí, modificar pedido",
      cancelButtonText: "No, cancelar",
      dangerMode: true,
    })
      .then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: 'Controlador2/cantidad.php',
            method: 'POST',
            data: {
              pedido: pedido,
              viejo: viejo,
              cant: cant
            },
            success: function (data) {
              console.log(data);
              Swal.fire("Pedido modificado", "", "success");
            },
            error: function () {
              Swal.fire("Error al modificar el pedido", "", "error");
            }
          });
        } else {
          Swal.fire("Pedido NO modificado", "", "info");
        }
      });
  
    ponerCero();
    
}

function eliminar(){
    var pedido = document.getElementById("pedidoEliminar").value;
    var viejo = document.getElementById("articuloViejoEliminar").value;

    eliminar2(pedido, viejo);

}

function eliminar2(pedido, viejo) {
    console.log(pedido+viejo);
  

    swal({
        title: "Estas seguro de eliminar el articulo?",
        text: "Vas a eliminar el articulo "+viejo+" del pedido: "+pedido,
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) {
            $.ajax({
                url: 'Controlador2/eliminar.php',
                method: 'POST',
                data: {
                  
                  pedido: pedido,
                  viejo: viejo
                },
                success: function(data) {
                  console.log(data);
                }
              });
          swal("Articulo eliminado del pedido", {
            icon: "success",
          });
        } else {
          swal("Articulo NO eliminado");
        }
      });

   
      ponerCero()
   
}

function ponerCero(){
    document.getElementById("pedidoModificar").value = '';
    document.getElementById("articuloViejoModificar").value = '';
    document.getElementById("articuloNuevoModificar").value = '';
    document.getElementById("pedidoCantidad").value = '';
    document.getElementById("articuloViejoCantidad").value = '';
    document.getElementById("cantidadCantidad").value = '';
    document.getElementById("pedidoEliminar").value = '';
    document.getElementById("articuloViejoEliminar").value = '';
}


$( window ).on( "load", function() {

  Swal.fire({
    title: "Escribir contraseña:",
    input: "password",
    inputPlaceholder: "Escriba la constraseña de administrador",
    showCancelButton: true,
    cancelButtonText: "Cancelar",
    confirmButtonText: "Aceptar",
    preConfirm: (password) => {
      if (password === "Admin") {
        return true; // La promesa se resolverá correctamente
      } else {
        Swal.showValidationMessage("Contraseña incorrecta");
        window.location.href = "../index.php";
        return false; // La promesa se rechazará
      }
    },
  })
  

});

function buscarPedido(pedido){
  if(pedido!=''){
    console.log(pedido);
    $.ajax({
      url: 'Controlador2/buscar.php',
      method: 'POST',
      data: {
        pedido: pedido
      },
      success: function(data) {
        if(data==0){
          swal("El pedido: "+pedido+" no existe o le falta un espacio en blanco");
        }
      }
    });
  }
}



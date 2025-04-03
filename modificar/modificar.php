<!doctype HTML>
<html>
<head>
<title>Pedidos Ecommerce Modificar</title>
<meta charset="UTF-8"></meta>
<link rel="shortcut icon" href="../../css/icono.jpg" />
<?php 
  require_once $_SERVER['DOCUMENT_ROOT'].'/ecommerce/assets/css/css.php';

?> 

</head>

<body >
<div class="container mt-3">

<table class="table table-dark">
  <thead>
    <tr>
      <th scope="col">OPCION</th>
      <th scope="col">PEDIDO</th>
      <th scope="col">ARTICULO VIEJO</th>
      <th scope="col">ARTICULO NUEVO</th>
      <th scope="col">CANTIDAD</th>
      <th scope="col">CONFIRMAR</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">MODIFICAR</th>
      <td><input type="text" class="form-control" placeholder="Pedido" id="pedidoModificar" onblur="buscarPedido(value)"></td>
      <td><input type="text" class="form-control" placeholder="Articulo viejo" id="articuloViejoModificar"></td>
      <td><input type="text" class="form-control" placeholder="Articulo nuevo" id="articuloNuevoModificar"></td>
      <td><input type="text" class="form-control" id="cantidadModificar" readonly></td>
      <td><button type="button" class="btn btn-info" onClick="modificar()">Modificar</button></td>
    </tr>
    <tr>
      <th scope="row">CANTIDAD</th>
      <td><input type="text" class="form-control" placeholder="Pedido" id="pedidoCantidad" onblur="buscarPedido(value)"></td>
      <td><input type="text" class="form-control" placeholder="Articulo" id="articuloViejoCantidad"></td>
      <td><input type="text" class="form-control" id="articuloNuevoCantidad" readonly></td>
      <td><input type="text" class="form-control" placeholder="Cantidad" id="cantidadCantidad"></td>
      <td><button type="button" class="btn btn-info" onClick="cantidad()">Cantidad</button></td>
    </tr>
    <tr>
      <th scope="row">ELIMINAR</th>
      <td><input type="text" class="form-control" placeholder="Pedido" id="pedidoEliminar" onblur="buscarPedido(value)"></td>
      <td><input type="text" class="form-control" placeholder="Articulo" id="articuloViejoEliminar"></td>
      <td><input type="text" class="form-control" id="articuloNuevoEliminar" readonly></td>
      <td><input type="text" class="form-control" id="cantidadEliminar" readonly></td>
      <td><button type="button" class="btn btn-info" onClick="eliminar()">Eliminar</button></td>
    </tr>
  </tbody>
</table>



<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/ecommerce/assets/js/js.php';
?>
</body>
</html>	
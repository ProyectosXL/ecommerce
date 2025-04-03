<?php

include 'metodos.php';
$suc = $_GET['suc'];
$articulos = articulos($suc);

?>
<!doctype HTML>
<html>
<head>
<title>Pedidos Ecommerce</title>
<meta charset="UTF-8"></meta>
<link rel="shortcut icon" href="../../../../css/icono.jpg" />
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>


<link rel="stylesheet" href="Vista/style.css">

</head>
<body>
<div class="container">

<h2 class="text-center mt-1">Articulos por local</h2>


<div class="row mt-3 mb-3" >

    <div class="col"></div>
    <div class="col"><input type="text" class="form-control form-control-sm" onkeyup="busquedaRapida()" id="textBox" name="factura" placeholder="Sobre cualquier campo.." autofocus></div>
    <div class="col"></div>

</div>

<table id="tabla" class="table table-striped" >
    <thead>
        <th style="width:30%">CODIGO</th>
        <th style="width:40%">DESCRIPCION</th>
        <th style="width:30%">STOCK</th>
    </thead>
    <tbody id="table">
        <?php foreach ($articulos as $key => $value) { ?>
            <tr>
                <td><?= $value['COD_ARTICU']?></td>
                <td><?= $value['DESCRIPCIO']?></td>
                <td><?= $value['CANT_STOCK']?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>




</div>
</body>

<script src="main.js"> </script>

</html>
<?php

include 'metodos.php';
$locales = locales();
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

<h2 class="text-center mt-1">Seleccionar sucursales para mostrar el Mercadolibre</h2>

<form action="agregarLocales.php" method="post">
<div class="row mt-3 mb-3" >

    <div class="col"></div>
    <div class="col"><input type="text" class="form-control form-control-sm" onkeyup="busquedaRapida()" id="textBox" name="factura" placeholder="Sobre cualquier campo.." autofocus></div>
    <div class="col text-center"><input type="submit" class="btn btn-primary btn-sm" value="Agregar locales"></div>
    <div class="col"></div>

</div>
<table id="tabla" class="table table-striped" style="width:66%" align="center">
    <thead>
        <th style="width:30%">NRO SUCURSAL</th>
        <th style="width:40%">NOMBRE</th>
        <th style="width:30%">HABILITADO</th>
    </thead>
    <tbody id="table">
        <?php foreach ($locales as $key => $value) { ?>
            <tr>
                <td><?= $value['NRO_SUCURSAL']?></td>
                <td><a href="articulosLocales.php?suc=<?= $value['NRO_SUCURSAL']?>"><?= $value['DESC_SUCURSAL']?></a></td>
                <td><input type="checkbox"  <?php if ($value['HABILITADO']){ echo 'checked';}?> name="locales[<?= $value['NRO_SUCURSAL']?>]" value="<?= $value['NRO_SUCURSAL']?>" ></td>
            </tr>
        <?php } ?>
    </tbody>
</table>
    
</form>



</div>
</body>

<script src="main.js"> </script>

</html>
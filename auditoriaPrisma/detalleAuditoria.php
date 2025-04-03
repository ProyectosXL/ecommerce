<?php

include 'Controlador/controlador.php';
$auditoria = auditoriaDetalle($_GET['mailClient']);

?>
<!doctype HTML>
<html>
<head>
<title>Pedidos Ecommerce</title>
<meta charset="UTF-8"></meta>
<link rel="shortcut icon" href="../../../../css/icono.jpg" />
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.3.0/css/select.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.dataTables.min.css">
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>


</head>
<body>
<div class="container-fluid">

<h2 class="text-center mt-1 mb-3">Detalle Auditoria de Prisma</h2>


<table id="tableData" class="table table-striped table-sm" align="center">
    <thead>
        <th >RAZON SOCIAL</th>
        <th >FECHA PED</th>
        <th >NRO_PEDIDO</th>
        <th >TARJETA</th>
        <th >DOMICILIO</th>
        <th >TELEFONO_1</th>
        <th >TELEFONO_2</th>
        
        <th >FACTURA</th>
        <th >GUIA</th>
        <th >AUTORIZA</th>
        <th >NO AUTORIZA</th>
    </thead>
    <tbody id="table">
        <?php foreach ($auditoria as $key => $value) { ?>
            <tr>
                <td><?= $value['RAZON_SOCI']?></td>
                <td><?= $value['FECHA_PEDI']?></td>
                <td><?= $value['NRO_PEDIDO']?></td>
                <td><?= $value['TARJETA']?></td>
                <td><?= $value['DOMICILIO']?></td>
                <td><?= $value['TELEFONO1_ENTREGA']?></td>
                <td><?= $value['TELEFONO2_ENTREGA']?></td>
                <td><?= $value['N_COMP']?></td>
                <td><?= $value['GC_GDT_NUM_GUIA']?></td>
                <td><input type="checkbox" name="a" id="b" value="c" <?php if($value['AUTORIZA']== 'si'){echo 'checked';}?> ></td>
                <td><input type="checkbox" name="a" id="b" value="c" <?php if($value['NO_AUTORIZA']== 'si'){echo 'checked';}?> ></td>
               
            </tr>
        <?php } ?>
    </tbody>
</table>
    

<button class="btn btn-success" onClick="enviar('<?= $_GET['mailClient']?>')">Actualizar</button>




</div>

<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>


<script type="text/javascript" src="Controlador/main.js"></script>

<script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/select/1.3.0/js/dataTables.select.min.js"></script>

<script type="text/javascript" src="https://cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
</body>

</html>
<?php

include 'Controlador/buscar_faltantes.php';
include 'mail/enviar.php';
$faltantes = new Faltante();

$list = $faltantes->buscarFaltantes();


$string = "
<html> 

<body> 

<table>
<thead>
    <th>Tienda</th>
    <th>Nro Orden Ecommerce</th>
    <th>Nro Pedido Tango</th>
    <th>Nombre Cliente</th>
    <th>Codigo Articulo</th>
    <th>Descripcion Articulo</th>
    <th>Cantidad</th>
    <th>01</th>
</thead>
<tbody>
<?php
";


foreach($list as $pendientes){
    $string .='<tr>';
    $string .='<td>'.$pendientes[1].'</td>';
        $string .= '<td>'.$pendientes[2].'</td>';
        $string .= '<td>'.$pendientes[3].'</td>';
        $string .= '<td>'.$pendientes[7].'</td>';
        $string .= '<td>'.$pendientes[8].'</td>';
        $string .= '<td>'.$pendientes[9].'</td>';
        $string .= '<td>'.$pendientes[10].'</td>';
        $string .= '<td>'.$pendientes[30].'</td>';
    $string .= '</tr>';
}

$string .= "
</tbody>
</table>

</body> 

</html>
";

//  echo $string;
enviarMail($string);





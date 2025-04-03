<!doctype HTML>
<html>
<head>
<title>Rotulos De Despacho</title>
<meta charset="UTF-8"></meta>
<link rel="shortcut icon" href="../css/icono.jpg" />
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<link rel="stylesheet" href="style.css">

<style rel="stylesheet">
td{
	font-size:11px;
}
</style>

</head>
<body>
<div class="container-fluid">

<?php
$cliente = '%'.$_GET['cliente'].'%';
$tienda = '%'.$_GET['tienda'];
$pedido = '%'.substr($_GET['pedido'], -10, 15);
$desde =  $_GET['desde'];
$hasta =  $_GET['hasta'];

include 'actua_comprobante.php';
actua_comprobante();


?>
<!--
<div class="d-flex justify-content-between mt-1">
<div ><h3>Tracking Pedidos Mayoristas</h3></div>
<div ><button type="button" class="btn btn-primary"onclick="window.location.href='index.php'">Atras</button></div>
</div>  
-->



<div class="row justify-content-md-center mb-2">

	<div class="row mr-1 ml-1">

		<div class="col mt-1">
			
				
				<button onclick="location.href='index.php?desde=<?php echo $_GET['desde'] ;?>&hasta=<?php echo $_GET['hasta'] ;?>&cliente=<?php echo $cliente ;?>&tienda=<?php echo $tienda ;?>&pedido=<?php echo $pedido ;?>'" class="btn btn-info btn-sm">Atras</button>
			
		</div>

	</div>
	
	<div class="font-weight-bold mt-1">
	Rotulos de Preparacion
	</div>

<form method="post" action="imprimirRotuloPreparacion.php">
	
	<div class="row mr-1 ml-1">

		<div class="col mt-1">
			
				<input type="submit" class="btn btn-info btn-sm" value="Imprimir">
				
			
		</div>

	</div>

</div>

<?php


//echo $desde.' '.$hasta.'- guia:'.$guia.' -cliente:'.$cliente.' -estado:'.$estado;

$dsn = '1 - CENTRAL';
$user = 'sa';
$pass = 'Axoft1988';

$sql = "
SET DATEFORMAT YMD

SELECT DISTINCT
ORIGEN, NRO_ORDEN_ECOMMERCE, FECHA_PEDIDO, NRO_PEDIDO,
RAZON_SOCIAL, TOTAL_PEDIDO, OBSERV_AUDITORIA, NRO_COMP
FROM SOF_AUDITORIA
WHERE FECHA_PEDIDO >= GETDATE()-90
AND FECHA_PEDIDO BETWEEN '$desde' AND '$hasta'
AND ORIGEN LIKE UPPER('$tienda')
AND NRO_ORDEN_ECOMMERCE LIKE '$pedido'
AND RAZON_SOCIAL LIKE '$cliente'

ORDER BY ORIGEN DESC, FECHA_PEDIDO DESC, NRO_PEDIDO
";

$cid=odbc_connect($dsn, $user, $pass);

if (!$cid){
	exit("<strong>Ha ocurrido un error tratando de conectarse con el origen de datos.</strong>");
}

ini_set('max_execution_time', 300);
$result=odbc_exec($cid,$sql)or die(exit("Error en odbc_exec"));

//echo '*'.$desde.'*'.$hasta.'*'.$tienda.'*'.$pedido.'*'.$cliente.'<br>';
//echo str_replace('H', 'A', 'HOLA');

?>

<div style="width:100%; padding-bottom:5%; margin-bottom:5%" >

<table class="table table-striped table-fh table-9c">
<thead>
	<tr>
		<th width="4%" class="h6">TIENDA</th>
		<th width="9%" class="h6">NRO<BR>ORDEN</th>
		<th width="9%" class="h6">FECHA<BR>PEDIDO</th>
		<th width="9%" class="h6">PEDIDO</th>
		<th width="4%" class="h6" style="width: 20px">SELEC</th>
		<th width="12%" class="h6">NOMBRE</th>
		<th width="9%" class="h6">IMPORTE</th>
		<th width="9%" class="h6">MEDIO<BR>PAGO</th>
		<th width="9%" class="h6">NRO<BR>FACT</th>
	</tr>
</thead>
<tbody>
<?php
while($v=odbc_fetch_array($result)){
	$i = 0;
?>
		<div class="row">
		
		<?php
			if($v['NRO_COMP'] == '' ){
				echo '<tr style="font-weight:bold;color:#FE2E2E">';
			}else{
				echo '<tr>';
			}
		?>

			<td width="4%"><?php echo $v['ORIGEN']?></td>
			<td width="9%"><?php echo $v['NRO_ORDEN_ECOMMERCE']?></td>
			<td width="9%"><?php echo $v['FECHA_PEDIDO']?></td>
			<td width="9%"><?php echo $v['NRO_PEDIDO']?></td>
			<!--<td width="4%"> <input type="checkbox" name="selec-<?php //echo $v['NRO_PEDIDO']?>"> </td>-->
			<!--<td width="4%" hidden> <input type="hidden" name="selec[]" value="off"> </td>-->
			<td width="4%"> <input type="checkbox" name="selec[]" value="<?php echo $v['NRO_PEDIDO']?>"> </td>
			
			<td width="12%"><?php echo $v['RAZON_SOCIAL']?></td>
			<td width="9%" hidden><input type="text" name="nro_pedido[]" value="<?php echo $v['NRO_PEDIDO']?>"></td>
			<td width="9%"><?php echo '$ '.number_format($v['TOTAL_PEDIDO'] , 0, '', '.')?></td>
			<td width="9%"><?php echo $v['OBSERV_AUDITORIA'];?></td>
			<td width="9%"><?php echo $v['NRO_COMP']?></td>
			
		</tr>
		</div>
<?php
$i++;
}
?>

		<div class="row"><tr><td width="4%"></td><td width="9%"></td><td width="9%"></td><td width="9%"></td><td width="4%"></td><td width="12%"></td>
		<td width="9%"></td><td width="12%"></td><td width="4%"></td><td width="9%"></td><td width="9%"></td><td width="9%"></td></tr></div>
		<div class="row"><tr><td width="4%"></td><td width="9%"></td><td width="9%"></td><td width="9%"></td><td width="4%"></td><td width="12%"></td>
		<td width="9%"></td><td width="12%"></td><td width="4%"></td><td width="9%"></td><td width="9%"></td><td width="9%"></td></tr></div>
		<div class="row"><tr><td width="4%"></td><td width="9%"></td><td width="9%"></td><td width="9%"></td><td width="4%"></td><td width="12%"></td>
		<td width="9%"></td><td width="12%"></td><td width="4%"></td><td width="9%"></td><td width="9%"></td><td width="9%"></td></tr></div>
		<div class="row"><tr><td width="4%"></td><td width="9%"></td><td width="9%"></td><td width="9%"></td><td width="4%"></td><td width="12%"></td>
		<td width="9%"></td><td width="12%"></td><td width="4%"></td><td width="9%"></td><td width="9%"></td><td width="9%"></td></tr></div>
		<div class="row"><tr><td width="4%"></td><td width="9%"></td><td width="9%"></td><td width="9%"></td><td width="4%"></td><td width="12%"></td>
		<td width="9%"></td><td width="12%"></td><td width="4%"></td><td width="9%"></td><td width="9%"></td><td width="9%"></td></tr></div>

</tbody>
</table>

</form>

</div>



  
</div>
</body>

</html>	
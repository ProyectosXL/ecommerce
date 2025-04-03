<!doctype HTML>
<html>
<head>
<title>Pedidos Ecommerce</title>
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
$hoy = date("Y-m-d");
if(!isset($_GET['cliente'])){$cliente = '%';}else{	$cliente = '%'.$_GET['cliente'].'%';}
if(!isset($_GET['tienda'])){$tienda = '%';}else{	$tienda = '%'.$_GET['tienda'].'%';}
if(!isset($_GET['pedido'])){$pedido = '%';}else{	$pedido = '%'.$_GET['pedido'];}
if(!isset($_GET['estado'])){$estado = '%';}else{	$estado = '%'.$_GET['estado'].'%';}
if(!isset($_GET['factura'])){$factura = 'AND (NRO_COMP IS NULL OR NRO_COMP LIKE \'%\')';}elseif($_GET['factura']==''){$factura = 'AND (NRO_COMP IS NULL OR NRO_COMP LIKE \'%\')';}else{$factura = 'AND (NRO_COMP LIKE \'%'.$_GET['factura'].'\')';}
//if(!isset($_GET['guia'])){	$guia = '%';}else{	$guia = $_GET['guia'];}

?>
<!--
<div class="d-flex justify-content-between mt-1">
<div ><h3>Tracking Pedidos Mayoristas</h3></div>
<div ><button type="button" class="btn btn-primary"onclick="window.location.href='index.php'">Atras</button></div>
</div>  
-->

<div class="row mb-2">

	<div class="col">

	<form method="GET" action="">
		<div class="row mb-1">
		
		<div class="col-sm-2">
		<label class="col-sm col-form-label">Desde:</label>
			<input type="date" class="form-control form-control-sm" name="desde" value="<?php if(!isset($_GET['desde'])){	echo $hoy;}else{ echo $_GET['desde'] ;}?>">
		</div>
	  
		<div class="col-sm-2">
		<label class="col-sm col-form-label">Hasta:</label>
			<input type="date" class="form-control form-control-sm" name="hasta" value="<?php if(!isset($_GET['hasta'])){	echo $hoy;}else{ echo $_GET['hasta'] ;}?>">
		</div>
		
		<div class="col-sm-2">
		<label class="col-sm col-form-label">Cliente:</label>
			<input type="text" class="form-control form-control-sm" name="cliente" value="<?php if(!isset($_GET['cliente'])){	echo '';}else{ echo $_GET['cliente'] ;}?>" placeholder="">
		</div>
		
		<div class="col-sm-1">
		<label class="col-sm col-form-label">Tienda:</label>
			<select class="form-control form-control-sm" name="tienda">
			<option selected></option>
			<option value="VTEX">VTEX</option>
			<option value="ML">MERCADO LIBRE</option>
			<option value="DAF">DAFITI</option>
			</select >
		</div>
		
		<div class="col-sm-2">
		<label class="col-sm col-form-label">Pedido:</label>
			<input type="text" class="form-control form-control-sm" name="pedido" placeholder="" autofocus>
		</div>
		
		<div class="col-sm-2">
		<label class="col-sm col-form-label">Factura:</label>
			<input type="text" class="form-control form-control-sm" name="factura" placeholder="">
		</div>
		
		<div class="col-sm-1 pt-4">
			<input type="submit" class="btn btn-primary" value="Buscar">
		</div>
	  
	  </div>
	  
	  
	  
	</form>  
	
	</div>


	<div class="row mr-1">

		<div class="col mt-1">
		
			<div>
				<button class="btn btn-info btn-sm">Rotulos 1</button>
				<button class="btn btn-info btn-sm">M L</button>
			</div>
			
			<div class="mt-1">		
				<button class="btn btn-info btn-sm">Rotulos 2</button>
				<button class="btn btn-info btn-sm">DAF</button>
			</div>	
			
		</div>

	</div>

</div>

<?php
if(isset($_GET['desde'])){
$desde =  $_GET['desde'];
$hasta =  $_GET['hasta'];
//echo $desde.' '.$hasta.'- guia:'.$guia.' -cliente:'.$cliente.' -estado:'.$estado;

$dsn = '1 - CENTRAL';
$user = 'sa';
$pass = 'Axoft1988';

$sql = "
SET DATEFORMAT YMD

SELECT * FROM SOF_AUDITORIA
WHERE FECHA_PEDIDO >= GETDATE()-30
AND FECHA_PEDIDO BETWEEN '$desde' AND '$hasta'
AND ORIGEN LIKE '$tienda'
AND NRO_PEDIDO LIKE '$pedido'
AND RAZON_SOCIAL LIKE '$cliente'
$factura
ORDER BY 2 desc, 7 DESC, NRO_PEDIDO, COD_ARTICULO
";

$cid=odbc_connect($dsn, $user, $pass);

if (!$cid){
	exit("<strong>Ha ocurrido un error tratando de conectarse con el origen de datos.</strong>");
}

ini_set('max_execution_time', 300);
$result=odbc_exec($cid,$sql)or die(exit("Error en odbc_exec"));

//echo $factura;

?>

<div style="width:100%; padding-bottom:5%; margin-bottom:5%" >

<table class="table table-striped table-fh table-12c">
<thead>
	<tr>
		<th width="4%" class="h6">TIENDA</th>
		<th width="9%" class="h6">NRO<BR>ORDEN</th>
		<th width="9%" class="h6">FECHA<BR>PEDIDO</th>
		<th width="9%" class="h6">PEDIDO</th>
		<th width="4%" class="h6" style="width: 20px">SELEC</th>
		<th width="12%" class="h6">NOMBRE</th>
		<th width="9%" class="h6">COD<BR>ARTICU</th>
		<th width="12%" class="h6">DESC<BR>ARTICULO</th>
		<th width="4%" class="h6">CANT</th>
		<th width="9%" class="h6">IMPORTE</th>
		<th width="9%" class="h6">MEDIO<BR>PAGO</th>
		<th width="9%" class="h6">NRO<BR>FACT</th>
	</tr>
</thead>
<tbody>
<?php
while($v=odbc_fetch_array($result)){
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
			<td width="4%"> <input type="checkbox" value="<?php echo $v['NRO_PEDIDO']?>"> </td>
			<td width="12%"><?php echo $v['RAZON_SOCIAL']?></td>
			<td width="9%"><?php echo $v['COD_ARTICULO'];?></td>
			<td width="12%"><?php echo $v['DESCRIPCION'];?></td>
			<td width="4%"><?php echo $v['CANTIDAD_A_FACTURAR'];?></td>
			<td width="9%"><?php echo '$ '.number_format($v['TOTAL_PEDIDO'] , 0, '', '.')?></td>
			<td width="9%"><?php echo $v['OBSERV_AUDITORIA'];?></td>
			<td width="9%"><?php echo $v['NRO_COMP']?></td>
			
		</tr>
		</div>
<?php
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


</div>
<?php
}
?>


  
</div>
</body>

</html>	
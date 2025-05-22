<?php

require_once 'Class/Conexion.php';
require_once 'Class/Pedido.php';
require_once 'Controlador/envio_remitos_once.php';
require_once 'Controlador/nuevo_ml.php';
require_once 'Controlador/nc_pend.php';
$pedidos = new Pedido();


remitos_buscar_once();
new_ml();

if(!isset($_GET['desde'])){
	nc_pendientes();
}

$hoy = date("Y-m-d");
$tienda = (!isset($_GET['tienda'])) ? '%' : '%'.$_GET['tienda'].'%';
$warehouse = (!isset($_GET['warehouse'])) ? '%' : '%'.$_GET['warehouse'].'%';
$desde = (!isset($_GET['desde'])) ? $hoy : $_GET['desde'];
$hasta = (!isset($_GET['hasta'])) ? $hoy : $_GET['hasta'];
$estado = (isset($_GET['estado'])) ? $_GET['estado'] : null;
$orden = (!isset($_GET['orden'])) ? '%' : '%'.$_GET['orden'].'%';
$todosLosWarehouse = $pedidos->traerWarehouse();
							


?>

<!doctype HTML>

<html lang="en">
<head>
<title>XL Extralarge - Inicio</title>	
<meta charset="UTF-8"></meta>
<link rel="shortcut icon" href="assets/icono.ico" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<?php 
	require_once $_SERVER['DOCUMENT_ROOT']. '/ecommerce/assets/css/css.php';
?>

<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">

</head>

<body>
<div class="container-fluid">

<div class="alert alert-primary" role="alert" id="menu">
	<div class="form-inline">
		<h3 class="mt-2"><i class="bi bi-handbag"></i> Estado Pedidos Ecommerce</h3>
		<label style="margin-left: 45%">Ordenes:</label>
		<input type="text" style="text-align:center; width:10rem; font-size: 16px;" class="form-control form-control-sm ml-1" id="cantidad" readonly disabled>
		<label class="ml-2">Artículos:</label>
		<input type="text" style="text-align:center; width:10rem; font-size: 16px;" class="form-control form-control-sm ml-1" id="cantidadArticulos" readonly disabled>
	</div>
  
	<div class="row"  id="renderr" style="margin-left:10px">

		<div class="mt-2">

			<form class="form-inline" method="GET" action="">		

				<div style="display: flex; flex-direction: column;">
					<label style="align-self: flex-start;">Desde:</label>
					<input type="date" class="form-control form-control-sm" name="desde" value="<?=$desde?>">
				</div>
				<div style="display: flex; flex-direction: column; margin-left:0.5rem">
					<label style="align-self: flex-start;">Hasta:</label>
					<input type="date" class="form-control form-control-sm" name="hasta" value="<?=$hasta?>">
				</div>			
				<div style="display: flex; flex-direction: column; margin-left:0.5rem">
					<label style="align-self: flex-start;">Tienda:</label>
					<select class="form-control form-control-sm" name="tienda">
						<option selected></option>
						<option value="ICBC">ICBC</option>
						<option value="VTEX">VTEX</option>
						<option value="ML">MERCADO LIBRE</option>
					</select>
				</div>

				<div style="display: flex; flex-direction: column; margin-left:0.5rem">
					<label style="align-self: flex-start;">Estado:</label>
					<select class="form-control form-control-sm" name="estado">
						<option selected></option>
						<option value="CANCELADO" <?= (isset($_GET['estado']) && $_GET['estado'] == 'CANCELADO') ? 'selected' : '' ?>>CANCELADO</option>
						<option value="PREPARADO" <?= (isset($_GET['estado']) && $_GET['estado'] == 'PREPARADO') ? 'selected' : '' ?>>PREPARADO</option>
						<option value="SIN_CONTROLAR" <?= (isset($_GET['estado']) && $_GET['estado'] == 'SIN_CONTROLAR') ? 'selected' : '' ?>>SIN CONTROLAR</option>
						<option value="FALTANTE" <?= (isset($_GET['estado']) && $_GET['estado'] == 'FALTANTE') ? 'selected' : '' ?>>FALTANTE</option>
						<option value="FACTURADO" <?= (isset($_GET['estado']) && $_GET['estado'] == 'FACTURADO') ? 'selected' : '' ?>>FACTURADO</option>
						<option value="DESPACHADO" <?= (isset($_GET['estado']) && $_GET['estado'] == 'DESPACHADO') ? 'selected' : '' ?>>DESPACHADO</option>
						<option value="ENTREGADO" <?= (isset($_GET['estado']) && $_GET['estado'] == 'ENTREGADO') ? 'selected' : '' ?>>ENTREGADO</option>
						<option value="SIN_DESPACHAR" <?= (isset($_GET['estado']) && $_GET['estado'] == 'SIN_DESPACHAR') ? 'selected' : '' ?>>SIN DESPACHAR</option>
					</select>
				</div>

				<div style="display: flex; flex-direction: column; margin-left:0.5rem">
					<label style="align-self: flex-start;">Origen:</label>
					<select class="form-control form-control-sm" name="warehouse">
							<option selected></option>
									<?php
									
								foreach($todosLosWarehouse as $warehouse => $key){
								
								?>
								
							<option value="<?= $key[0]->WAREHOUSE ?>"><?= $key[0]->WAREHOUSE ?></option>
								<?php   
								}
								?>
					</select>
				</div>

				<div style="display: flex; flex-direction: column; margin-left:0.5rem">
					<label style="align-self: flex-start;">Orden:</label>
					<input class="form-control form-control-sm" type="text" placeholder="Número de orden.." name="orden">
				</div>
				
				<div class="ml-2">
					<button type="submit" class="btn btn-primary btn-buscar mt-4">Buscar <i class="bi bi-search"></i></button>
				</div>
				<!-- spinner -->
				<div id="boxLoading"></div> 

				<?php 
				if(isset($_GET['desde'])){			
				?>

				<label class="ml-2 mt-4">Busqueda:</label>
					<input type="text" class="form-control form-control-sm ml-1 mt-4" onkeyup="busquedaRapida()" onkeypress = "return pulsar(event)" id="textBox" name="factura" placeholder="Sobre cualquier campo.." autofocus>
				<?php 
				}
				?>

				<?php
					if(isset($_GET['desde'])){

					$arrayPedidos = $pedidos->traerPedidos($desde, $hasta, $tienda, $warehouse, $estado, $orden);

					$bandera = 0;
					$pedido_viejo = '';
					$pedido_nuevo = '';

				?>

			</form>  
		
		</div>

		<div class="mt-4" style="margin-left: 1.5rem;">
			<button onclick="filterPendientes()" id="buttonPendientes">Pendientes</button>
			</svg>
		</div>

		<div class="ml-1 mt-4">
			<button onclick="filterCancelados()" id="buttonCancelados">Sin NC</button>
			</svg>
		</div>

		<div class="ml-1 mt-4">
			<button onclick="filterIncompletos()" id="buttonIncompletos">Incompletos</button>
			</svg>
		</div>
		<div class="ml-1 mt-4">
			<button onclick="exportar()" id="buttonExportar" style="background-color:#28a745" >Exportar <i class="bi bi-filetype-xls"></i></button>
			</svg>
		</div>

	</div>


</div>

<?php 
require_once $_SERVER['DOCUMENT_ROOT']. '/ecommerce/assets/js/js.php';
?>

<div style="width:100%;" >


<!-- <script src="https://cdn.jsdelivr.net/npm/table2excel@1.0.4/dist/table2excel.min.js"></script> -->


	<div >

		<table class="table table-hover " id="id_tabla">
			<thead id="tablaPedidosH">
				<tr>
					<th style="width: 2%;" class="headerTitle">TIENDA</th>
					<th style="width: 9%;" class="headerTitle">NRO<BR>ORDEN</th>
					<th style="width: 6%;" class="headerTitle">FECHA<BR>PEDIDO</th>
					<th style="width: 6%;" class="headerTitle">HORA<BR>PEDIDO</th>
					<th style="width: 5%;" class="headerTitle">PEDIDO</th>
					<th style="width: 12%;" class="headerTitle">NOMBRE</th>
					<th style="width: 8%;" class="headerTitle">COD<BR>ARTICULO</th>
					<th style="width: 8%;" class="headerTitle">DESC<BR>ARTICULO</th>
					<th style="width: 4%; text-align: left; padding-left: 0px" class="headerTitle">CANT</th>
					<th style="width: 7%;" class="headerTitle">IMPORTE</th>
					<th style="width: 5.5%;" class="headerTitle">NRO<BR>FACT</th>
					<th style="width: 5%;" class="headerTitle">DEPOSITO</th>
					<th style="width: 5%;" class="headerTitle">METODO<BR>ENVIO</th>
					<th style="width: 5%;" class="headerTitle">TIENDA</th>
					<th style="width: 1%; color: white;" class="headerTitle noExl"><i class="fa fa-cart-plus" data-toggle="tooltip" data-placement="top" title="Preparación" style="color: #FFFFFF; font-size: 18px; padding-top: 0.4rem;"></i></th>
					<th style="width: 1%; color: white;" class="headerTitle noExl"><i class="bi bi-file-earmark-text-fill" data-toggle="tooltip" data-placement="top" title="Facturación" style="color: #FFFFFF; font-size: 18px; padding-top: 0.4rem;"></i></th>
					<th style="width: 1%; color: white;" class="headerTitle noExl"><i class="bi bi-clipboard2-check-fill" data-toggle="tooltip" data-placement="top" title="Control" style="color: #FFFFFF; font-size: 18px; padding-top: 0.4rem;"></i></th>
					<th style="width: 1%; color: white;" class="headerTitle noExl"><i class="fas fa-truck" data-toggle="tooltip" data-placement="top" title="Despacho" style="color: #FFFFFF; font-size: 18px; padding-top: 0.4rem;"></i></th>
					<th style="width: 1%; color: white;" class="headerTitle noExl"><i class="bi bi-box-seam-fill" data-toggle="tooltip" data-placement="top" title="Entrega" style="color: #FFFFFF; font-size: 18px; padding-top: 0.4rem;"></i></th>
				</tr>
			</thead>
			<tbody id="table">
				<?php
				$id = 0;
				foreach($arrayPedidos as $key => $value){


					echo '<tr id="tr" style="';
					if ($value[0]->NRO_COMP == '' && $value[0]->CANCELADO == 0 ){
						echo 'font-weight:bold; color:#FE2E2E;';
					}else{
						echo '';
					}
					if($bandera == 0){
						$pedido_viejo = $value[0]->NRO_PEDIDO;
						$bandera = 1;
					}elseif($value[0]->NRO_PEDIDO==$pedido_viejo){
						echo '';
					}else{
						echo ';border-top: 1px solid black;';
						$pedido_viejo = $value[0]->NRO_PEDIDO;
					}

					echo '">'

					// $date = date_create($value[0]->FECHA_PEDIDO);
					// $date = date_format($date,"Y/m/d");
				?>
					<td ><?= $value[0]->ORIGEN?></td>
					<td> 
						<?php if($value[0]->ORIGEN=='VTEX' ){
						?>
						<a href="https://xlshop.myvtex.com/admin/orders/<?= $value[0]->NRO_ORDEN_ECOMMERCE; ?>" target="_blank">
							<?= $value[0]->NRO_ORDEN_ECOMMERCE?>
								</a>
						<?php
						}else{
								echo $value[0]->NRO_ORDEN_ECOMMERCE;
						} ?>
					</td>
					<td ><?= $value[0]->FECHA_PEDIDO->format("Y-m-d"); ?></td>
					<td ><?= $value[0]->HORA?></td>
					<td ><?= $value[0]->NRO_PEDIDO?></td>
					<td   data-toggle="tooltip" data-placement="right" title="<?= $value[0]->LUGAR_ENTREGA?>" name="orden_<?php ?>"><small><?= $value[0]->RAZON_SOCIAL?></small></td>
					<td ><?= $value[0]->COD_ARTICULO;?></td>
					<td ><small><?= $value[0]->DESCRIPCION;?></small></td>
					<td  style="text-align: left;"><?= $value[0]->CANTIDAD_A_FACTURAR;?></td>
					<td  ><?= '$ '.number_format($value[0]->IMPORTE_PAGO , 0, '', '.')?></td>
					<td  style="text-align: center;"><small><?= $value[0]->NRO_COMP?></small></td>
					<td  style="text-align: center;"><small><?= $value[0]->WAREHOUSE?></small></td>
					<td  style="text-align: center;"><small><?= $value[0]->METODO_ENVIO?></small></td>
					<td  style="text-align: center;"><small><?= $value[0]->DESC_SUCURSAL?></small></td>

					<td  id="incompleto" class="noExl">
						<?php if(isset($value[0]->FALTANTE) && $value[0]->FALTANTE== 1){ ?>
							<i title="Pedido incompleto" data-toggle="tooltip" data-placement="left" class="bi bi-cart-dash-fill incompleto" style="color: orange; font-size: 20px;"></i>	
							<?php }else if(isset($value[0]->FALTANTE) && $value[0]->FALTANTE== 0){?>
								<i class="fas fa-square" style="color: white; font-size: 20px;">
								<?php } ?>
					</td>
					
					<td id="cancelado" class="noExl">

						<?php if($value[0]->CANCELADO == 1 && $value[0]->FACTURADO == 1 && !isset($value[0]->NCR)){?>
							<i class="bi bi-clipboard-x-fill cancelado" data-toggle="tooltip" data-placement="left" title="Pedido cancelado sin NC" style="color: #6610f2; font-size: 20px; padding: 0;"></i>
							<?php
						}else if(isset($value[0]->NCR)){?>
							<i class="bi bi-clipboard-check-fill" data-toggle="tooltip" data-placement="left" title="Pedido cancelado <?php if(isset($value[0]->NCR)){echo 'NCR '.$value[0]->NCR;}?>" style="color: #17a2b8; font-size: 20px; padding: 0;"></i>
							<?php
						}else if($value[0]->CANCELADO == 1 ){
						?>
							<i class="bi bi-cart-x-fill" data-toggle="tooltip" data-placement="left" title="Cancelado" style="color: red; font-size: 20px; padding: 0;"></i>
						<?php
						}else if($value[0]->FACTURADO == 1){ ?>
						<i class="bi bi-file-earmark-text-fill" data-toggle="tooltip" data-placement="left"  title="Facturado" style="color: #6c757d; font-size: 20px;"></i>
							<?php }else if($value[0]->FACTURADO == 0){?>
								<i class="fas fa-square pendiente" style="color: white; font-size: 20px;">
							<?php } ?>	

					</td>

					<td class="noExl" >
						<?php if($value[0]->CONTROLADO== 1){ ?>
							<i class="bi bi-clipboard2-check-fill"  data-toggle="tooltip" data-placement="left" title="Controlado <?= $value[0]->FECHA_CONTROLADO->format("Y-m-d")?>" style="color: green; font-size: 20px;"></i>
								<?php }else if($value[0]->CONTROLADO== 0){?>
										<i class="fas fa-square" style="color: white; font-size: 20px;">
									<?php } ?>
					</td>

					<td class="noExl">
						<?php if($value[0]->DESPACHADO== 1){ ?>
							<i class="fas fa-truck"  data-toggle="tooltip" data-placement="left" title="Despachado <?= $value[0]->FECHA_DESPACHO->format("Y-m-d")?>" style="color: #17a2b8; font-size: 18px; padding-top: 0.4rem;"></i>
								<?php }else if($value[0]->DESPACHADO== 0){?>
									<i class="fas fa-square" style="color: white; font-size: 20px;">
									<?php } ?>
					</td>

					<td class="noExl">
						<?php if($value[0]->ENTREGADO == 1){ ?>
							<i class="bi bi-box-seam-fill" data-toggle="tooltip" data-placement="left" title="Entregado <?= $value[0]->FECHA_ENTREGADO->format("Y-m-d")?>" style="color: #007bff; font-size: 18px; padding-top: 0.4rem;"></i>
								<?php }else if($value[0]->ENTREGADO == 0){?>
								<i class="fas fa-square" style="color: white; font-size: 20px;">
								<?php } ?>
					</td>
				

				</tr>
			<?php
			$id++;
			}
			?>

			</tbody>

		</table>
		
	</div>
</div>
<?php
}
?>

<script>
	
	const contar = () => {
		let trFiltrados = $('#id_tabla tbody tr:visible');
		let pedidosUnicos = new Set();
		let totalArticulos = 0;

		trFiltrados.each(function() {
			let numeroPedido = $(this).find('td').eq(4).text().trim();
			let codArticulo = $(this).find('td').eq(6).text().trim();
			let cantidad = parseFloat($(this).find('td').eq(8).text().trim()) || 0;
			
			pedidosUnicos.add(numeroPedido);
			
			if (codArticulo !== '***COSTO ENVIO') {
				totalArticulos += cantidad;
			}
		});

		document.getElementById('cantidad').value = pedidosUnicos.size.toLocaleString();
		document.getElementById('cantidadArticulos').value = totalArticulos.toLocaleString();
	}

		$(document).ready(function () {
			contar();
		});

</script>



<!-- <script type="text/javascript" src="Controlador/main.js"></script> -->



<script src="assets/bootstrap/popper.min.js" ></script>
<script src="assets/bootstrap/bootstrap.min.js" ></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

</body>

</html>	
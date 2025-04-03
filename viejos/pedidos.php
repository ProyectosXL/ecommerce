<?php 
session_start(); 

if(!isset($_SESSION['username'])){

	header("Location:../login.php");

}else{
	$_SESSION['cargaPedido'] = 1;
	if($_SESSION['nuevoPedido']==0 && $_SESSION['cargaPedido']==1){
		
		?>
		<!doctype html>
		<html>
		<head>
		
		<title>Carga de Pedidos</title>
		<meta charset="UTF-8"></meta>
		<link rel="shortcut icon" href="../../css/icono.jpg" />
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
		
		<style>
		td{
			font-size:11px;
		}
		#cant{
			font-size:14px;
		}
		#stock{
			font-size:14px;
		}
		
		</style>
			
		</head>
		<body>

		<?php

		$dsn = "1 - CENTRAL";
		$user = "sa";
		$pass = "Axoft1988";
		$suc = $_SESSION['numsuc'];
		
		
		switch($_GET['tipo']){
		case 1: $_SESSION['tipo_pedido'] = 'GENERAL'; break;
		case 2: $_SESSION['tipo_pedido'] = 'ACCESORIOS'; break;
		case 3: $_SESSION['tipo_pedido'] = 'OUTLET'; break;
		}
		
		$_SESSION['depo'] = '01';
		
		$codClient = $_SESSION['username'];
		
		include 'tipoPedido.php';
		
		switch($_GET['tipo']){
		case 1: $sql = $sql1; break;
		case 2: $sql = $sql2; break;
		case 3: $sql = $sql3; break;
		}
		
		$cid = odbc_connect($dsn, $user, $pass);
		
		ini_set('max_execution_time', 300);
		$result=odbc_exec($cid,$sql)or die(exit("Error en odbc_exec"));

		?>

		
		

		<form method="POST" action="cargarPedidoNuevo.php" onkeypress = "return pulsar(event)">

		<div style="width:100%; padding-bottom:5%; margin-bottom:5%" >
  
		  <table class="table table-striped table-fh table-12c" id="id_tabla">
			
		<thead>
			<tr style="font-size:smaller">
				<th style="width: 8%">FOTO</th>
				<th style="width: 10%">CODIGO</th>
				<th style="width: 1%"></th >
				<th style="width: 25%">DESCRIPCION</th>
				<th style="width: 12%">RUBRO</th>
				<th style="width: 1%"></th >
				<th style="width: 1%"></th >
				<th style="width: 10%">STOCK<br>CENTRAL</th>
				<?php if($_SESSION['dsn']!='SIN'){echo "<th style='width: 10%'>STOCK<br>LOCAL</th>"; }?>
				<?php if($_SESSION['dsn']!='SIN'){echo "<th style='width: 10%'>VENTAS<br>30 DIAS</th>"; }?>
				<?php if($_GET['tipo']!=3){?><th style="width: 5%">DIST</th><?php } ?>
				<th style="width: 5%">PEDIDO</th>
				<th style="width: 3%"><input type="submit" value="Enviar" class="btn btn-primary btn-sm"></th>
			</tr>
		</thead>
		
		<tbody>
		<?php


		while($v=odbc_fetch_array($result)){

			?>
			
			
			<?php 
			if(substr($v['DESCRIPCIO'], -11)=='-- SALE! --'){
				?>
				<tr style="font-weight:bold;color:#FE2E2E" >
				<?php
			}else{
				?>
				<tr >
				<?php
			}
			?>
			
			<td style="width: 8%"> 
			<a target="_blank" data-toggle="modal" data-target="#exampleModal<?php echo substr($v['COD_ARTICU'], 0, 13) ;?>" href="../imagenes/<?php echo substr($v['COD_ARTICU'], 0, 13) ;?>.jpg"><img src="../imagenes/<?php echo substr($v['COD_ARTICU'], 0, 13) ;?>.jpg" alt="" height="50" width="50"></a>
			</td>
			
			<div class="modal fade" id="exampleModal<?php echo substr($v['COD_ARTICU'], 0, 13) ;?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				  <div class="modal-body" align="center">
					<img src="../imagenes/<?php echo substr($v['COD_ARTICU'], 0, 13) ;?>.jpg" alt="<?php echo substr($v['COD_ARTICU'], 0, 13) ;?>.jpg - imagen no encontrada" height="400" width="400">
				  </div>
				  <div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
				  </div>
				</div>
			  </div>
			</div>
			
			<td style="width: 10%"> <?php echo $v['COD_ARTICU'] ;?></td>
			
			<td style="width: 1%"><input name="codArt[]" value="<?php echo $v['COD_ARTICU'] ;?>"  hidden></td>

			<td style="width: 25%"><?php echo $v['DESCRIPCIO'] ;?></td>
			
			<td style="width: 12%"><?php echo $v['RUBRO'] ;?></td>

			<td style="width: 1%"><input name="rubro[]" id="cant" value="<?php echo $v['RUBRO'] ;?>"  hidden></td>
			
			<td style="width: 1%"><input name="stock[]" id="cant" value="<?php echo $v['CANT_STOCK'] ;?>"  hidden></td>
			
			<td style="width: 10%" id="stock"><?php echo (int)($v['CANT_STOCK']) ;?></td>
			
			
			<?php 
			if($_SESSION['dsn']!='SIN'){?>
			<td style="width: 10%" id="cant" ><?php echo (int)($v['STOCK_LOCAL']) ;?></td>
			<?php ;}?>
			
			<?php 
			if($_SESSION['dsn']!='SIN'){?>
			<td style="width: 10%" id="cant"><?php echo (int)($v['VENDIDO_LOCAL']) ;?></td>
			<?php ;}?>
			
			<?php if($_GET['tipo']!=3){?> <td style="width: 5%" id="cant"><?php echo (int)($v['DISTRI']) ;?></td> <?php } ?>

			<td style="width: 5%" id="cant"><input type="text" name="cantPed[]" value="0" size="4" tabindex="1" id="articulo" class="form-control form-control-sm" onChange="total();verifica()"></td>

			<td style="width: 3%" id="cant"></td>
			
			</tr>

			<?php

		}

		?>
		
			<!--FILA DE MAS QUE SE PONE PARA QUE EL TOTAL NO PISE AL ULTIMO REGISTRO-->
			<tr style="font-size:smaller" ><td style="width: 8%"></td></td></tr>
		
		</tbody>
		
		

		</table>
		</div>
		
		
		
		</form>

		
		<div class="mt-2 text-center fixed fixed-bottom bg-white" style="height: 30px!important; background-color: white;" >
			<a> <strong>Total de articulos:</strong> </a> <input name="total_todo" size="3" id="total" value="0" type="text">
		</div>
		
				
		</body>
		
		<script>			
			function pulsar(e) {
			tecla = (document.all) ? e.keyCode : e.which;
			return (tecla != 13);
			}
			
				
			function total() {
				var suma = 0;
				var x = document.querySelectorAll("#id_tabla input[name='cantPed[]']"); //tomo todos los input con name='cantProd[]'

				var i;
				for (i = 0; i < x.length; i++) {
					suma += parseInt(0+x[i].value); //acá hago 0+x[i].value para evitar problemas cuando el input está vacío, si no tira NaN
				}

				// ni idea dónde lo vas a mostrar ese dato, yo puse un input, pero puede ser cualquier otro elemento
				document.getElementById('total').value = suma;
			};
			
			
			function verifica() {
				
				var x = document.querySelectorAll("#id_tabla input[name='cantPed[]']");
				var y = document.querySelectorAll("#id_tabla #id_stock");

				var i;
				for (i = 0; i < x.length; i++) {
					if( parseInt(x[i].value) > parseInt(y[i].value) ){
						alert("El valor ingresado es incorrecto");
					}
				}
		
			};
			
			
			$('#myModal').on('shown.bs.modal', function () {
			  $('#myInput').trigger('focus')
			})
		
		</script>
		</html>

		<?php
	}

	else{
		
		header("Location:login.php");
	}
	
}
?>


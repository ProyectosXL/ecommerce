<?php

//var_dump($_POST);

for($x=0; $x<count($_POST['nro_pedido']); $x++){
	
	
	
	
		$pedido = $_POST['nro_pedido'][$x];
		//$selec = $_POST['selec'][$x];
		
		echo '*'.$pedido.'*';
	
	
	
}



?>
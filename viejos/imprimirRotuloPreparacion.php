<?php

$dsn = '1 - CENTRAL';
$user = 'sa';
$pass = 'Axoft1988';

$sqlCero = "
UPDATE SOF_AUDITORIA SET NRO_TRACKING = 0 WHERE NRO_TRACKING = 1
";

$cid=odbc_connect($dsn, $user, $pass);

odbc_exec($cid, $sqlCero);




for($x=0; $x<count($_POST['nro_pedido']); $x++){
	
	
	
	if (isset($_POST['selec'][$x])){
		$pedido = $_POST['nro_pedido'][$x];
		$selec = $_POST['selec'][$x];
		
		$sqlUno = "UPDATE SOF_AUDITORIA SET NRO_TRACKING = 1 WHERE NRO_PEDIDO = '$selec'";
		odbc_exec($cid, $sqlUno);
		//echo '-'.$selec.'<br>';
	}
	
	
	
	
}

header("location:index.php");
//header("location:http://servidor/ReportServer/Pages/ReportViewer.aspx?%2freportes_preparacion&rs:Command=Render");


//var_dump('<pre>', $_POST, '</pre>');

/*
foreach($_POST as $key => $postValue) {
   if (false !== strrpos($key, 'selec-')) {
    echo (substr($key, 7)) . '<br>';
  }
}
*/

?>


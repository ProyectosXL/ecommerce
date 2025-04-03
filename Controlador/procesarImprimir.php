<?php
require_once 'Class/Conexion.php';
$cid = new Conexion();
$cid_central = $cid->conectarSql('central');

$sqlCero = "
UPDATE SOF_AUDITORIA SET NRO_TRACKING = 0, USUARIO_ETIQUETA = '' WHERE NRO_TRACKING = 1 
";

// $cid=odbc_connect($dsn, $user, $pass);

sqlsrv_query($cid_central, $sqlCero);


var_dump($_POST);

for($x=0; $x<count($_POST['pedidos']); $x++){
		
    $pedido = $_POST['pedidos'][$x];
    
    echo 'Pedido: '.$pedido;
	
		$sqlUno = "UPDATE SOF_AUDITORIA SET NRO_TRACKING = 1 WHERE NRO_PEDIDO = '$pedido'";
		sqlsrv_query($cid_central, $sqlUno);
}



?>
<?php

//central
$servidor_central = 'servidor';
$conexion_central = array( "Database"=>"LAKER_SA", "UID"=>"sa", "PWD"=>"Axoft1988");
$cid_central = sqlsrv_connect($servidor_central, $conexion_central);

//locales
$servidor_lakerbis = 'lakerbis';
$conexion_locales = array( "Database"=>"LOCALES_LAKERS", "UID"=>"sa", "PWD"=>"Axoft");
$cid_locales = sqlsrv_connect($servidor_lakerbis, $conexion_locales);

//franquicias
$conexion_franquicias = array( "Database"=>"FRANQUICIAS_LAKERS", "UID"=>"sa", "PWD"=>"Axoft");
$cid_franquicias = sqlsrv_connect($servidor_lakerbis, $conexion_franquicias);

//tangonet buscar
$conexion_tangonet_bis_1 = array( "Database"=>"TangoNet_Bis_1", "UID"=>"sa", "PWD"=>"Axoft");
$cid_tangonet_bis_1 = sqlsrv_connect($servidor_lakerbis, $conexion_tangonet_bis_1);

//execute
// $query = sqlsrv_prepare($cid, $sql);
// $result = sqlsrv_execute($query);
// while($v=sqlsrv_fetch_array($query)){
// 	echo $v['IDFOLDER'].' | '.$v['DESCRIP'].'<br>';
// }
// sqlsrv_close($cid);

?>
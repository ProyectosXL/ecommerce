<?php

function agregar_novedades(){
    
    require_once 'Class/Conexion.php';
    $cid = new Conexion();
    $cid_central = $cid->conectarSql('central');

	if (!$cid_central){exit("<strong>Ha ocurrido un error tratando de conectarse con el origen de datos.</strong>");}
    
    $sqlAgreNov = 
    "
    EXEC SJ_SP_ECOMMERCE_AGREGAR_NOVEDADES
    "
    ;
  
    sqlsrv_query($cid_central,$sqlAgreNov)or die(exit("Error en odbc_exec"));
}
    
?>
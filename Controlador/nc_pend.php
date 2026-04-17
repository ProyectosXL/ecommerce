<?php

function nc_pendientes(){

    require_once 'Class/Conexion.php';
    $cid = new Conexion();
    $cid_central = $cid->conectarSql('central');

	if (!$cid_central){
        return []; // Retornar array vacío en caso de error
    }

    
    $sqlNc = 
    "SELECT * FROM SJ_NC_ECOMMERCE_PEND
    WHERE FECHA >= GETDATE()-60
    ORDER BY FECHA ASC 
    "
    ;
    
    ini_set('max_execution_time', 300);
    $result=sqlsrv_query($cid_central,$sqlNc)or die(exit("Error en odbc_exec"));
    
    $nc_pendientes = [];
    
    while($v=sqlsrv_fetch_array($result)){
        if($v['NUM_NC'] == 'NO'){
            $nc_pendientes[] = [
                'fecha' => $v['FECHA'],
                'promocion' => $v['DESC_PROMOCION_TARJETA'],
                'importe' => $v['NC'],
                'cod_articulo' => $v['COD_ARTICU']
            ];
        }
    }
    
    return $nc_pendientes;
}
    
    ?>
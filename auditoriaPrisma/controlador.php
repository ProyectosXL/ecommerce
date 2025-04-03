<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/ecommerce/Class/Conexion.php';

function auditoria($dias){

    
    $cid = new Conexion();
    $cid_central = $cid->conectarSql('central');

    $sql = "
    EXEC SJ_AUDITORIA_PRISMA $dias
    ";

    $result=sqlsrv_query($cid,$sql)or die(exit("Error en sqlsrv_query"));

    $rows = array();

    while($v=sqlsrv_fetch_array($result)){
        $rows[] = $v;
        }

    return $rows;

}

function auditoriaDetalle($mail){

    $cid = new Conexion();
    $cid_central = $cid->conectarSql('central');

    $sql = "
    EXEC SJ_AUDITORIA_PRISMA_DETALLE '$mail'
    ";

    $result=sqlsrv_query($cid,$sql)or die(exit("Error en sqlsrv_query"));

    $rows = array();

    while($v=sqlsrv_fetch_array($result)){
        $rows[] = $v;
        }

    return $rows;

}


function actualizarAuditoria($mail, $nroPedido, $tarjeta, $autoriza, $noAutoriza){

    $cid = new Conexion();
    $cid_central = $cid->conectarSql('central');

    $sql = "
    EXEC SJ_AUDITORIA_PRISMA_DETALLE_ACTUALIZAR '$mail', '$nroPedido', '$tarjeta', '$autoriza', '$noAutoriza'
    ";

    sqlsrv_query($cid,$sql)or die(exit("Error en sqlsrv_query"));

}
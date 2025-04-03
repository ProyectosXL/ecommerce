<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/ecommerce/Class/Conexion.php';



function auditoria($dias){

    $cid = new Conexion();
    $cid_central = $cid->conectarSql('central');

    $sql = "
    SELECT A.*, ISNULL(B.CANT, 0)CANT FROM SJ_AUDITORIA_PRISMA_HISTORIAL A
    LEFT JOIN (SELECT MAIL, COUNT(*)CANT FROM SJ_AUDITORIA_ECOMMERCE_APROBADOS WHERE AUTORIZA = 'no' AND NO_AUTORIZA = 'no' GROUP BY MAIL) B
    ON A.E_MAIL = B.MAIL
    ORDER BY CANT DESC, E_MAIL
    ";

    $result=sqlsrv_query($cid_central,$sql)or die(exit("Error en sqlsrv_query"));

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

    $result=sqlsrv_query($cid_central, $sql)or die(exit("Error en sqlsrv_query"));

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

    sqlsrv_query($cid_central, $sql)or die(exit("Error en sqlsrv_query"));

}
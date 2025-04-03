<?php


function locales(){
    include '../../../AccesoDatos/dsn.php';

    $sql = "
    SELECT A.NRO_SUCURSAL, A.DESC_SUCURSAL, CASE WHEN B.NRO_SUCURS IS NOT NULL THEN 1 ELSE 0 END HABILITADO
    FROM SUCURSAL A
    LEFT JOIN SJ_LOCALES_ML_ECOMMERCE B
    ON A.NRO_SUCURSAL = B.NRO_SUCURS
    WHERE A.NRO_SUCURSAL < 1000
    AND A.DESC_SUCURSAL NOT LIKE '%SOFIA%'
    AND A.DESC_SUCURSAL NOT LIKE '%FERRETE%'
    AND A.NRO_SUCURSAL NOT IN (1, 99, 100)
    AND A.CA_423_HABILITADO = 1
    ORDER BY A.NRO_SUCURSAL
    ";

    $result=odbc_exec($cid,$sql)or die(exit("Error en odbc_exec"));

    $rows = array();

    while($v=odbc_fetch_array($result)){
        $rows[] = $v;
        }

    return $rows;

}

function comprobarLocales($locales){
    include '../../../AccesoDatos/dsn.php';

    $sql = "
    TRUNCATE TABLE SJ_LOCALES_ML_ECOMMERCE
    ";

    odbc_exec($cid,$sql)or die(exit("Error en odbc_exec"));

    if($locales != null){
        foreach ($locales as $key => $value) {
            $sql2 = 'INSERT INTO SJ_LOCALES_ML_ECOMMERCE (NRO_SUCURS) VALUES ('.$value.')';

            //echo $sql2;
            odbc_exec($cid,$sql2)or die(exit("Error en odbc_exec"));
            
            
        }
    }

}



function articulos($suc){
    include '../../../AccesoDatos/dsn.php';

    $sql = "
    SELECT A.SKU COD_ARTICU, C.DESC_CTA_ARTICULO DESCRIPCIO, CAST(A.CANTIDAD_STOCK  AS INT) CANT_STOCK
    FROM SJ_STOCK_EN_LOCALES A
    INNER JOIN CTA_ARTICULO C
    ON A.SKU = C.COD_ARTICULO
    WHERE A.NRO_SUCURSAL = $suc
    ORDER BY 1
    ";

    $result=odbc_exec($cid,$sql)or die(exit("Error en odbc_exec"));

    $rows = array();

    while($v=odbc_fetch_array($result)){
        $rows[] = $v;
        }

    return $rows;

}
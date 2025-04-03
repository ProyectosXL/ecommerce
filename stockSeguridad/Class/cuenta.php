
<?php

class Cuenta
{

    private function retornarArray($sqlEnviado,$db = 'central')
    {

        require_once $_SERVER['DOCUMENT_ROOT']. '/ecommerce/Class/Conexion.php';


        $cid = new Conexion();
        $cid_central = $cid->conectarSql($db);
        $sql = $sqlEnviado;

        $stmt = sqlsrv_query($cid_central, $sql);

        $rows = array();

        while ($v = sqlsrv_fetch_array($stmt)) {
            $rows[] = $v;
        }

        return $rows;
    }


    public function traerCuentas($db = 'central')
    {

        $sql = "SELECT A.WAREHOUSE_ID, A.DESCRIPCION, B.VTEX_CUENTA, A.UTILIZA_STOCK_SEGURIDAD_POR_CLASIF FROM GC_ECOMMERCE_WAREHOUSE A
                INNER JOIN GC_ECOMMERCE_CUENTA B
                ON A.ID_GC_ECOMMERCE_CUENTA = B.ID_GC_ECOMMERCE_CUENTA
                WHERE UTILIZA_STOCK_SEGURIDAD_POR_CLASIF = 0
                ORDER BY 1
                ";

        $rows = $this->retornarArray($sql,$db);

        $myJSON = json_encode($rows);

        return $myJSON;
    }


    public function traerCuentas2()
    {
        $desc = $_GET['descripcionWarehouse'];
        
        if(strpos($_GET['tipo'], 'Activar') !== false)
        {
            $sql = "SELECT A.WAREHOUSE_ID, A.DESCRIPCION, B.VTEX_CUENTA, A.UTILIZA_STOCK_SEGURIDAD_POR_CLASIF FROM GC_ECOMMERCE_WAREHOUSE A
            INNER JOIN GC_ECOMMERCE_CUENTA B
            ON A.ID_GC_ECOMMERCE_CUENTA = B.ID_GC_ECOMMERCE_CUENTA
            WHERE UTILIZA_STOCK_SEGURIDAD_POR_CLASIF = 0 AND WAREHOUSE_ID = '".$_GET['warehouse']."'
            AND A.DESCRIPCION LIKE '%".$desc."%'";
         
        }else{
            $sql = "SELECT A.WAREHOUSE_ID, A.DESCRIPCION, B.VTEX_CUENTA, A.UTILIZA_STOCK_SEGURIDAD_POR_CLASIF FROM GC_ECOMMERCE_WAREHOUSE A
            INNER JOIN GC_ECOMMERCE_CUENTA B
            ON A.ID_GC_ECOMMERCE_CUENTA = B.ID_GC_ECOMMERCE_CUENTA
            WHERE UTILIZA_STOCK_SEGURIDAD_POR_CLASIF = 1 AND WAREHOUSE_ID = '".$_GET['warehouse']."'
            AND A.DESCRIPCION LIKE '%".$desc."%'";
        }


        $rows = $this->retornarArray($sql);

        $myJSON = json_encode($rows);

        echo $myJSON;
    }

    public function traerCuentas3($db = 'central')
    {

        $sql = "SELECT WAREHOUSE_ID,A.DESCRIPCION,B.VTEX_CUENTA FROM  GC_ECOMMERCE_WAREHOUSE A
        LEFT JOIN GC_ECOMMERCE_CUENTA B
        ON A.ID_GC_ECOMMERCE_CUENTA = B.ID_GC_ECOMMERCE_CUENTA
        ";
     /*    $sql = "SELECT * FROM
                (
                SELECT DESCRIP RUBRO, RUTA, WAREHOUSE_ID, VTEX_CUENTA, DESC_SUCURSAL, STOCK_SEGURIDAD FROM GC_VIEW_ECOMMERCE_CLASIFICADOR_ARTICULOS A
                LEFT JOIN
                (
                    SELECT B.WAREHOUSE_ID, D.VTEX_CUENTA, UPPER(B.DESCRIPCION) DESC_SUCURSAL, PATH_CLASIF, REPLACE(REPLACE(C.PATH_CLASIF, 'Todos(1)/',''), RIGHT(C.PATH_CLASIF,3),'') RUBRO, CAST(A.STOCK_SEGURIDAD AS FLOAT) STOCK_SEGURIDAD
                    FROM GC_ECOMMERCE_STOCK_SEG_CLASIF_ARTICULOS A
                    LEFT JOIN GC_ECOMMERCE_WAREHOUSE B ON A.ID_GC_ECOMMERCE_WAREHOUSE = B.ID_GC_ECOMMERCE_WAREHOUSE
                    LEFT JOIN GC_ECOMMERCE_CLASIFICADOR_ARTICULOS C ON A.ID_GC_ECOMMERCE_CLASIFICADOR_ARTICULOS = C.ID_GC_ECOMMERCE_CLASIFICADOR_ARTICULOS
                    LEFT JOIN GC_ECOMMERCE_CUENTA D ON B.ID_GC_ECOMMERCE_CUENTA = D.ID_GC_ECOMMERCE_CUENTA
                    WHERE C.PATH_CLASIF NOT LIKE '%DISC%' AND C.PATH_CLASIF NOT LIKE '%OUTLET%' AND B.INTEGRA_STOCK = 1 AND B.UTILIZA_STOCK_SEGURIDAD_POR_CLASIF = 1
                ) B
                ON A.RUTA = B.PATH_CLASIF
                WHERE WAREHOUSE_ID = 'xl915' AND A.RUTA IS NOT NULL OR B.PATH_CLASIF IS NULL
                ) A
                WHERE RUBRO NOT LIKE '%OUTLET' AND RUBRO NOT LIKE '[_]%' AND RUBRO != 'Todos' ";  */
        $rows = $this->retornarArray($sql, $db);

        $myJSON = json_encode($rows);

        return $myJSON;
    }
}

$cuenta = new Cuenta;

if (isset($_GET['traerCuenta']) && $_GET['traerCuenta'] == 1) {
    $cuenta->traerCuentas2();
}

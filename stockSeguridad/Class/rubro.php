
<?php

class Rubro
{

    private function retornarArray($sqlEnviado,$db)
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


    public function traerRubros($db = 'central')
    {

        $sql = "SELECT REPLACE(REPLACE(RUTA, 'Todos(1)/',''), RIGHT(RUTA,3),'') RUBRO, RUTA AS PATH_CLASIF FROM GC_VIEW_ECOMMERCE_CLASIFICADOR_ARTICULOS
		        WHERE NIVEL = 2 AND RUTA NOT LIKE '%DISC%' AND RUTA NOT LIKE '%OUTLET%'
        ";

        $rows = $this->retornarArray($sql,$db);
        
        $myJSON = json_encode($rows);

        return $myJSON;

    }

}
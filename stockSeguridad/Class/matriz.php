
<?php

class Matriz
{

    private function retornarArray($sqlEnviado)
    {

        require_once $_SERVER['DOCUMENT_ROOT']. '/ecommerce/Class/Conexion.php';

        $cid = new Conexion();
        $cid_central = $cid->conectarSql('central');
        $sql = $sqlEnviado;

        $stmt = sqlsrv_query($cid_central, $sql);

        $rows = array();

        while ($v = sqlsrv_fetch_array($stmt)) {
            $rows[] = $v;
        }


        return $rows;
    }


    public function traerMatriz()
    {

        $sql = " SELECT * FROM RO_T_STOCK_SEGURIDAD_VTEX ORDER BY DESC_SUCURSAL
        ";

        $rows = $this->retornarArray($sql);
        
        $myJSON = json_encode($rows);

        return $myJSON;

    }

    public function traerMatrizUy()
    {
        require_once $_SERVER['DOCUMENT_ROOT']. '/ecommerce/Class/Conexion.php';
        $cid = new Conexion();
        $cid_uruguay = $cid->conectarSql('uy');
        
        $sql = " SELECT * FROM RO_T_STOCK_SEGURIDAD_VTEX ORDER BY DESC_SUCURSAL
        ";
     

        $stmt = sqlsrv_query($cid_uruguay, $sql);

        $rows = array();

        while ($v = sqlsrv_fetch_array($stmt)) {
            $rows[] = $v;
        }


        return $rows;
       

    }


}
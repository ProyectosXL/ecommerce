<?php

class Faltante{

    public function buscarFaltantes(){
        include __DIR__."/../sql/sql_conexion.php";
        include __DIR__."/../sql/querys.php";

        $query = sqlsrv_prepare($cid_central, $buscar_faltantes);
        $result = sqlsrv_execute($query);
        
        $rows = array();
        
        while($v=sqlsrv_fetch_array($query)){
            $rows[] = $v;
        }
            
        return $rows;
    }

}
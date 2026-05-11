<?php

class Cliente{

    function __construct(){
        require_once __DIR__.'/../../Class/conexion.php';
        $cid = new Conexion();
        $this->cid_central = $cid->conectarSql('central');
        $this->cid_mongo = $cid->conectarMongoDb();

    } 
    
    public function traerRubros(){

        $sql = " 
            SELECT DESCRIP FROM STA11FLD
            WHERE DESCRIP NOT LIKE '[_]%' 
            AND DESCRIP NOT LIKE 'Todos'
            AND DESCRIP NOT LIKE '%OUTLET' 
            AND DESCRIP NOT IN ('ALHAJEROS', 'PACKAGING')
            ORDER BY DESCRIP
        ";

        $result = sqlsrv_query($this->cid_central, $sql);
        
        $array = [];

        while ($v = sqlsrv_fetch_array($result)) {
            $array[] = $v;
        }

        return $array;
    }

    public function traerCategorias($rubros = null){

        $sql = " 
            SELECT * FROM RO_MAESTRO_RUBRO_CATEGORIA2 
        ";
        
        if($rubros != null){
            $sql .= " WHERE RUBRO IN $rubros ";
        }
        $sql .= " ORDER BY 1, 2";
 

        $result = sqlsrv_query($this->cid_central, $sql);
        
        $array = [];

        while ($v = sqlsrv_fetch_array($result)) {
            $array[] = $v;
        }

        return $array;
    }

    public function traerBancos(){

        $sql = " 
            SELECT *   FROM [LAKERBIS].locales_lakers.dbo.CTA_BANCO 
        ";

        $result = sqlsrv_query($this->cid_central, $sql);
        
        $array = [];

        while ($v = sqlsrv_fetch_array($result)) {
            $array[] = $v;
        }

        return $array;
    }

    public function traerClientes($desde, $hasta, $selectBanco, $selectRubro, $selectCategoria, $selectRangoEtario){


        $mongoCollection = $this->cid_mongo->selectCollection("Ventas");


        $filter = [];
        $arrayBanco = []; 
        foreach ($selectBanco as $key => $banco) {
            $partes = explode("?", $banco);
            $arrayBanco[]= $partes[0];
            $arrayBanco[]= $partes[1];
        }
    
        
        if($arrayBanco != null){
            $numericBancoValues = array_map('intval', $arrayBanco);
            $filter["BANCO"] = ['$in' => $numericBancoValues];
        }
   

        
        if($selectRubro != null || $selectCategoria != null){
            $articulosFilter = [];
            if($selectRubro != null){
                $articulosFilter["RUBRO"] = array('$in' => $selectRubro);
            }
            if($selectCategoria != null){
                $articulosFilter["CATEGORIA"] = array('$in' => $selectCategoria);
            }
            $filter["ARTICULOS"] = ['$elemMatch' => $articulosFilter];
        }

        
        if($selectRangoEtario != null){
            $filter["RANGO_ETARIO"] = array('$in' => $selectRangoEtario);
        }
        
        if ($desde != null && $hasta != null) {
            $desdeDate = new MongoDB\BSON\UTCDateTime(strtotime($desde) * 1000);
            $hastaDate = new MongoDB\BSON\UTCDateTime(strtotime($hasta) * 1000);
       
        
            $filter["FECHA"] = ['$gte' => $desdeDate, '$lte' => $hastaDate];

            

        }
  
        $pipeline = [
            ['$match' => $filter],
            ['$unwind' => '$ARTICULOS'],
        ];

        // Filter individual articles after unwinding
        if ($selectRubro != null || $selectCategoria != null) {
            $articuloMatchFilter = [];
            if ($selectRubro != null) {
                $articuloMatchFilter['ARTICULOS.RUBRO'] = ['$in' => $selectRubro];
            }
            if ($selectCategoria != null) {
                $articuloMatchFilter['ARTICULOS.CATEGORIA'] = ['$in' => $selectCategoria];
            }
            $pipeline[] = ['$match' => $articuloMatchFilter];
        }

        $pipeline[] = ['$group' => [
            '_id'         => '$NOMBRE_CLI',
            'NOMBRE_CLI'  => ['$first' => '$NOMBRE_CLI'],
            'DNI'         => ['$first' => '$DNI'],
            'RANGO_ETARIO'=> ['$first' => '$RANGO_ETARIO'],
            'E_MAIL'      => ['$first' => '$E_MAIL'],
            'ARTICULOS'   => ['$push'  => '$ARTICULOS'],
        ]];
        $pipeline[] = ['$sort' => ['NOMBRE_CLI' => 1]];

        $result = $mongoCollection->aggregate($pipeline, [
            'collation'    => ['locale' => 'es'],
            'allowDiskUse' => true,
        ]);

        $newArray = [];

        foreach ($result as $x => $document) {
            $documentArray = $document->getArrayCopy();
            $newArray[$x]['ID'] = (string) $documentArray['_id'];
            foreach ($documentArray as $key => $value) {
                if ($key === '_id') continue;
                $newArray[$x][$key] = $value;
            }
        }

        return ($newArray);

    
    }



}
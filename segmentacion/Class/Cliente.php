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
            SELECT * FROM RO_MAESTRO_RUBRO_CATEGORIA 
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
  
        $options = [
            'projection' => [
                'NOMBRE_CLI' => 1,
                'DNI' => 1,
                'RANGO_ETARIO' => 1,
                'E_MAIL' => 1,
                'ARTICULOS' => 1
            ],
            'sort' => ['NOMBRE_CLI' => 1],
            'collation' => ['locale' => 'es'],
            'group' => [
                '_id' => '$NOMBRE_CLI',
                'nombre' => '$NOMBRE_CLI',
                'dni' => ['$first' => '$DNI'],
                'rango etario' => ['$first' => '$RANGO_ETARIO'],
                'articulos' => ['$push' => '$ARTICULOS']
            ]
        ];
       

        $result = $mongoCollection->find($filter, $options);
        $newArray = [];

        foreach ($result as $x => $document) {

        $documentArray = $document->getArrayCopy();

        // Acceder al campo _id
        $id = (string) $documentArray['_id'];

        $newArray[$x]['ID'] = $id;
        // Acceder a los demás campos
        $keys = array_keys($documentArray);
        
        foreach ($keys as $key) {
            
            
            // Saltar el campo _id
            if ($key === '_id') {
                continue;
            }
                  
       
            if($key != 'ARTICULOS' ){
                
                $newArray[$x][$key] = $documentArray[$key];
                
            }else{
                
                foreach ($documentArray['ARTICULOS'] as $y => &$articulo) {
                
                    if($selectRubro != null || $selectCategoria != null){

                        if(!in_array($articulo['RUBRO'], $selectRubro)){
                            unset($documentArray['ARTICULOS'][$y]);
                            continue;
                        }

                        if(!in_array($articulo['CATEGORIA'], $selectCategoria)){
                            unset($documentArray['ARTICULOS'][$y]);
                            continue;
                        }
                 
           
                    }

                    $newArray[$x][$key] = $documentArray[$key];


                }

            }
            
        }

        }

        return ($newArray);

    
    }



}
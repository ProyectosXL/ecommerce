<?php

class Cliente{

    /**
     * Campo de sucursal en los documentos de la coleccion Ventas.
     * Confirmar el nombre real con segmentacion/diagnosticoVentas.php y ajustar aca:
     * es el unico lugar donde esta escrito el nombre del campo.
     */
    const CAMPO_SUCURSAL = 'NRO_SUCURSAL';

    /** Cache por request del mapa NRO_SUCURSAL => datos de DIRECCIONARIO. */
    private $mapaSucursales = null;

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

    /**
     * Provincias donde hay sucursales. Alimenta el combo de Provincia.
     * DIRECCIONARIO vive en el server de locales: desde el modulo ecommerce el
     * linked server se escribe [LAKERBIS] (no [XL-LAKERBIS], que usan bi/ y comercial/).
     */
    public function traerProvincias(){

        $sql = "
            SELECT DISTINCT PROVINCIA
            FROM [LAKERBIS].LOCALES_LAKERS.DBO.DIRECCIONARIO
            WHERE NRO_SUC_MADRE IS NULL AND PROVINCIA IS NOT NULL AND PROVINCIA <> ''
            ORDER BY PROVINCIA
        ";

        $result = sqlsrv_query($this->cid_central, $sql);

        $array = [];

        if($result === false){
            error_log('traerProvincias: '.print_r(sqlsrv_errors(), true));
            return $array;
        }

        while ($v = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
            $array[] = trim($v['PROVINCIA']);
        }

        return $array;
    }

    /**
     * Localidades con sucursal, opcionalmente acotadas a las provincias elegidas.
     * @param array $provincias
     */
    public function traerLocalidades($provincias = []){

        $sql = "
            SELECT DISTINCT LOCALIDAD, PROVINCIA
            FROM [LAKERBIS].LOCALES_LAKERS.DBO.DIRECCIONARIO
            WHERE NRO_SUC_MADRE IS NULL AND LOCALIDAD IS NOT NULL AND LOCALIDAD <> ''
        ";

        $params = [];

        if(!empty($provincias)){
            $sql .= " AND PROVINCIA IN (".implode(',', array_fill(0, count($provincias), '?')).") ";
            $params = array_values($provincias);
        }

        $sql .= " ORDER BY LOCALIDAD";

        $result = empty($params)
            ? sqlsrv_query($this->cid_central, $sql)
            : sqlsrv_query($this->cid_central, $sql, $params);

        $array = [];

        if($result === false){
            error_log('traerLocalidades: '.print_r(sqlsrv_errors(), true));
            return $array;
        }

        while ($v = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
            $array[] = [
                'LOCALIDAD' => trim($v['LOCALIDAD']),
                'PROVINCIA' => trim($v['PROVINCIA'] ?? ''),
            ];
        }

        return $array;
    }

    /**
     * Sucursales, opcionalmente acotadas por provincia y/o localidad.
     * Alimenta el combo de Tienda y el mapa sucursal => provincia/localidad.
     * @param array $provincias
     * @param array $localidades
     */
    public function traerSucursales($provincias = [], $localidades = []){

        $sql = "
            SELECT NRO_SUCURSAL, DESC_SUCURSAL, LOCALIDAD, PROVINCIA
            FROM [LAKERBIS].LOCALES_LAKERS.DBO.DIRECCIONARIO
            WHERE NRO_SUC_MADRE IS NULL AND NRO_SUCURSAL IS NOT NULL
        ";

        $params = [];

        if(!empty($provincias)){
            $sql .= " AND PROVINCIA IN (".implode(',', array_fill(0, count($provincias), '?')).") ";
            $params = array_merge($params, array_values($provincias));
        }

        if(!empty($localidades)){
            $sql .= " AND LOCALIDAD IN (".implode(',', array_fill(0, count($localidades), '?')).") ";
            $params = array_merge($params, array_values($localidades));
        }

        $sql .= " ORDER BY DESC_SUCURSAL";

        $result = empty($params)
            ? sqlsrv_query($this->cid_central, $sql)
            : sqlsrv_query($this->cid_central, $sql, $params);

        $array = [];

        if($result === false){
            error_log('traerSucursales: '.print_r(sqlsrv_errors(), true));
            return $array;
        }

        while ($v = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
            $array[] = [
                'NRO_SUCURSAL'  => trim((string) $v['NRO_SUCURSAL']),
                'DESC_SUCURSAL' => trim((string) ($v['DESC_SUCURSAL'] ?? '')),
                'LOCALIDAD'     => trim((string) ($v['LOCALIDAD'] ?? '')),
                'PROVINCIA'     => trim((string) ($v['PROVINCIA'] ?? '')),
            ];
        }

        return $array;
    }

    /**
     * Normaliza el nro de sucursal para que matcheen los dos lados:
     * Mongo puede guardarlo como int o string, y DIRECCIONARIO como char
     * con padding o ceros a la izquierda.
     */
    private function normalizarSucursal($nro){

        $nro = trim((string) $nro);

        if($nro === '') return '';

        return is_numeric($nro) ? (string) (int) $nro : strtoupper($nro);
    }

    /**
     * NRO_SUCURSAL normalizado => ['NRO_SUCURSAL','DESC_SUCURSAL','LOCALIDAD','PROVINCIA'].
     *
     * Se resuelve en PHP y no con $lookup de Mongo a proposito: $lookup solo une
     * colecciones dentro de la misma base de MongoDB, y DIRECCIONARIO vive en SQL Server.
     * Son cientos de filas, asi que entra entero en memoria y se carga una sola vez.
     */
    private function mapaSucursales(){

        if($this->mapaSucursales !== null) return $this->mapaSucursales;

        $mapa = [];

        foreach ($this->traerSucursales() as $s) {
            $k = $this->normalizarSucursal($s['NRO_SUCURSAL']);
            if($k === '') continue;
            $mapa[$k] = $s;
        }

        $this->mapaSucursales = $mapa;

        return $mapa;
    }

    public function traerClientes($desde, $hasta, $selectBanco, $selectRubro, $selectCategoria, $selectRangoEtario, $selectTienda = [], $selectProvincia = [], $selectLocalidad = []){


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

        // --- TIENDA / PROVINCIA / LOCALIDAD ---------------------------------
        // El dato geografico vive en SQL Server (DIRECCIONARIO) y el filtro se aplica
        // en Mongo, asi que hay que traducir provincia/localidad => lista de NRO_SUCURSAL.
        //
        // null = sin filtro | array (incluso vacio) = filtrar por esa lista.
        // Usar !== null y no != null a proposito: si el usuario elige una provincia sin
        // sucursales, la lista queda vacia y el $in => [] tiene que quedar puesto para
        // devolver 0 filas. Con != null el filtro se saltearia y devolveria TODOS.
        $sucursalesFiltro = null;

        if($selectProvincia != null || $selectLocalidad != null){
            $sucursalesFiltro = [];
            foreach ($this->traerSucursales($selectProvincia, $selectLocalidad) as $s) {
                $sucursalesFiltro[] = $this->normalizarSucursal($s['NRO_SUCURSAL']);
            }
        }

        if($selectTienda != null){
            $tiendas = array_map([$this, 'normalizarSucursal'], $selectTienda);
            // Si ya hay recorte por provincia/localidad -> interseccion (AND), no union:
            // "Cordoba" + "Sucursal Palermo" tiene que dar vacio.
            $sucursalesFiltro = ($sucursalesFiltro === null)
                ? $tiendas
                : array_values(array_intersect($sucursalesFiltro, $tiendas));
        }

        if($sucursalesFiltro !== null){
            // El campo puede estar guardado como int o como string segun el ETL:
            // se mandan las dos representaciones para no depender del tipo exacto.
            $valores = [];
            foreach (array_unique($sucursalesFiltro) as $nro) {
                if($nro === '') continue;
                $valores[] = $nro;
                if(is_numeric($nro)) $valores[] = (int) $nro;
            }
            $filter[self::CAMPO_SUCURSAL] = ['$in' => $valores];
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

        // Clave compuesta DNI+NOMBRE y no solo NOMBRE_CLI: agrupar por nombre fusiona
        // homonimos, y ahora eso mezclaria ademas las sucursales de dos personas distintas.
        // Tampoco se agrupa solo por DNI: los documentos con DNI vacio o null colapsarian
        // todos en una sola fila.
        $pipeline[] = ['$group' => [
            '_id'         => ['DNI' => '$DNI', 'NOMBRE' => '$NOMBRE_CLI'],
            'NOMBRE_CLI'  => ['$first' => '$NOMBRE_CLI'],
            'DNI'         => ['$first' => '$DNI'],
            'RANGO_ETARIO'=> ['$first' => '$RANGO_ETARIO'],
            'E_MAIL'      => ['$first' => '$E_MAIL'],
            // $addToSet y no $push: un cliente con 40 compras en la misma sucursal
            // aporta un solo valor al set.
            'SUCURSALES'  => ['$addToSet' => '$' . self::CAMPO_SUCURSAL],
            'ARTICULOS'   => ['$push'  => '$ARTICULOS'],
        ]];
        $pipeline[] = ['$sort' => ['NOMBRE_CLI' => 1]];

        $result = $mongoCollection->aggregate($pipeline, [
            'collation'    => ['locale' => 'es'],
            'allowDiskUse' => true,
        ]);

        $mapa = $this->mapaSucursales();

        $newArray = [];

        foreach ($result as $x => $document) {
            $documentArray = $document->getArrayCopy();

            // El _id ahora es compuesto, asi que el cast directo a string ya no sirve.
            $id = $documentArray['_id'];
            $newArray[$x]['ID'] = is_scalar($id) ? (string) $id : json_encode((array) $id);

            foreach ($documentArray as $key => $value) {
                if ($key === '_id') continue;
                $newArray[$x][$key] = $value;
            }

            // Sucursal => descripcion, provincia y localidad. Las claves del array hacen
            // de conjunto: unicidad y orden alfabetico gratis.
            $tiendas = []; $provincias = []; $localidades = [];

            $sucursales = isset($documentArray['SUCURSALES'])
                ? (array) $documentArray['SUCURSALES'] : [];

            foreach ($sucursales as $nro) {
                $k = $this->normalizarSucursal($nro);
                if($k === '') continue;

                if(isset($mapa[$k])){
                    $tiendas[$mapa[$k]['DESC_SUCURSAL']] = true;
                    if($mapa[$k]['PROVINCIA'] !== '') $provincias[$mapa[$k]['PROVINCIA']] = true;
                    if($mapa[$k]['LOCALIDAD'] !== '') $localidades[$mapa[$k]['LOCALIDAD']] = true;
                }else{
                    // Esta en Ventas pero no en DIRECCIONARIO: se muestra en vez de
                    // esconderlo con una celda vacia. Muchos "SUC N" => revisar
                    // normalizarSucursal() o el NRO_SUC_MADRE IS NULL de traerSucursales().
                    $tiendas['SUC '.$k] = true;
                }
            }

            ksort($tiendas); ksort($provincias); ksort($localidades);

            $newArray[$x]['TIENDAS']     = implode(', ', array_keys($tiendas));
            $newArray[$x]['PROVINCIAS']  = implode(', ', array_keys($provincias));
            $newArray[$x]['LOCALIDADES'] = implode(', ', array_keys($localidades));
        }

        return ($newArray);

    
    }



}
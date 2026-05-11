<?php

class Conexion{
    
    // Propiedades de la clase
    protected $envVars;
    protected $host_central;
    protected $database_central;
    protected $host_locales;
    protected $database_locales;
    protected $user;
    protected $pass;
    protected $pass_locales;
    protected $character;
    protected $host_mongo;
    protected $database_mongo;
    protected $database_uy;
    
    function __construct(){

        require_once(__DIR__.'/classEnv.php');

        $vars = new DotEnv(__DIR__ . '/../.env');
        $this->envVars = $vars->listVars();
        
        // Debug: ver qué variables se cargaron
        error_log("Constructor Conexion - Variables cargadas: " . json_encode($this->envVars));
        
        $this->host_central = $this->envVars['HOST_CENTRAL'];
        $this->database_central = $this->envVars['DATABASE_CENTRAL'];
        $this->host_locales = $this->envVars['HOST_LOCALES'];
        $this->database_locales = $this->envVars['DATABASE_LOCALES'];
        $this->user = $this->envVars['USER'];
        $this->pass = $this->envVars['PASS'];
        $this->pass_locales = $this->envVars['PASS_LOCALES'];
        $this->character = $this->envVars['CHARACTER'];
        $this->host_mongo = $this->envVars['HOST_MONGO'];
        $this->database_mongo = $this->envVars['DATABASE_MONGO'];
        $this->database_uy = $this->envVars['DATABASE_UY'];

        // Debug: ver qué propiedades se asignaron
        error_log("Constructor Conexion - Propiedades asignadas:");
        error_log("host_central: " . $this->host_central);
        error_log("database_central: " . $this->database_central);
        error_log("user: " . $this->user);
        error_log("pass: " . ($this->pass ? 'DEFINIDA' : 'NO_DEFINIDA'));

    }

    protected function servidor($nameServer) {
        
        if($nameServer == 'central'){
            return array($this->host_central, $this->database_central);
        }elseif($nameServer == 'locales'){
            return array($this->host_locales, $this->database_locales);
        }elseif($nameServer == 'uy'){
            return array($this->host_central, $this->database_uy);
        }else{
            return array($_SESSION['conexion_dns'], $_SESSION['base_nombre']);
        }

    }

    public function conectarSql($nameServer = null) {

        try {

            $serverDB = $this->servidor($nameServer);

            $pass = ($nameServer == 'locales') ? $this->pass_locales : $this->pass;

            // Forzar TCP/IP explícitamente para evitar Named Pipes
            // Formato: tcp:serverName,port
            $serverWithPort = 'tcp:' . $serverDB[0] . ',1433';

            $params = array( 
                "Database" => $serverDB[1], 
                "UID" => $this->user, 
                "PWD" => $pass, 
                "CharacterSet" => $this->character
            );

            $cid = sqlsrv_connect($serverWithPort, $params);

            return $cid;
            
        } catch (PDOException $e) {
            echo $e->getMessage();
        }


    }

    public function conectarMongoDb () {
        
        try {
            $mongoClient = new MongoDB\Client($this->host_mongo);
    
            $mongoDb = $mongoClient->selectDatabase($this->database_mongo);
    
            return $mongoDb;
        

        } catch (\Throwable $e) {

            echo $e->getMessage();

        }

    }

    

    private function buscarLocal($nameLocal){

        $sql = "select * from [LAKERBIS].locales_lakers.dbo.sucursales_lakers where cod_client = '$nameLocal'";

        $params = array( 
            "Database" => $this->database_central, 
            "UID" => $this->user, 
            "PWD" => $this->pass, 
            "CharacterSet" => $this->character
        );

        $cid = sqlsrv_connect($this->host_central, $params);

        $stmt = sqlsrv_query($cid, $sql);

        try {

            // $next_result = sqlsrv_next_result($stmt);

            while ($row = sqlsrv_fetch_array($stmt,SQLSRV_FETCH_ASSOC)) {

                $v[] = $row;

            }
    
            return $v[0];

        } catch (\Throwable $th) {

            print_r($th);

        }
    }
    
}

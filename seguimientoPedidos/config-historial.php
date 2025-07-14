
<?php
// seguimientoPedidos/config-historial.php
// Configuración específica para el módulo de historial de incidentes

// Configuración inicial de la aplicación
require_once '../Class/Conexion.php';
require_once '../Class/Pedido.php';

// Configuración de errores (ajustar según el entorno)
if (getenv('ENVIRONMENT') === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/logs/historial_errors.log');
}

// Configuración de zona horaria
date_default_timezone_set('America/Argentina/Buenos_Aires');

// Configuración de memoria y tiempo de ejecución para reportes grandes
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 600); // 10 minutos para exportaciones grandes

// Inicializar objetos principales
try {
    $pedidos = new Pedido();
    $sucursales = $pedidos->traerWarehouse();
} catch (Exception $e) {
    error_log("Error inicializando objetos: " . $e->getMessage());
    die("Error de configuración del sistema. Contacte al administrador.");
}

// Configuración de cache simple para mejorar rendimiento
class SimpleCache {
    private static $cacheDir = __DIR__ . '/cache/';
    private static $cacheTime = 300; // 5 minutos
    
    public static function get($key) {
        $file = self::$cacheDir . md5($key) . '.cache';
        
        if (!file_exists($file)) {
            return null;
        }
        
        $data = file_get_contents($file);
        $data = unserialize($data);
        
        if ($data['expires'] < time()) {
            unlink($file);
            return null;
        }
        
        return $data['content'];
    }
    
    public static function set($key, $content) {
        if (!is_dir(self::$cacheDir)) {
            mkdir(self::$cacheDir, 0755, true);
        }
        
        $file = self::$cacheDir . md5($key) . '.cache';
        $data = [
            'content' => $content,
            'expires' => time() + self::$cacheTime
        ];
        
        file_put_contents($file, serialize($data));
    }
    
    public static function clear() {
        $files = glob(self::$cacheDir . '*.cache');
        foreach ($files as $file) {
            unlink($file);
        }
    }
}

// Configuración de autenticación básica
class AuthManager {
    public static function isAuthenticated() {
        // Verificar sesión activa
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['usuario_autenticado']) && $_SESSION['usuario_autenticado'] === true;
    }
    
    public static function getUserLevel() {
        return $_SESSION['nivel_acceso'] ?? 'readonly';
    }
    
    public static function getUserName() {
        return $_SESSION['usuario_nombre'] ?? 'Usuario Anónimo';
    }
    
    public static function hasPermission($module) {
        $userLevel = self::getUserLevel();
        
        $permissions = [
            'admin' => ['read', 'write', 'export', 'delete'],
            'manager' => ['read', 'write', 'export'],
            'user' => ['read', 'export'],
            'readonly' => ['read']
        ];
        
        return in_array($module, $permissions[$userLevel] ?? []);
    }
}

// Configuración de logging
class Logger {
    private static $logFile = __DIR__ . '/logs/historial.log';
    
    public static function log($level, $message, $context = []) {
        if (!is_dir(dirname(self::$logFile))) {
            mkdir(dirname(self::$logFile), 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $user = AuthManager::getUserName();
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        $logEntry = sprintf(
            "[%s] %s: %s - Usuario: %s - IP: %s",
            $timestamp,
            strtoupper($level),
            $message,
            $user,
            $ip
        );
        
        if (!empty($context)) {
            $logEntry .= " - Context: " . json_encode($context);
        }
        
        $logEntry .= PHP_EOL;
        
        file_put_contents(self::$logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    public static function info($message, $context = []) {
        self::log('info', $message, $context);
    }
    
    public static function error($message, $context = []) {
        self::log('error', $message, $context);
    }
    
    public static function warning($message, $context = []) {
        self::log('warning', $message, $context);
    }
}

// Extensión de la clase Pedido con métodos adicionales para historial
if (!method_exists('Pedido', 'getHistorialIncidentesCompleto')) {
    
    // Agregar método a la clase Pedido existente
    eval('
    class PedidoExtended extends Pedido {
        
        public function getHistorialIncidentesCompleto($fechaInicio, $fechaFin, $tipoIncidente = "", $proveedor = "", $warehouse = "", $estado = "", $impacto = "") {
            Logger::info("Consultando historial de incidentes", [
                "fecha_inicio" => $fechaInicio,
                "fecha_fin" => $fechaFin,
                "filtros" => compact("tipoIncidente", "proveedor", "warehouse", "estado", "impacto")
            ]);
            
            // Verificar cache primero
            $cacheKey = "historial_" . md5(serialize(func_get_args()));
            $cached = SimpleCache::get($cacheKey);
            
            if ($cached !== null) {
                return $cached;
            }
            
            $cid = new Conexion();
            $cid_central = $cid->conectarSql("central");

            // Construir la consulta SQL con filtros
            $whereConditions = [];
            $whereConditions[] = "CAST(FECHA_PEDIDO AS DATE) BETWEEN \\"$fechaInicio\\" AND \\"$fechaFin\\"";
            
            if (!empty($warehouse)) {
                $whereConditions[] = "(WAREHOUSE LIKE \\"%$warehouse%\\" OR SUC_DESPACHO LIKE \\"%$warehouse%\\")";
            }
            
            if (!empty($estado)) {
                $whereConditions[] = "ESTADO = \\"$estado\\"";
            }
            
            $whereClause = implode(" AND ", $whereConditions);

            $sql = "
                SET DATEFORMAT YMD
                SELECT 
                    H.FECHA_PEDIDO,
                    H.NRO_ORDEN,
                    H.NRO_PEDIDO,
                    H.CLIENTE,
                    H.WAREHOUSE,
                    H.COD_ARTICULO_CAMBIO,
                    H.DESCRIPCION,
                    H.CANTIDAD,
                    H.ESTADO,
                    H.RESOLUCION,
                    H.SUC_DESPACHO,
                    H.COD_ARTICULO,
                    H.FECHA_ALTA,
                    H.FECHA_ULT_MODIF,
                    -- Campos calculados adicionales
                    CASE 
                        WHEN H.ESTADO = \\"resuelto\\" THEN DATEDIFF(day, H.FECHA_PEDIDO, H.FECHA_ULT_MODIF)
                        ELSE DATEDIFF(day, H.FECHA_PEDIDO, GETDATE())
                    END as DIAS_TRANSCURRIDOS,
                    CASE 
                        WHEN H.CANTIDAD >= 10 THEN \\"critico\\"
                        WHEN H.CANTIDAD >= 5 THEN \\"alto\\"
                        WHEN H.CANTIDAD >= 2 THEN \\"medio\\"
                        ELSE \\"bajo\\"
                    END as NIVEL_IMPACTO
                FROM RO_T_ENC_ECOMMERCE_HISTORIAL_FALT H
                WHERE $whereClause
                ORDER BY H.FECHA_PEDIDO DESC, H.NRO_PEDIDO DESC
            ";

            $result = sqlsrv_query($cid_central, $sql) or die(exit("Error en sqlsrv_query: " . print_r(sqlsrv_errors(), true)));

            $data = [];
            while ($v = sqlsrv_fetch_object($result)) {
                $data[] = array($v);
            }
            
            // Guardar en cache
            SimpleCache::set($cacheKey, $data);
            
            Logger::info("Consulta de historial completada", ["registros" => count($data)]);
            
            return $data;
        }
        
        public function getEstadisticasRapidas($dias = 30) {
            $cacheKey = "stats_$dias";
            $cached = SimpleCache::get($cacheKey);
            
            if ($cached !== null) {
                return $cached;
            }
            
            $cid = new Conexion();
            $cid_central = $cid->conectarSql("central");

            $fechaInicio = date("Y-m-d", strtotime("-$dias days"));
            $fechaFin = date("Y-m-d");

            $sql = "
                SET DATEFORMAT YMD
                SELECT 
                    COUNT(*) as TOTAL_INCIDENTES,
                    COUNT(CASE WHEN CAST(FECHA_PEDIDO AS DATE) = CAST(GETDATE() AS DATE) THEN 1 END) as INCIDENTES_HOY,
                    COUNT(CASE WHEN ESTADO = \\"resuelto\\" THEN 1 END) as RESUELTOS,
                    COUNT(CASE WHEN ESTADO = \\"abierto\\" THEN 1 END) as ABIERTOS,
                    COUNT(CASE WHEN ESTADO = \\"proceso\\" THEN 1 END) as EN_PROCESO,
                    COUNT(DISTINCT COD_ARTICULO_CAMBIO) as ARTICULOS_AFECTADOS,
                    COUNT(DISTINCT WAREHOUSE) as WAREHOUSES_AFECTADOS,
                    AVG(CASE WHEN ESTADO = \\"resuelto\\" THEN DATEDIFF(day, FECHA_PEDIDO, FECHA_ULT_MODIF) END) as TIEMPO_RESOLUCION_PROMEDIO
                FROM RO_T_ENC_ECOMMERCE_HISTORIAL_FALT
                WHERE CAST(FECHA_PEDIDO AS DATE) BETWEEN \\"$fechaInicio\\" AND \\"$fechaFin\\"
            ";

            $result = sqlsrv_query($cid_central, $sql) or die(exit("Error en sqlsrv_query: " . print_r(sqlsrv_errors(), true)));
            
            $data = [];
            if ($v = sqlsrv_fetch_object($result)) {
                $data = array($v);
            }
            
            SimpleCache::set($cacheKey, $data);
            
            return $data;
        }
    }
    ');
    
    // Reemplazar la instancia global
    $pedidos = new PedidoExtended();
}

// Configuración final del entorno
$config = [
    'fecha_maxima' => date('Y-m-d'),
    'fecha_por_defecto_inicio' => date('Y-m-d', strtotime('-30 days')),
    'usuario_actual' => AuthManager::getUserName(),
    'nivel_acceso' => AuthManager::getUserLevel(),
    'timezone' => 'America/Argentina/Buenos_Aires',
    'version' => '2.0.1',
    'last_update' => filemtime(__FILE__),
    'environment' => getenv('ENVIRONMENT') ?: 'production',
    'debug_mode' => getenv('ENVIRONMENT') === 'development',
    'cache_enabled' => true,
    'max_export_records' => 50000,
    'session_timeout' => 3600, // 1 hora
    'allowed_file_types' => ['excel', 'csv', 'pdf', 'json'],
    'rate_limit' => [
        'requests_per_minute' => 60,
        'requests_per_hour' => 1000
    ]
];

// Crear directorio de logs si no existe
if (!is_dir(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0755, true);
}

// Crear directorio de cache si no existe
if (!is_dir(__DIR__ . '/cache')) {
    mkdir(__DIR__ . '/cache', 0755, true);
}

// Log de inicialización
Logger::info('Sistema de historial de incidentes inicializado', [
    'version' => $config['version'],
    'usuario' => $config['usuario_actual'],
    'nivel_acceso' => $config['nivel_acceso']
]);
?>
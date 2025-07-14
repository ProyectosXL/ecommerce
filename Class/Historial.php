
    <?php

    require_once $_SERVER['DOCUMENT_ROOT']. '/ecommerce/Class/Conexion.php';

    class Historial{
        

        private function getDatos($sql){
            $cid = new Conexion();
            $cid_central = $cid->conectarSql('central');


            ini_set('max_execution_time', 300);
            $result=sqlsrv_query($cid_central,$sql)or die(exit("Error en sqlsrv_query"));

            $data = [];
            while($v=sqlsrv_fetch_object($result)){
                $data[] = array($v);
            };
            return $data;

        }

/**
 * Método para obtener el historial completo de pedidos con faltantes
 * 
 * @param string $fechaInicio Fecha de inicio en formato Y-m-d
 * @param string $fechaFin Fecha fin en formato Y-m-d
 * @param string $warehouse Filtro por warehouse
 * @param string $estado Filtro por estado (abierto, proceso, resuelto)
 * @param string $resolucion Filtro por tipo de resolución
 * @return array Array con los datos del historial de faltantes
 */
public function getHistorialFaltantesCompleto($fechaInicio, $fechaFin, $warehouse = '', $estado = '', $resolucion = '') {
    $cid = new Conexion();
    $cid_central = $cid->conectarSql('central');

    // Construir la consulta SQL con filtros
    $whereConditions = [];
    $whereConditions[] = "CAST(FECHA_PEDIDO AS DATE) BETWEEN '$fechaInicio' AND '$fechaFin'";
    
    if (!empty($warehouse)) {
        $whereConditions[] = "(WAREHOUSE LIKE '%$warehouse%' OR SUC_DESPACHO LIKE '%$warehouse%')";
    }
    
    if (!empty($estado)) {
        $whereConditions[] = "ESTADO = '$estado'";
    }
    
    if (!empty($resolucion)) {
        $whereConditions[] = "RESOLUCION = '$resolucion'";
    }
    
    $whereClause = implode(' AND ', $whereConditions);

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
                WHEN H.ESTADO = 'resuelto' THEN DATEDIFF(day, H.FECHA_PEDIDO, H.FECHA_ULT_MODIF)
                ELSE DATEDIFF(day, H.FECHA_PEDIDO, GETDATE())
            END as DIAS_TRANSCURRIDOS
        FROM RO_T_ENC_ECOMMERCE_HISTORIAL_FALT H
        WHERE $whereClause
        ORDER BY H.FECHA_PEDIDO DESC, H.NRO_PEDIDO DESC
    ";

    ini_set('max_execution_time', 300);
    $result = sqlsrv_query($cid_central, $sql) or die(exit("Error en sqlsrv_query: " . print_r(sqlsrv_errors(), true)));

    $data = [];
    while ($v = sqlsrv_fetch_object($result)) {
        $data[] = array($v);
    }

    return $data;
}

/**
 * Método para obtener estadísticas rápidas de pedidos con faltantes
 * 
 * @param int $dias Número de días hacia atrás para calcular estadísticas
 * @return array Array con estadísticas básicas
 */
public function getEstadisticasFaltantes($dias = 30) {
    $cid = new Conexion();
    $cid_central = $cid->conectarSql('central');

    $fechaInicio = date('Y-m-d', strtotime("-$dias days"));
    $fechaFin = date('Y-m-d');

    $sql = "
        SET DATEFORMAT YMD
        SELECT 
            COUNT(*) as TOTAL_FALTANTES,
            COUNT(CASE WHEN CAST(FECHA_PEDIDO AS DATE) = CAST(GETDATE() AS DATE) THEN 1 END) as FALTANTES_HOY,
            COUNT(CASE WHEN ESTADO = 'resuelto' THEN 1 END) as RESUELTOS,
            COUNT(CASE WHEN ESTADO = 'abierto' THEN 1 END) as ABIERTOS,
            COUNT(CASE WHEN ESTADO = 'proceso' THEN 1 END) as EN_PROCESO,
            COUNT(DISTINCT COD_ARTICULO_CAMBIO) as ARTICULOS_FALTANTES,
            COUNT(DISTINCT WAREHOUSE) as WAREHOUSES_AFECTADOS,
            AVG(CASE WHEN ESTADO = 'resuelto' THEN DATEDIFF(day, FECHA_PEDIDO, FECHA_ULT_MODIF) END) as TIEMPO_RESOLUCION_PROMEDIO
        FROM RO_T_ENC_ECOMMERCE_HISTORIAL_FALT
        WHERE CAST(FECHA_PEDIDO AS DATE) BETWEEN '$fechaInicio' AND '$fechaFin'
    ";

    $result = sqlsrv_query($cid_central, $sql) or die(exit("Error en sqlsrv_query: " . print_r(sqlsrv_errors(), true)));
    
    $data = [];
    if ($v = sqlsrv_fetch_object($result)) {
        $data = array($v);
    }

    return $data;
}

/**
 * Método para obtener faltantes por rango de fechas con información detallada
 * 
 * @param string $fechaInicio
 * @param string $fechaFin
 * @return array
 */
public function getFaltantesDetallados($fechaInicio, $fechaFin) {
    $cid = new Conexion();
    $cid_central = $cid->conectarSql('central');

    $sql = "
        SET DATEFORMAT YMD
        SELECT 
            H.*,
            -- Obtener información del pedido original si existe
            P.MARKETPLACE,
            P.METODO_ENVIO,
            P.LUGAR_ENTREGA,
            -- Contar comentarios asociados
            (SELECT COUNT(*) FROM RO_T_DET_ECOMMERCE_HISTORIAL_FALT D WHERE D.NRO_PEDIDO = H.NRO_PEDIDO) as CANTIDAD_COMENTARIOS
        FROM RO_T_ENC_ECOMMERCE_HISTORIAL_FALT H
        LEFT JOIN (
            SELECT DISTINCT NRO_PEDIDO, MARKETPLACE, METODO_ENVIO, LUGAR_ENTREGA
            FROM RO_ECOMMERCE_PEDIDOS_VIEW
        ) P ON H.NRO_PEDIDO = P.NRO_PEDIDO
        WHERE CAST(H.FECHA_PEDIDO AS DATE) BETWEEN '$fechaInicio' AND '$fechaFin'
        ORDER BY H.FECHA_PEDIDO DESC
    ";

    $result = sqlsrv_query($cid_central, $sql) or die(exit("Error en sqlsrv_query: " . print_r(sqlsrv_errors(), true)));

    $data = [];
    while ($v = sqlsrv_fetch_object($result)) {
        $data[] = array($v);
    }

    return $data;
}

/**
 * Método para obtener tendencias de faltantes por período
 * 
 * @param string $fechaInicio
 * @param string $fechaFin
 * @param string $agrupacion 'day', 'week', 'month'
 * @return array
 */
public function getTendenciasFaltantes($fechaInicio, $fechaFin, $agrupacion = 'day') {
    $cid = new Conexion();
    $cid_central = $cid->conectarSql('central');

    $formatoFecha = match($agrupacion) {
        'week' => "FORMAT(FECHA_PEDIDO, 'yyyy-MM') + '-W' + FORMAT(DATEPART(week, FECHA_PEDIDO), '00')",
        'month' => "FORMAT(FECHA_PEDIDO, 'yyyy-MM')",
        default => "CAST(FECHA_PEDIDO AS DATE)"
    };

    $sql = "
        SET DATEFORMAT YMD
        SELECT 
            $formatoFecha as PERIODO,
            COUNT(*) as CANTIDAD_FALTANTES,
            COUNT(CASE WHEN ESTADO = 'resuelto' THEN 1 END) as RESUELTOS,
            COUNT(CASE WHEN ESTADO = 'abierto' THEN 1 END) as ABIERTOS,
            COUNT(CASE WHEN ESTADO = 'proceso' THEN 1 END) as EN_PROCESO,
            AVG(CAST(CANTIDAD as FLOAT)) as CANTIDAD_PROMEDIO,
            COUNT(DISTINCT WAREHOUSE) as WAREHOUSES_AFECTADOS
        FROM RO_T_ENC_ECOMMERCE_HISTORIAL_FALT
        WHERE CAST(FECHA_PEDIDO AS DATE) BETWEEN '$fechaInicio' AND '$fechaFin'
        GROUP BY $formatoFecha
        ORDER BY PERIODO
    ";

    $result = sqlsrv_query($cid_central, $sql) or die(exit("Error en sqlsrv_query: " . print_r(sqlsrv_errors(), true)));

    $data = [];
    while ($v = sqlsrv_fetch_object($result)) {
        $data[] = array($v);
    }

    return $data;
}

/**
 * Método para obtener top de artículos con más faltantes
 * 
 * @param string $fechaInicio
 * @param string $fechaFin
 * @param int $limite
 * @return array
 */
public function getTopArticulosFaltantes($fechaInicio, $fechaFin, $limite = 15) {
    $cid = new Conexion();
    $cid_central = $cid->conectarSql('central');

    $sql = "
        SET DATEFORMAT YMD
        SELECT TOP $limite
            COD_ARTICULO_CAMBIO as CODIGO_ARTICULO,
            DESCRIPCION,
            COUNT(*) as CANTIDAD_FALTANTES,
            SUM(CANTIDAD) as CANTIDAD_TOTAL_FALTANTE,
            COUNT(DISTINCT WAREHOUSE) as WAREHOUSES_AFECTADOS,
            COUNT(CASE WHEN ESTADO = 'resuelto' THEN 1 END) as RESUELTOS,
            MAX(FECHA_PEDIDO) as ULTIMO_FALTANTE,
            AVG(CASE WHEN ESTADO = 'resuelto' THEN DATEDIFF(day, FECHA_PEDIDO, FECHA_ULT_MODIF) END) as TIEMPO_RESOLUCION_PROMEDIO
        FROM RO_T_ENC_ECOMMERCE_HISTORIAL_FALT
        WHERE CAST(FECHA_PEDIDO AS DATE) BETWEEN '$fechaInicio' AND '$fechaFin'
            AND COD_ARTICULO_CAMBIO IS NOT NULL 
            AND COD_ARTICULO_CAMBIO != ''
        GROUP BY COD_ARTICULO_CAMBIO, DESCRIPCION
        ORDER BY CANTIDAD_FALTANTES DESC, CANTIDAD_TOTAL_FALTANTE DESC
    ";

    $result = sqlsrv_query($cid_central, $sql) or die(exit("Error en sqlsrv_query: " . print_r(sqlsrv_errors(), true)));

    $data = [];
    while ($v = sqlsrv_fetch_object($result)) {
        $data[] = array($v);
    }

    return $data;
}

/**
 * Método para obtener faltantes por warehouse
 * 
 * @param string $fechaInicio
 * @param string $fechaFin
 * @return array
 */
public function getFaltantesPorWarehouse($fechaInicio, $fechaFin) {
    $cid = new Conexion();
    $cid_central = $cid->conectarSql('central');

    $sql = "
        SET DATEFORMAT YMD
        SELECT 
            COALESCE(WAREHOUSE, SUC_DESPACHO, 'Sin especificar') as WAREHOUSE,
            COUNT(*) as CANTIDAD_FALTANTES,
            COUNT(CASE WHEN ESTADO = 'resuelto' THEN 1 END) as RESUELTOS,
            COUNT(CASE WHEN ESTADO = 'abierto' THEN 1 END) as ABIERTOS,
            COUNT(CASE WHEN ESTADO = 'proceso' THEN 1 END) as EN_PROCESO,
            COUNT(DISTINCT COD_ARTICULO_CAMBIO) as ARTICULOS_AFECTADOS,
            SUM(CANTIDAD) as CANTIDAD_TOTAL,
            AVG(CASE WHEN ESTADO = 'resuelto' THEN DATEDIFF(day, FECHA_PEDIDO, FECHA_ULT_MODIF) END) as TIEMPO_RESOLUCION_PROMEDIO,
            MAX(FECHA_PEDIDO) as ULTIMO_FALTANTE
        FROM RO_T_ENC_ECOMMERCE_HISTORIAL_FALT
        WHERE CAST(FECHA_PEDIDO AS DATE) BETWEEN '$fechaInicio' AND '$fechaFin'
        GROUP BY COALESCE(WAREHOUSE, SUC_DESPACHO, 'Sin especificar')
        ORDER BY CANTIDAD_FALTANTES DESC
    ";

    $result = sqlsrv_query($cid_central, $sql) or die(exit("Error en sqlsrv_query: " . print_r(sqlsrv_errors(), true)));

    $data = [];
    while ($v = sqlsrv_fetch_object($result)) {
        $data[] = array($v);
    }

    return $data;
}

/**
 * Método para obtener análisis de resolución de faltantes
 * 
 * @param string $fechaInicio
 * @param string $fechaFin
 * @return array
 */
public function getAnalisisResolucionFaltantes($fechaInicio, $fechaFin) {
    $cid = new Conexion();
    $cid_central = $cid->conectarSql('central');

    $sql = "
        SET DATEFORMAT YMD
        SELECT 
            RESOLUCION,
            COUNT(*) as CANTIDAD,
            AVG(CASE WHEN ESTADO = 'resuelto' THEN DATEDIFF(day, FECHA_PEDIDO, FECHA_ULT_MODIF) END) as TIEMPO_PROMEDIO_RESOLUCION,
            MIN(CASE WHEN ESTADO = 'resuelto' THEN DATEDIFF(day, FECHA_PEDIDO, FECHA_ULT_MODIF) END) as TIEMPO_MIN_RESOLUCION,
            MAX(CASE WHEN ESTADO = 'resuelto' THEN DATEDIFF(day, FECHA_PEDIDO, FECHA_ULT_MODIF) END) as TIEMPO_MAX_RESOLUCION,
            COUNT(CASE WHEN ESTADO = 'resuelto' THEN 1 END) as CASOS_RESUELTOS,
            CAST(COUNT(CASE WHEN ESTADO = 'resuelto' THEN 1 END) as FLOAT) / COUNT(*) * 100 as PORCENTAJE_RESOLUCION
        FROM RO_T_ENC_ECOMMERCE_HISTORIAL_FALT
        WHERE CAST(FECHA_PEDIDO AS DATE) BETWEEN '$fechaInicio' AND '$fechaFin'
            AND RESOLUCION IS NOT NULL 
            AND RESOLUCION != ''
        GROUP BY RESOLUCION
        ORDER BY CANTIDAD DESC
    ";

    $result = sqlsrv_query($cid_central, $sql) or die(exit("Error en sqlsrv_query: " . print_r(sqlsrv_errors(), true)));

    $data = [];
    while ($v = sqlsrv_fetch_object($result)) {
        $data[] = array($v);
    }

    return $data;
}

/**
 * Método para buscar faltantes con filtros avanzados
 * 
 * @param array $filtros Array asociativo con filtros
 * @return array
 */
public function buscarFaltantesAvanzado($filtros = []) {
    $cid = new Conexion();
    $cid_central = $cid->conectarSql('central');

    // Construir condiciones WHERE dinámicamente
    $whereConditions = ["1=1"]; // Condición base
    
    if (!empty($filtros['fecha_inicio'])) {
        $whereConditions[] = "CAST(H.FECHA_PEDIDO AS DATE) >= '{$filtros['fecha_inicio']}'";
    }
    
    if (!empty($filtros['fecha_fin'])) {
        $whereConditions[] = "CAST(H.FECHA_PEDIDO AS DATE) <= '{$filtros['fecha_fin']}'";
    }
    
    if (!empty($filtros['nro_pedido'])) {
        $whereConditions[] = "H.NRO_PEDIDO LIKE '%{$filtros['nro_pedido']}%'";
    }
    
    if (!empty($filtros['nro_orden'])) {
        $whereConditions[] = "H.NRO_ORDEN LIKE '%{$filtros['nro_orden']}%'";
    }
    
    if (!empty($filtros['cliente'])) {
        $whereConditions[] = "H.CLIENTE LIKE '%{$filtros['cliente']}%'";
    }
    
    if (!empty($filtros['articulo'])) {
        $whereConditions[] = "(H.COD_ARTICULO_CAMBIO LIKE '%{$filtros['articulo']}%' OR H.DESCRIPCION LIKE '%{$filtros['articulo']}%')";
    }
    
    if (!empty($filtros['warehouse'])) {
        $whereConditions[] = "(H.WAREHOUSE LIKE '%{$filtros['warehouse']}%' OR H.SUC_DESPACHO LIKE '%{$filtros['warehouse']}%')";
    }
    
    if (!empty($filtros['estado'])) {
        $whereConditions[] = "H.ESTADO = '{$filtros['estado']}'";
    }
    
    if (!empty($filtros['resolucion'])) {
        $whereConditions[] = "H.RESOLUCION = '{$filtros['resolucion']}'";
    }

    $whereClause = implode(' AND ', $whereConditions);

    $sql = "
        SET DATEFORMAT YMD
        SELECT 
            H.*,
            -- Información adicional de seguimiento
            (SELECT COUNT(*) FROM RO_T_DET_ECOMMERCE_HISTORIAL_FALT D WHERE D.NRO_PEDIDO = H.NRO_PEDIDO) as CANTIDAD_SEGUIMIENTOS,
            (SELECT TOP 1 AGENTE FROM RO_T_DET_ECOMMERCE_HISTORIAL_FALT D WHERE D.NRO_PEDIDO = H.NRO_PEDIDO ORDER BY FECHA_PEDIDO DESC) as ULTIMO_AGENTE,
            (SELECT TOP 1 TIPO_CONTACTO FROM RO_T_DET_ECOMMERCE_HISTORIAL_FALT D WHERE D.NRO_PEDIDO = H.NRO_PEDIDO ORDER BY FECHA_PEDIDO DESC) as ULTIMO_TIPO_CONTACTO,
            DATEDIFF(day, H.FECHA_PEDIDO, COALESCE(H.FECHA_ULT_MODIF, GETDATE())) as DIAS_TRANSCURRIDOS
        FROM RO_T_ENC_ECOMMERCE_HISTORIAL_FALT H
        WHERE $whereClause
        ORDER BY H.FECHA_PEDIDO DESC
    ";

    $result = sqlsrv_query($cid_central, $sql) or die(exit("Error en sqlsrv_query: " . print_r(sqlsrv_errors(), true)));

    $data = [];
    while ($v = sqlsrv_fetch_object($result)) {
        $data[] = array($v);
    }

    return $data;
}

/**
 * Método para exportar datos de faltantes en formato específico
 * 
 * @param string $fechaInicio
 * @param string $fechaFin
 * @param string $formato 'excel', 'csv', 'json'
 * @param array $campos Campos específicos a exportar
 * @return array
 */
public function exportarFaltantes($fechaInicio, $fechaFin, $formato = 'excel', $campos = []) {
    $data = $this->getHistorialFaltantesCompleto($fechaInicio, $fechaFin);
    
    // Si no se especifican campos, usar todos
    if (empty($campos)) {
        $campos = [
            'NRO_PEDIDO', 'NRO_ORDEN', 'FECHA_PEDIDO', 'CLIENTE', 
            'COD_ARTICULO_CAMBIO', 'DESCRIPCION', 'CANTIDAD', 
            'WAREHOUSE', 'ESTADO', 'RESOLUCION', 'FECHA_ULT_MODIF'
        ];
    }
    
    $exportData = [];
    foreach ($data as $row) {
        $item = $row[0];
        $exportRow = [];
        
        foreach ($campos as $campo) {
            $exportRow[$campo] = isset($item->$campo) ? $item->$campo : '';
        }
        
        $exportData[] = $exportRow;
    }
    
    return [
        'data' => $exportData,
        'formato' => $formato,
        'campos' => $campos,
        'total_registros' => count($exportData),
        'fecha_exportacion' => date('Y-m-d H:i:s')
    ];
}

/**
 * Método para obtener alertas de faltantes críticos
 * 
 * @param int $dias Días hacia atrás para revisar
 * @return array
 */
public function getAlertasFaltantesCriticos($dias = 7) {
    $cid = new Conexion();
    $cid_central = $cid->conectarSql('central');

    $fechaInicio = date('Y-m-d', strtotime("-$dias days"));

    $sql = "
        SET DATEFORMAT YMD
        SELECT 
            H.*,
            DATEDIFF(day, H.FECHA_PEDIDO, GETDATE()) as DIAS_ABIERTO,
            CASE 
                WHEN DATEDIFF(day, H.FECHA_PEDIDO, GETDATE()) >= 7 AND H.ESTADO != 'resuelto' THEN 'CRITICO_TIEMPO'
                WHEN H.CANTIDAD >= 5 THEN 'CRITICO_CANTIDAD'
                WHEN H.COD_ARTICULO_CAMBIO IN (
                    SELECT TOP 5 COD_ARTICULO_CAMBIO 
                    FROM RO_T_ENC_ECOMMERCE_HISTORIAL_FALT 
                    WHERE CAST(FECHA_PEDIDO AS DATE) >= '$fechaInicio'
                    GROUP BY COD_ARTICULO_CAMBIO 
                    ORDER BY COUNT(*) DESC
                ) THEN 'CRITICO_FRECUENCIA'
                ELSE 'NORMAL'
            END as TIPO_ALERTA
        FROM RO_T_ENC_ECOMMERCE_HISTORIAL_FALT H
        WHERE CAST(H.FECHA_PEDIDO AS DATE) >= '$fechaInicio'
            AND (
                (DATEDIFF(day, H.FECHA_PEDIDO, GETDATE()) >= 7 AND H.ESTADO != 'resuelto') OR
                H.CANTIDAD >= 5 OR
                H.COD_ARTICULO_CAMBIO IN (
                    SELECT TOP 5 COD_ARTICULO_CAMBIO 
                    FROM RO_T_ENC_ECOMMERCE_HISTORIAL_FALT 
                    WHERE CAST(FECHA_PEDIDO AS DATE) >= '$fechaInicio'
                    GROUP BY COD_ARTICULO_CAMBIO 
                    ORDER BY COUNT(*) DESC
                )
            )
        ORDER BY 
            CASE 
                WHEN DATEDIFF(day, H.FECHA_PEDIDO, GETDATE()) >= 7 AND H.ESTADO != 'resuelto' THEN 1
                WHEN H.CANTIDAD >= 5 THEN 2
                ELSE 3
            END,
            H.FECHA_PEDIDO DESC
    ";

    $result = sqlsrv_query($cid_central, $sql) or die(exit("Error en sqlsrv_query: " . print_r(sqlsrv_errors(), true)));

    $data = [];
    while ($v = sqlsrv_fetch_object($result)) {
        $data[] = array($v);
    }

    return $data;
}


// Agregar estos métodos a la clase Pedido existente en Class/Pedido.php

/**
 * Método para obtener el historial completo de incidentes con filtros
 * 
 * @param string $fechaInicio Fecha de inicio en formato Y-m-d
 * @param string $fechaFin Fecha fin en formato Y-m-d
 * @param string $tipoIncidente Filtro por tipo de incidente
 * @param string $proveedor Filtro por proveedor
 * @param string $warehouse Filtro por warehouse
 * @param string $estado Filtro por estado (abierto, proceso, resuelto)
 * @param string $impacto Filtro por nivel de impacto
 * @return array Array con los datos del historial
 */
public function getHistorialIncidentesCompleto($fechaInicio, $fechaFin, $tipoIncidente = '', $proveedor = '', $warehouse = '', $estado = '', $impacto = '') {
    $cid = new Conexion();
    $cid_central = $cid->conectarSql('central');

    // Construir la consulta SQL con filtros
    $whereConditions = [];
    $whereConditions[] = "CAST(FECHA_PEDIDO AS DATE) BETWEEN '$fechaInicio' AND '$fechaFin'";
    
    if (!empty($warehouse)) {
        $whereConditions[] = "(WAREHOUSE LIKE '%$warehouse%' OR SUC_DESPACHO LIKE '%$warehouse%')";
    }
    
    if (!empty($estado)) {
        $whereConditions[] = "ESTADO = '$estado'";
    }
    
    $whereClause = implode(' AND ', $whereConditions);

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
                WHEN H.ESTADO = 'resuelto' THEN DATEDIFF(day, H.FECHA_PEDIDO, H.FECHA_ULT_MODIF)
                ELSE DATEDIFF(day, H.FECHA_PEDIDO, GETDATE())
            END as DIAS_TRANSCURRIDOS,
            CASE 
                WHEN H.CANTIDAD >= 10 THEN 'critico'
                WHEN H.CANTIDAD >= 5 THEN 'alto'
                WHEN H.CANTIDAD >= 2 THEN 'medio'
                ELSE 'bajo'
            END as NIVEL_IMPACTO
        FROM RO_T_ENC_ECOMMERCE_HISTORIAL_FALT H
        WHERE $whereClause
        ORDER BY H.FECHA_PEDIDO DESC, H.NRO_PEDIDO DESC
    ";

    ini_set('max_execution_time', 300);
    $result = sqlsrv_query($cid_central, $sql) or die(exit("Error en sqlsrv_query: " . print_r(sqlsrv_errors(), true)));

    $data = [];
    while ($v = sqlsrv_fetch_object($result)) {
        $data[] = array($v);
    }

    return $data;
}

/**
 * Método para obtener tendencias de incidentes por período
 * 
 * @param string $fechaInicio
 * @param string $fechaFin
 * @param string $agrupacion 'day', 'week', 'month'
 * @return array
 */
public function getTendenciasIncidentes($fechaInicio, $fechaFin, $agrupacion = 'day') {
    $cid = new Conexion();
    $cid_central = $cid->conectarSql('central');

    $formatoFecha = match($agrupacion) {
        'week' => "FORMAT(FECHA_PEDIDO, 'yyyy-MM') + '-W' + FORMAT(DATEPART(week, FECHA_PEDIDO), '00')",
        'month' => "FORMAT(FECHA_PEDIDO, 'yyyy-MM')",
        default => "CAST(FECHA_PEDIDO AS DATE)"
    };

    $sql = "
        SET DATEFORMAT YMD
        SELECT 
            $formatoFecha as PERIODO,
            COUNT(*) as CANTIDAD_INCIDENTES,
            COUNT(CASE WHEN ESTADO = 'resuelto' THEN 1 END) as RESUELTOS,
            COUNT(CASE WHEN ESTADO = 'abierto' THEN 1 END) as ABIERTOS,
            COUNT(CASE WHEN ESTADO = 'proceso' THEN 1 END) as EN_PROCESO,
            AVG(CAST(CANTIDAD as FLOAT)) as CANTIDAD_PROMEDIO,
            COUNT(DISTINCT WAREHOUSE) as WAREHOUSES_AFECTADOS
        FROM RO_T_ENC_ECOMMERCE_HISTORIAL_FALT
        WHERE CAST(FECHA_PEDIDO AS DATE) BETWEEN '$fechaInicio' AND '$fechaFin'
        GROUP BY $formatoFecha
        ORDER BY PERIODO
    ";

    $result = sqlsrv_query($cid_central, $sql) or die(exit("Error en sqlsrv_query: " . print_r(sqlsrv_errors(), true)));

    $data = [];
    while ($v = sqlsrv_fetch_object($result)) {
        $data[] = array($v);
    }

    return $data;
}

/**
 * Método para obtener top de artículos con más incidentes
 * 
 * @param string $fechaInicio
 * @param string $fechaFin
 * @param int $limite
 * @return array
 */
public function getTopArticulosIncidentes($fechaInicio, $fechaFin, $limite = 15) {
    $cid = new Conexion();
    $cid_central = $cid->conectarSql('central');

    $sql = "
        SET DATEFORMAT YMD
        SELECT TOP $limite
            COD_ARTICULO_CAMBIO as CODIGO_ARTICULO,
            DESCRIPCION,
            COUNT(*) as CANTIDAD_INCIDENTES,
            SUM(CANTIDAD) as CANTIDAD_TOTAL_AFECTADA,
            COUNT(DISTINCT WAREHOUSE) as WAREHOUSES_AFECTADOS,
            COUNT(CASE WHEN ESTADO = 'resuelto' THEN 1 END) as RESUELTOS,
            MAX(FECHA_PEDIDO) as ULTIMO_INCIDENTE,
            AVG(CASE WHEN ESTADO = 'resuelto' THEN DATEDIFF(day, FECHA_PEDIDO, FECHA_ULT_MODIF) END) as TIEMPO_RESOLUCION_PROMEDIO,
            CASE 
                WHEN COD_ARTICULO_CAMBIO LIKE 'X%' THEN 'Proveedor Premium'
                WHEN COD_ARTICULO_CAMBIO LIKE 'O%' THEN 'Proveedor Estándar'
                WHEN COD_ARTICULO_CAMBIO LIKE 'A%' THEN 'Proveedor Especial'
                ELSE 'Proveedor General'
            END as PROVEEDOR_ESTIMADO
        FROM RO_T_ENC_ECOMMERCE_HISTORIAL_FALT
        WHERE CAST(FECHA_PEDIDO AS DATE) BETWEEN '$fechaInicio' AND '$fechaFin'
            AND COD_ARTICULO_CAMBIO IS NOT NULL 
            AND COD_ARTICULO_CAMBIO != ''
        GROUP BY COD_ARTICULO_CAMBIO, DESCRIPCION
        ORDER BY CANTIDAD_INCIDENTES DESC, CANTIDAD_TOTAL_AFECTADA DESC
    ";

    $result = sqlsrv_query($cid_central, $sql) or die(exit("Error en sqlsrv_query: " . print_r(sqlsrv_errors(), true)));

    $data = [];
    while ($v = sqlsrv_fetch_object($result)) {
        $data[] = array($v);
    }

    return $data;
}

/**
 * Método para obtener incidentes por warehouse
 * 
 * @param string $fechaInicio
 * @param string $fechaFin
 * @return array
 */
public function getIncidentesPorWarehouse($fechaInicio, $fechaFin) {
    $cid = new Conexion();
    $cid_central = $cid->conectarSql('central');

    $sql = "
        SET DATEFORMAT YMD
        SELECT 
            COALESCE(WAREHOUSE, SUC_DESPACHO, 'Sin especificar') as WAREHOUSE,
            COUNT(*) as CANTIDAD_INCIDENTES,
            COUNT(CASE WHEN ESTADO = 'resuelto' THEN 1 END) as RESUELTOS,
            COUNT(CASE WHEN ESTADO = 'abierto' THEN 1 END) as ABIERTOS,
            COUNT(CASE WHEN ESTADO = 'proceso' THEN 1 END) as EN_PROCESO,
            COUNT(DISTINCT COD_ARTICULO_CAMBIO) as ARTICULOS_AFECTADOS,
            SUM(CANTIDAD) as CANTIDAD_TOTAL,
            AVG(CASE WHEN ESTADO = 'resuelto' THEN DATEDIFF(day, FECHA_PEDIDO, FECHA_ULT_MODIF) END) as TIEMPO_RESOLUCION_PROMEDIO,
            MAX(FECHA_PEDIDO) as ULTIMO_INCIDENTE
        FROM RO_T_ENC_ECOMMERCE_HISTORIAL_FALT
        WHERE CAST(FECHA_PEDIDO AS DATE) BETWEEN '$fechaInicio' AND '$fechaFin'
        GROUP BY COALESCE(WAREHOUSE, SUC_DESPACHO, 'Sin especificar')
        ORDER BY CANTIDAD_INCIDENTES DESC
    ";

    $result = sqlsrv_query($cid_central, $sql) or die(exit("Error en sqlsrv_query: " . print_r(sqlsrv_errors(), true)));

    $data = [];
    while ($v = sqlsrv_fetch_object($result)) {
        $data[] = array($v);
    }

    return $data;
}

/**
 * Método para obtener análisis de resolución de incidentes
 * 
 * @param string $fechaInicio
 * @param string $fechaFin
 * @return array
 */
public function getAnalisisResolucion($fechaInicio, $fechaFin) {
    $cid = new Conexion();
    $cid_central = $cid->conectarSql('central');

    $sql = "
        SET DATEFORMAT YMD
        SELECT 
            RESOLUCION,
            COUNT(*) as CANTIDAD,
            AVG(CASE WHEN ESTADO = 'resuelto' THEN DATEDIFF(day, FECHA_PEDIDO, FECHA_ULT_MODIF) END) as TIEMPO_PROMEDIO_RESOLUCION,
            MIN(CASE WHEN ESTADO = 'resuelto' THEN DATEDIFF(day, FECHA_PEDIDO, FECHA_ULT_MODIF) END) as TIEMPO_MIN_RESOLUCION,
            MAX(CASE WHEN ESTADO = 'resuelto' THEN DATEDIFF(day, FECHA_PEDIDO, FECHA_ULT_MODIF) END) as TIEMPO_MAX_RESOLUCION,
            COUNT(CASE WHEN ESTADO = 'resuelto' THEN 1 END) as CASOS_RESUELTOS,
            CAST(COUNT(CASE WHEN ESTADO = 'resuelto' THEN 1 END) as FLOAT) / COUNT(*) * 100 as PORCENTAJE_RESOLUCION
        FROM RO_T_ENC_ECOMMERCE_HISTORIAL_FALT
        WHERE CAST(FECHA_PEDIDO AS DATE) BETWEEN '$fechaInicio' AND '$fechaFin'
            AND RESOLUCION IS NOT NULL 
            AND RESOLUCION != ''
        GROUP BY RESOLUCION
        ORDER BY CANTIDAD DESC
    ";

    $result = sqlsrv_query($cid_central, $sql) or die(exit("Error en sqlsrv_query: " . print_r(sqlsrv_errors(), true)));

    $data = [];
    while ($v = sqlsrv_fetch_object($result)) {
        $data[] = array($v);
    }

    return $data;
}

/**
 * Método para obtener métricas de rendimiento por agente
 * 
 * @param string $fechaInicio
 * @param string $fechaFin
 * @return array
 */
public function getMetricasAgentes($fechaInicio, $fechaFin) {
    $cid = new Conexion();
    $cid_central = $cid->conectarSql('central');

    $sql = "
        SET DATEFORMAT YMD
        SELECT 
            D.AGENTE,
            COUNT(DISTINCT D.NRO_PEDIDO) as CASOS_ATENDIDOS,
            COUNT(*) as TOTAL_INTERACCIONES,
            COUNT(DISTINCT D.TIPO_CONTACTO) as CANALES_UTILIZADOS,
            STRING_AGG(DISTINCT D.TIPO_CONTACTO, ', ') as CANALES_LISTA,
            AVG(LEN(D.COMENTARIOS)) as LONGITUD_PROMEDIO_COMENTARIOS,
            COUNT(CASE WHEN H.ESTADO = 'resuelto' THEN 1 END) as CASOS_RESUELTOS,
            CAST(COUNT(CASE WHEN H.ESTADO = 'resuelto' THEN 1 END) as FLOAT) / COUNT(DISTINCT D.NRO_PEDIDO) * 100 as PORCENTAJE_RESOLUCION
        FROM RO_T_DET_ECOMMERCE_HISTORIAL_FALT D
        INNER JOIN RO_T_ENC_ECOMMERCE_HISTORIAL_FALT H ON D.NRO_PEDIDO = H.NRO_PEDIDO
        WHERE CAST(H.FECHA_PEDIDO AS DATE) BETWEEN '$fechaInicio' AND '$fechaFin'
            AND D.AGENTE IS NOT NULL 
            AND D.AGENTE != ''
        GROUP BY D.AGENTE
        ORDER BY CASOS_ATENDIDOS DESC, PORCENTAJE_RESOLUCION DESC
    ";

    $result = sqlsrv_query($cid_central, $sql) or die(exit("Error en sqlsrv_query: " . print_r(sqlsrv_errors(), true)));

    $data = [];
    while ($v = sqlsrv_fetch_object($result)) {
        $data[] = array($v);
    }

    return $data;
}

/**
 * Método para buscar incidentes con filtros avanzados
 * 
 * @param array $filtros Array asociativo con filtros
 * @return array
 */
public function buscarIncidentesAvanzado($filtros = []) {
    $cid = new Conexion();
    $cid_central = $cid->conectarSql('central');

    // Construir condiciones WHERE dinámicamente
    $whereConditions = ["1=1"]; // Condición base
    
    if (!empty($filtros['fecha_inicio'])) {
        $whereConditions[] = "CAST(H.FECHA_PEDIDO AS DATE) >= '{$filtros['fecha_inicio']}'";
    }
    
    if (!empty($filtros['fecha_fin'])) {
        $whereConditions[] = "CAST(H.FECHA_PEDIDO AS DATE) <= '{$filtros['fecha_fin']}'";
    }
    
    if (!empty($filtros['nro_pedido'])) {
        $whereConditions[] = "H.NRO_PEDIDO LIKE '%{$filtros['nro_pedido']}%'";
    }
    
    if (!empty($filtros['nro_orden'])) {
        $whereConditions[] = "H.NRO_ORDEN LIKE '%{$filtros['nro_orden']}%'";
    }
    
    if (!empty($filtros['cliente'])) {
        $whereConditions[] = "H.CLIENTE LIKE '%{$filtros['cliente']}%'";
    }
    
    if (!empty($filtros['articulo'])) {
        $whereConditions[] = "(H.COD_ARTICULO_CAMBIO LIKE '%{$filtros['articulo']}%' OR H.DESCRIPCION LIKE '%{$filtros['articulo']}%')";
    }
    
    if (!empty($filtros['warehouse'])) {
        $whereConditions[] = "(H.WAREHOUSE LIKE '%{$filtros['warehouse']}%' OR H.SUC_DESPACHO LIKE '%{$filtros['warehouse']}%')";
    }
    
    if (!empty($filtros['estado'])) {
        $whereConditions[] = "H.ESTADO = '{$filtros['estado']}'";
    }
    
    if (!empty($filtros['resolucion'])) {
        $whereConditions[] = "H.RESOLUCION = '{$filtros['resolucion']}'";
    }

    $whereClause = implode(' AND ', $whereConditions);

    $sql = "
        SET DATEFORMAT YMD
        SELECT 
            H.*,
            -- Información adicional de seguimiento
            (SELECT COUNT(*) FROM RO_T_DET_ECOMMERCE_HISTORIAL_FALT D WHERE D.NRO_PEDIDO = H.NRO_PEDIDO) as CANTIDAD_SEGUIMIENTOS,
            (SELECT TOP 1 AGENTE FROM RO_T_DET_ECOMMERCE_HISTORIAL_FALT D WHERE D.NRO_PEDIDO = H.NRO_PEDIDO ORDER BY FECHA_PEDIDO DESC) as ULTIMO_AGENTE,
            (SELECT TOP 1 TIPO_CONTACTO FROM RO_T_DET_ECOMMERCE_HISTORIAL_FALT D WHERE D.NRO_PEDIDO = H.NRO_PEDIDO ORDER BY FECHA_PEDIDO DESC) as ULTIMO_TIPO_CONTACTO,
            DATEDIFF(day, H.FECHA_PEDIDO, COALESCE(H.FECHA_ULT_MODIF, GETDATE())) as DIAS_TRANSCURRIDOS
        FROM RO_T_ENC_ECOMMERCE_HISTORIAL_FALT H
        WHERE $whereClause
        ORDER BY H.FECHA_PEDIDO DESC
    ";

    $result = sqlsrv_query($cid_central, $sql) or die(exit("Error en sqlsrv_query: " . print_r(sqlsrv_errors(), true)));

    $data = [];
    while ($v = sqlsrv_fetch_object($result)) {
        $data[] = array($v);
    }

    return $data;
}

/**
 * Método para exportar datos de incidentes en formato específico
 * 
 * @param string $fechaInicio
 * @param string $fechaFin
 * @param string $formato 'excel', 'csv', 'json'
 * @param array $campos Campos específicos a exportar
 * @return array
 */
public function exportarIncidentes($fechaInicio, $fechaFin, $formato = 'excel', $campos = []) {
    $data = $this->getHistorialIncidentesCompleto($fechaInicio, $fechaFin);
    
    // Si no se especifican campos, usar todos
    if (empty($campos)) {
        $campos = [
            'NRO_PEDIDO', 'NRO_ORDEN', 'FECHA_PEDIDO', 'CLIENTE', 
            'COD_ARTICULO_CAMBIO', 'DESCRIPCION', 'CANTIDAD', 
            'WAREHOUSE', 'ESTADO', 'RESOLUCION', 'FECHA_ULT_MODIF'
        ];
    }
    
    $exportData = [];
    foreach ($data as $row) {
        $item = $row[0];
        $exportRow = [];
        
        foreach ($campos as $campo) {
            $exportRow[$campo] = isset($item->$campo) ? $item->$campo : '';
        }
        
        $exportData[] = $exportRow;
    }
    
    return [
        'data' => $exportData,
        'formato' => $formato,
        'campos' => $campos,
        'total_registros' => count($exportData),
        'fecha_exportacion' => date('Y-m-d H:i:s')
    ];
}

/**
 * Método para obtener alertas de incidentes críticos
 * 
 * @param int $dias Días hacia atrás para revisar
 * @return array
 */
public function getAlertasIncidentesCriticos($dias = 7) {
    $cid = new Conexion();
    $cid_central = $cid->conectarSql('central');

    $fechaInicio = date('Y-m-d', strtotime("-$dias days"));

    $sql = "
        SET DATEFORMAT YMD
        SELECT 
            H.*,
            DATEDIFF(day, H.FECHA_PEDIDO, GETDATE()) as DIAS_ABIERTO,
            CASE 
                WHEN DATEDIFF(day, H.FECHA_PEDIDO, GETDATE()) >= 7 AND H.ESTADO != 'resuelto' THEN 'CRITICO_TIEMPO'
                WHEN H.CANTIDAD >= 10 THEN 'CRITICO_CANTIDAD'
                WHEN H.COD_ARTICULO_CAMBIO IN (
                    SELECT TOP 5 COD_ARTICULO_CAMBIO 
                    FROM RO_T_ENC_ECOMMERCE_HISTORIAL_FALT 
                    WHERE CAST(FECHA_PEDIDO AS DATE) >= '$fechaInicio'
                    GROUP BY COD_ARTICULO_CAMBIO 
                    ORDER BY COUNT(*) DESC
                ) THEN 'CRITICO_FRECUENCIA'
                ELSE 'NORMAL'
            END as TIPO_ALERTA
        FROM RO_T_ENC_ECOMMERCE_HISTORIAL_FALT H
        WHERE CAST(H.FECHA_PEDIDO AS DATE) >= '$fechaInicio'
            AND (
                (DATEDIFF(day, H.FECHA_PEDIDO, GETDATE()) >= 7 AND H.ESTADO != 'resuelto') OR
                H.CANTIDAD >= 10 OR
                H.COD_ARTICULO_CAMBIO IN (
                    SELECT TOP 5 COD_ARTICULO_CAMBIO 
                    FROM RO_T_ENC_ECOMMERCE_HISTORIAL_FALT 
                    WHERE CAST(FECHA_PEDIDO AS DATE) >= '$fechaInicio'
                    GROUP BY COD_ARTICULO_CAMBIO 
                    ORDER BY COUNT(*) DESC
                )
            )
        ORDER BY 
            CASE 
                WHEN DATEDIFF(day, H.FECHA_PEDIDO, GETDATE()) >= 7 AND H.ESTADO != 'resuelto' THEN 1
                WHEN H.CANTIDAD >= 10 THEN 2
                ELSE 3
            END,
            H.FECHA_PEDIDO DESC
    ";

    $result = sqlsrv_query($cid_central, $sql) or die(exit("Error en sqlsrv_query: " . print_r(sqlsrv_errors(), true)));

    $data = [];
    while ($v = sqlsrv_fetch_object($result)) {
        $data[] = array($v);
    }

    return $data;
}[] = array($v);
    }

    return $data;
}

/**
 * Método para obtener estadísticas rápidas de incidentes
 * 
 * @param int $dias Número de días hacia atrás para calcular estadísticas
 * @return array Array con estadísticas básicas
 */
public function getEstadisticasRapidas($dias = 30) {
    $cid = new Conexion();
    $cid_central = $cid->conectarSql('central');

    $fechaInicio = date('Y-m-d', strtotime("-$dias days"));
    $fechaFin = date('Y-m-d');

    $sql = "
        SET DATEFORMAT YMD
        SELECT 
            COUNT(*) as TOTAL_INCIDENTES,
            COUNT(CASE WHEN CAST(FECHA_PEDIDO AS DATE) = CAST(GETDATE() AS DATE) THEN 1 END) as INCIDENTES_HOY,
            COUNT(CASE WHEN ESTADO = 'resuelto' THEN 1 END) as RESUELTOS,
            COUNT(CASE WHEN ESTADO = 'abierto' THEN 1 END) as ABIERTOS,
            COUNT(CASE WHEN ESTADO = 'proceso' THEN 1 END) as EN_PROCESO,
            COUNT(DISTINCT COD_ARTICULO_CAMBIO) as ARTICULOS_AFECTADOS,
            COUNT(DISTINCT WAREHOUSE) as WAREHOUSES_AFECTADOS,
            AVG(CASE WHEN ESTADO = 'resuelto' THEN DATEDIFF(day, FECHA_PEDIDO, FECHA_ULT_MODIF) END) as TIEMPO_RESOLUCION_PROMEDIO
        FROM RO_T_ENC_ECOMMERCE_HISTORIAL_FALT
        WHERE CAST(FECHA_PEDIDO AS DATE) BETWEEN '$fechaInicio' AND '$fechaFin'
    ";

    $result = sqlsrv_query($cid_central, $sql) or die(exit("Error en sqlsrv_query: " . print_r(sqlsrv_errors(), true)));
    
    $data = [];
    if ($v = sqlsrv_fetch_object($result)) {
        $data = array($v);
    }

    return $data;
}

/**
 * Método para obtener incidentes por rango de fechas con información detallada
 * 
 * @param string $fechaInicio
 * @param string $fechaFin
 * @return array
 */
public function getIncidentesDetallados($fechaInicio, $fechaFin) {
    $cid = new Conexion();
    $cid_central = $cid->conectarSql('central');

    $sql = "
        SET DATEFORMAT YMD
        SELECT 
            H.*,
            -- Obtener información del pedido original si existe
            P.MARKETPLACE,
            P.METODO_ENVIO,
            P.LUGAR_ENTREGA,
            -- Contar comentarios asociados
            (SELECT COUNT(*) FROM RO_T_DET_ECOMMERCE_HISTORIAL_FALT D WHERE D.NRO_PEDIDO = H.NRO_PEDIDO) as CANTIDAD_COMENTARIOS,
            -- Información del artículo si está disponible
            CASE 
                WHEN H.COD_ARTICULO_CAMBIO LIKE 'X%' THEN 'Proveedor Premium'
                WHEN H.COD_ARTICULO_CAMBIO LIKE 'O%' THEN 'Proveedor Estándar'
                WHEN H.COD_ARTICULO_CAMBIO LIKE 'A%' THEN 'Proveedor Especial'
                ELSE 'Proveedor General'
            END as PROVEEDOR_ESTIMADO
        FROM RO_T_ENC_ECOMMERCE_HISTORIAL_FALT H
        LEFT JOIN (
            SELECT DISTINCT NRO_PEDIDO, MARKETPLACE, METODO_ENVIO, LUGAR_ENTREGA
            FROM RO_ECOMMERCE_PEDIDOS_VIEW
        ) P ON H.NRO_PEDIDO = P.NRO_PEDIDO
        WHERE CAST(H.FECHA_PEDIDO AS DATE) BETWEEN '$fechaInicio' AND '$fechaFin'
        ORDER BY H.FECHA_PEDIDO DESC
    ";

    $result = sqlsrv_query($cid_central, $sql) or die(exit("Error en sqlsrv_query: " . print_r(sqlsrv_errors(), true)));

    $data = [];
    while ($v = sqlsrv_fetch_object($result)) {
        $data
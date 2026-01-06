<?php

require_once $_SERVER['DOCUMENT_ROOT']. '/ecommerce/Class/Conexion.php';

class Pedido{
    
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
    
    public function traerPedidos($desde, $hasta, $tienda, $warehouse, $estado = null, $orden = '%'){
        
        // Manejar el estado: si es null, pasar NULL sin comillas al SQL
        $estadoSQL = ($estado === null || $estado === '') ? 'NULL' : "'$estado'";
            
        $sql = "
        SET DATEFORMAT YMD;
        SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;
        EXEC RO_ECOMMERCE_PEDIDOS '$desde', '$hasta', '$tienda', '$warehouse', $estadoSQL, '$orden'
        ";
        
        $array = $this->getDatos($sql);
        
        return $array;
    }

    public function traerWarehouse(){
        $sql = "SELECT WAREHOUSE FROM
                (
                SELECT REPLACE(A.NOMBRE_SUC, 'RT - SUC - ', '') WAREHOUSE FROM STA22 A
                INNER JOIN (SELECT NRO_SUCURSAL FROM LAKERBIS.LOCALES_LAKERS.DBO.SUCURSALES_LAKERS WHERE CANAL = 'PROPIOS' AND HABILITADO = 1) B ON A.SUCURSAL_DESTINO = B.NRO_SUCURSAL
                WHERE A.NOMBRE_SUC LIKE 'RT%'
                UNION ALL
                SELECT 'CENTRAL'
                ) A
                ORDER BY 1
        ";

        $array = $this->getDatos($sql);    
        return $array;
    }

    public function buscarPedido($desde, $hasta, $orden){
        $sql = "
        SET DATEFORMAT YMD
        EXEC RO_SP_ECOMMERCE_PEDIDOS_FLUJO '$desde', '$hasta', '$orden'
        ";
        $array = $this->getDatos($sql);    
        return $array;
    }

    public function buscarDetallePedido($desde, $hasta, $orden){
        $sql = "
        SET DATEFORMAT YMD
        EXEC RO_SP_ECOMMERCE_PEDIDOS_FLUJO_DETALLE '$desde', '$hasta', '$orden'
        ";

        $array = $this->getDatos($sql);    
        return $array;
    }

    public function buscarStockArticulo($sucursal){
        // Mapeo de nombres de sucursales entre warehouse y tabla de stock
        $mapeoSucursales = [
            'PILAR' => 'PALMAS DEL PILAR',
            'UNICENTER' => 'UNICENTER',
            'ALTO PALERMO' => 'ALTO PALERMO',
            'AVELLANEDA' => 'AVELLANEDA',
            'ABASTO' => 'ABASTO',
            'SOLAR' => 'SOLAR',
            'TORTUGAS' => 'TORTUGAS',
            'SAN MARTIN' => 'FACTORY SAN MARTIN',
            'PASEO DEL SIGLO' => 'PASEO DEL SIGLO',
            'MDP GALLEGOS' => 'MDP GALLEGOS',
            'MDP ALDREY' => 'PASEO ALDREY',
            'FLORES 1' => 'FLORES 1',
            'ALTO ROSARIO' => 'ALTO ROSARIO',
            'CABALLITO' => 'CABALLITO',
            'PORTAL ROSARIO' => 'PORTAL ROSARIO',
            'DOT' => 'DOT',
            'PALACE GARDEN' => 'PALACE GARDEN',
            'GURRUCHAGA' => 'GURRUCHAGA',
            'FLORES 2' => 'FLORES 2',
            'SOLEIL' => 'SOLEIL',
            'PARQUE BROWN' => 'PARQUE BROWN',
            'DISTRITO ARCOS' => 'DISTRITO ARCOS',
            'SAN JUSTO' => 'SAN JUSTO',
            'CENTRAL' => 'CASA CENTRAL'
        ];
        
        // Buscar en el mapeo (case-insensitive)
        $sucursalUpper = strtoupper(trim($sucursal));
        $sucursalStock = $sucursal;
        
        foreach ($mapeoSucursales as $key => $value) {
            if (strtoupper($key) === $sucursalUpper) {
                $sucursalStock = $value;
                break;
            }
        }
        
        $sql = "SELECT NRO_SUCURSAL, DESC_SUCURSAL, ARTICULO, DESC_CTA_ARTICULO, CANT_STOCK FROM [LAKERBIS].LOCALES_LAKERS.DBO.RO_STOCK_LAKERS A
                INNER JOIN [LAKERBIS].LOCALES_LAKERS.DBO.CTA_ARTICULO B ON A.ARTICULO = B.COD_ARTICULO
                WHERE DESC_SUCURSAL = '$sucursalStock' AND A.ARTICULO LIKE '[XO]%'
                ORDER BY ARTICULO
        ";

        $array = $this->getDatos($sql);    
        return $array;
    }

    public function guardarHistorialReclamo($data) {
        $cid = new Conexion();
        $cid_central = $cid->conectarSql('central');
        
        // Establecer formato de fecha
        sqlsrv_query($cid_central, "SET DATEFORMAT YMD");
        
        // Parsear y formatear la fecha correctamente
        $fechaPedido = null;
        if (!empty($data['fechaHora'])) {
            $fechaTexto = trim($data['fechaHora']);
            
            // Formato: "31/10/2025 10:30:45" o "31/10/2025"
            if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})/', $fechaTexto, $matches)) {
                $fechaPedido = $matches[3] . '-' . $matches[2] . '-' . $matches[1]; // YYYY-MM-DD
            } 
            // Formato: "2025-10-31 10:30:45" o "2025-10-31"
            else if (preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $fechaTexto)) {
                $fechaPedido = substr($fechaTexto, 0, 10);
            }
            // Usar strtotime como fallback
            else {
                $timestamp = strtotime($fechaTexto);
                if ($timestamp !== false) {
                    $fechaPedido = date('Y-m-d', $timestamp);
                }
            }
        }
        
        $fechaPedidoSQL = $fechaPedido ? "'$fechaPedido'" : "GETDATE()";
    
        $sql = "SET DATEFORMAT YMD;
                INSERT INTO RO_T_ENC_ECOMMERCE_HISTORIAL_FALT (FECHA_PEDIDO, NRO_ORDEN, NRO_PEDIDO, CLIENTE, WAREHOUSE, COD_ARTICULO_CAMBIO, DESCRIPCION, CANTIDAD, ESTADO, RESOLUCION, SUC_DESPACHO, COD_ARTICULO)
                VALUES ($fechaPedidoSQL, '".$data['nroOrden']."', '".$data['nro_pedido']."', '".$data['cliente']."', '".$data['sucursal']."', '".$data['articulo']."', '".$data['descripcion']."', '".$data['modalCantidad']."', '".$data['estado']."', '".$data['resolucion']."', '".$data['sucursal']."', '".$data['modalCodigo']."')
        ";

        $result=sqlsrv_query($cid_central,$sql)or die(exit("Error en sqlsrv_query: " . print_r(sqlsrv_errors(), true)));
        return $result;
    }

    public function traerHistorialReclamo($nro_pedido){
        $cid = new Conexion();
        $cid_central = $cid->conectarSql('central');
        $sql = "SELECT * FROM RO_T_ENC_ECOMMERCE_HISTORIAL_FALT WHERE NRO_PEDIDO = '$nro_pedido'";
       
        $result = sqlsrv_query($cid_central,$sql)or die(exit("Error en sqlsrv_query"));

        $data = [];
        while($v=sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)){
            $data[] = array($v);
        };
        if(count($data) == 0){
            return false;
        }
        return $data[0];
    }

    public function guardarReclamoDetalle ($stringValues){
        $cid = new Conexion();
        $cid_central = $cid->conectarSql('central');
        $sql = "INSERT INTO RO_T_DET_ECOMMERCE_HISTORIAL_FALT (NRO_PEDIDO, COMENTARIOS, TIPO_CONTACTO, AGENTE, FECHA_PEDIDO) VALUES $stringValues";

        $result=sqlsrv_query($cid_central,$sql)or die(exit("Error en sqlsrv_query"));
        return $result;
    }
    
    public function listarReclamoDetalle($nro_pedido) {
        $cid = new Conexion();
        $cid_central = $cid->conectarSql('central');
        $sql = "SELECT * FROM RO_T_DET_ECOMMERCE_HISTORIAL_FALT WHERE NRO_PEDIDO = '$nro_pedido'";

        $result=sqlsrv_query($cid_central,$sql)or die(exit("Error en sqlsrv_query"));

        $data = [];
        while($v=sqlsrv_fetch_object($result)){
            $data[] = array($v);
        };
        return $data;
    }

    public function consultarEstado ($nroOrden) {
        $cid = new Conexion();
        $cid_central = $cid->conectarSql('central');
        $sql = "SELECT ESTADO FROM RO_T_ENC_ECOMMERCE_HISTORIAL_FALT WHERE NRO_ORDEN = '$nroOrden'";

        $result=sqlsrv_query($cid_central,$sql)or die(exit("Error en sqlsrv_query"));

        $data = '';
        while($v=sqlsrv_fetch_object($result)){
            // retorna solo el estado 
            $data = $v->ESTADO;
        };
        return $data;
    }

public function actualizarEstadoReclamo($nro_pedido, $estado, $nro_orden = null) {
        $cid = new Conexion();
        $cid_central = $cid->conectarSql('central');
        
        // Verificar si existe un registro en la tabla de historial
        $sqlCheck = "SELECT COUNT(*) as count FROM RO_T_ENC_ECOMMERCE_HISTORIAL_FALT WHERE NRO_PEDIDO = '$nro_pedido'";
        $resultCheck = sqlsrv_query($cid_central, $sqlCheck);
        $row = sqlsrv_fetch_array($resultCheck, SQLSRV_FETCH_ASSOC);

        // Preparar el Nro de Orden para la consulta SQL
        $nroOrdenSQL = $nro_orden ? "'$nro_orden'" : "NULL";
        
        if ($row['count'] > 0) {
            // Actualizar registro existente - solo estado y fecha de última modificación
            // También actualizamos el NRO_ORDEN si viene, para asegurar consistencia.
            $updateNroOrdenSQL = $nro_orden ? ", NRO_ORDEN = $nroOrdenSQL" : "";
            $sql = "UPDATE RO_T_ENC_ECOMMERCE_HISTORIAL_FALT 
                    SET ESTADO = '$estado', FECHA_ULT_MODIF = GETDATE() $updateNroOrdenSQL
                    WHERE NRO_PEDIDO = '$nro_pedido' AND (NRO_ORDEN IS NULL OR NRO_ORDEN = '')";
        } else {
            // Crear registro básico si no existe - con fecha de alta
            $sql = "INSERT INTO RO_T_ENC_ECOMMERCE_HISTORIAL_FALT (NRO_PEDIDO, NRO_ORDEN, ESTADO, FECHA_PEDIDO, FECHA_ALTA, FECHA_ULT_MODIF) 
                    VALUES ('$nro_pedido', $nroOrdenSQL, '$estado', GETDATE(), GETDATE(), GETDATE())";
        }
        
        $result = sqlsrv_query($cid_central, $sql) or die(exit("Error en sqlsrv_query: " . print_r(sqlsrv_errors(), true)));
        return $result;
    }

    public function guardarHistorialReclamoConUpsert($data) {
        $cid = new Conexion();
        $cid_central = $cid->conectarSql('central');

        // Establecer formato de fecha
        sqlsrv_query($cid_central, "SET DATEFORMAT YMD");

        // Verificar si ya existe un registro para este pedido
        $nro_pedido = $data['nro_pedido'];
        $sqlCheck = "SELECT COUNT(*) as count FROM RO_T_ENC_ECOMMERCE_HISTORIAL_FALT WHERE NRO_PEDIDO = '$nro_pedido'";
        $resultCheck = sqlsrv_query($cid_central, $sqlCheck);
        $row = sqlsrv_fetch_array($resultCheck, SQLSRV_FETCH_ASSOC);
        
        // Mapping correcto de campos:
        $nroOrden = !empty($data['nroOrden']) ? $data['nroOrden'] : '';
        $cliente = !empty($data['cliente']) ? $data['cliente'] : '';
        $warehouse = !empty($data['prepara']) ? $data['prepara'] : ''; // Viene del campo "Prepara"
        $modalCodigo = !empty($data['modalCodigo']) ? $data['modalCodigo'] : '';
        $modalCantidad = !empty($data['modalCantidad']) ? $data['modalCantidad'] : '1';
        $sucDespacho = !empty($data['sucursal']) ? $data['sucursal'] : ''; // Sucursal seleccionada en modal
        $resolucion = !empty($data['resolucion']) ? strtolower(trim($data['resolucion'])) : '';
        $estado = !empty($data['estado']) ? $data['estado'] : '';
        
        // CORRECCIÓN: Parsear y formatear la fecha correctamente
        $fechaPedido = null;
        if (!empty($data['fechaHora'])) {
            // Intentar parsear diferentes formatos de fecha
            $fechaTexto = trim($data['fechaHora']);
            
            // Formato: "31/10/2025 10:30:45" o "31/10/2025"
            if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})/', $fechaTexto, $matches)) {
                $fechaPedido = $matches[3] . '-' . $matches[2] . '-' . $matches[1]; // Convertir a YYYY-MM-DD
            } 
            // Formato: "2025-10-31 10:30:45" o "2025-10-31"
            else if (preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $fechaTexto)) {
                $fechaPedido = substr($fechaTexto, 0, 10); // Extraer solo la fecha
            }
            // Si no coincide con ningún formato, usar strtotime
            else {
                $timestamp = strtotime($fechaTexto);
                if ($timestamp !== false) {
                    $fechaPedido = date('Y-m-d', $timestamp);
                }
            }
        }
        
        // Si no se pudo parsear la fecha, usar GETDATE()
        $fechaPedidoSQL = $fechaPedido ? "'$fechaPedido'" : "GETDATE()";
        
        if ($row['count'] > 0) {
            // UPDATE - Actualizar registro existente
            $sql = "UPDATE RO_T_ENC_ECOMMERCE_HISTORIAL_FALT SET 
                    NRO_ORDEN = '$nroOrden',
                    CLIENTE = '$cliente',
                    WAREHOUSE = '$warehouse',
                    COD_ARTICULO = '$modalCodigo',
                    CANTIDAD = '$modalCantidad',
                    SUC_DESPACHO = '$sucDespacho',
                    ESTADO = '$estado',
                    RESOLUCION = '$resolucion',
                    COD_ARTICULO_CAMBIO = '" . ($data['articulo'] ?? '') . "',
                    DESCRIPCION = '" . ($data['descripcion'] ?? '') . "',
                    FECHA_ULT_MODIF = GETDATE()
                    WHERE NRO_PEDIDO = '$nro_pedido'";
        } else {
            // INSERT - Crear nuevo registro
            $sql = "SET DATEFORMAT YMD;
                    INSERT INTO RO_T_ENC_ECOMMERCE_HISTORIAL_FALT (
                    FECHA_PEDIDO, NRO_ORDEN, NRO_PEDIDO, CLIENTE, WAREHOUSE, 
                    COD_ARTICULO_CAMBIO, DESCRIPCION, CANTIDAD, ESTADO, RESOLUCION, 
                    SUC_DESPACHO, COD_ARTICULO, FECHA_ALTA, FECHA_ULT_MODIF
                    ) VALUES (
                    $fechaPedidoSQL, 
                    '$nroOrden', 
                    '$nro_pedido', 
                    '$cliente', 
                    '$warehouse', 
                    '" . ($data['articulo'] ?? '') . "', 
                    '" . ($data['descripcion'] ?? '') . "', 
                    '$modalCantidad', 
                    '$estado', 
                    '$resolucion', 
                    '$sucDespacho', 
                    '$modalCodigo',
                    GETDATE(),
                    GETDATE()
                    )";
        }
        
        $result = sqlsrv_query($cid_central, $sql) or die(exit("Error en sqlsrv_query: " . print_r(sqlsrv_errors(), true)));
        
        // NUEVA FUNCIONALIDAD: Si la resolución es "Completado" o "Cambio", marcar el pedido como Controlado
        if ($result && in_array($resolucion, ['completado', 'cambio'])) {
            $this->marcarPedidoComoControlado($nro_pedido, $nroOrden);
        }
        
        // NUEVA FUNCIONALIDAD: Si la resolución es "Cancelado", marcar el pedido como cancelado en la tabla de estados
        if ($result && $resolucion === 'cancelado') {
            $this->marcarPedidoCancelado($nro_pedido, $nroOrden);
        }
        
        return $result;
    }

    /**
     * Marca un pedido como controlado en la tabla RO_T_ESTADO_PEDIDOS_ECOMMERCE
     * Se ejecuta automáticamente cuando se completa o cambia un artículo
     */
    public function marcarPedidoComoControlado($nro_pedido, $nro_orden = null) {
        $cid = new Conexion();
        $cid_central = $cid->conectarSql('central');
        
        // Actualizar el estado del pedido como controlado
        $sql = "UPDATE RO_T_ESTADO_PEDIDOS_ECOMMERCE 
                SET CONTROLADO = 1, FECHA_CONTROLADO = GETDATE(), FECHA_ULT_MODIF = GETDATE()
                WHERE NRO_PEDIDO = '$nro_pedido'";
        
        // Si hay ORDER_ID, agregar la condición
        if ($nro_orden) {
            $sql .= " AND ORDER_ID = '$nro_orden'";
        }
        
        $result = sqlsrv_query($cid_central, $sql);
        
        if ($result === false) {
            error_log("Error al marcar pedido como controlado: " . print_r(sqlsrv_errors(), true));
        }
        
        return $result;
    }

    /**
     * Marca un pedido como cancelado en la tabla RO_T_ESTADO_PEDIDOS_ECOMMERCE
     * Se ejecuta automáticamente cuando la resolución es "Cancelado"
     */
    public function marcarPedidoCancelado($nro_pedido, $nro_orden = null) {
        $cid = new Conexion();
        $cid_central = $cid->conectarSql('central');
        
        // Actualizar el estado del pedido como cancelado
        $sql = "UPDATE RO_T_ESTADO_PEDIDOS_ECOMMERCE 
                SET CANCELADO = 1, FECHA_ULT_MODIF = GETDATE()
                WHERE NRO_PEDIDO = '$nro_pedido'";
        
        // Si hay ORDER_ID, agregar la condición
        if ($nro_orden) {
            $sql .= " AND ORDER_ID = '$nro_orden'";
        }
        
        $result = sqlsrv_query($cid_central, $sql);
        
        if ($result === false) {
            error_log("Error al marcar pedido como cancelado: " . print_r(sqlsrv_errors(), true));
        }
        
        return $result;
    }

    /**
     * Obtiene el historial completo de incidentes por faltantes, con filtros.
     * Este es el nuevo método para el dashboard de reportes.
     */
// Reemplaza esta función completa en Class/Pedido.php

public function getHistorialFaltantesCompleto($fechaInicio, $fechaFin, $warehouse = '', $estado = '', $resolucion = '')
{
    $cid = new Conexion();
    $cid_central = $cid->conectarSql("central");

    $sqlSetFormat = "SET DATEFORMAT YMD";
    sqlsrv_query($cid_central, $sqlSetFormat);

    if (!empty($fechaInicio)) {
        $fechaInicio = date('Y-m-d', strtotime($fechaInicio));
    }
    if (!empty($fechaFin)) {
        $fechaFin = date('Y-m-d', strtotime($fechaFin));
    }

    $sql = "
        SET DATEFORMAT YMD;
        
        WITH IncidentesAuditoria AS (
            SELECT 
                A.NRO_ORDEN_ECOMMERCE,
                -- CORREGIDO: Obtenemos solo los artículos que ACTUALMENTE tienen faltante
                -- (donde la cantidad auditada es menor que la cantidad a facturar)
                -- Usamos STRING_AGG para concatenar múltiples artículos faltantes si los hay
                STRING_AGG(A.COD_ARTICULO, ', ') AS ARTICULO_AUDITADO
            FROM SOF_AUDITORIA A
            WHERE 
                A.FECHA_AUDITORIA_1 IS NOT NULL 
                AND A.COD_ARTICULO LIKE '[XO]%'
                AND CAST(A.FECHA_PEDIDO AS DATE) BETWEEN ? AND ?
                -- CLAVE: Solo incluir artículos donde actualmente hay faltante
                AND CAST(A.CANT_AUDITADO AS FLOAT) < CAST(A.CANTIDAD_A_FACTURAR AS FLOAT)
            GROUP BY 
                A.NRO_ORDEN_ECOMMERCE
            HAVING 
                -- Verificar que la suma total también tenga discrepancia
                SUM(CAST(A.CANTIDAD_A_FACTURAR AS FLOAT)) <> SUM(CAST(A.CANT_AUDITADO AS FLOAT))
        )
        SELECT 
            ISNULL(H.ESTADO, 'abierto') as ESTADO,
            ISNULL(H.RESOLUCION, 'Pendiente') as RESOLUCION,
            ISNULL(H.CLIENTE, GVA38.RAZON_SOCI) as CLIENTE,
            ISNULL(H.WAREHOUSE, H.SUC_DESPACHO) as WAREHOUSE_RECLAMO,
            H.FECHA_ULT_MODIF,
            H.FECHA_ALTA,
            
            GVA21.NRO_PEDIDO,
            GVA21.ORDER_ID_TIENDA AS NRO_ORDEN,
            CAST(GVA21.FECHA_PEDI AS DATE) AS FECHA_PEDIDO,
            GVA21.COD_SUCURS AS DEPOSITO_ORIGEN,
            COALESCE(V_STA22.SUCURSAL_ENTREGA, CASE WHEN GVA21.COD_SUCURS = '01' THEN 'CENTRAL' ELSE 'No especificado' END) AS NOMBRE_ORIGEN,

            -- LÓGICA CORREGIDA PARA ARTÍCULO ORIGINAL:
            -- 1. Si se cargó manualmente un cambio (H.COD_ARTICULO_CAMBIO), usa ese.
            -- 2. Si no, usa el/los artículo(s) detectado(s) con faltante actual en la auditoría (IA.ARTICULO_AUDITADO).
            -- 3. Si falla todo, pone N/A.
            COALESCE(H.COD_ARTICULO_CAMBIO, IA.ARTICULO_AUDITADO, 'N/A') as ARTICULO_ORIGINAL,

            ISNULL(H.COD_ARTICULO, 'Discrepancia General') as COD_ARTICULO
            
        FROM IncidentesAuditoria IA
        INNER JOIN GVA21 ON IA.NRO_ORDEN_ECOMMERCE = GVA21.ORDER_ID_TIENDA COLLATE DATABASE_DEFAULT
        LEFT JOIN GVA38 ON GVA21.NRO_PEDIDO = GVA38.N_COMP AND GVA21.TALON_PED = GVA38.TALONARIO
        LEFT JOIN RO_T_ENC_ECOMMERCE_HISTORIAL_FALT H ON GVA21.ORDER_ID_TIENDA = H.NRO_ORDEN COLLATE DATABASE_DEFAULT
        LEFT JOIN RO_V_STA22 V_STA22 ON GVA21.COD_SUCURS = V_STA22.COD_SUCURS COLLATE DATABASE_DEFAULT
    ";
    
    $params = array($fechaInicio, $fechaFin);

    $whereConditions = [];
    if (!empty($estado)) {
        $whereConditions[] = "ISNULL(H.ESTADO, 'abierto') = ?";
        array_push($params, $estado);
    }
    
    if (!empty($whereConditions)) {
        $sql .= " WHERE " . implode(' AND ', $whereConditions);
    }

    $sql .= " ORDER BY GVA21.FECHA_PEDI DESC;";

    $stmt = sqlsrv_query($cid_central, $sql, $params);

    if ($stmt === false) {
        return [];
    }

    $data = [];
    while ($v = sqlsrv_fetch_object($stmt)) {
        $data[] = array($v);
    }

    return $data;
}

    /**
     * Obtener devoluciones/reintegros de un pedido desde RO_T_ESTADO_PEDIDOS_ECOMMERCE
     * @param string $nroPedido Número de pedido
     * @param string $nroOrden Número de orden (opcional)
     * @return array Array con los datos de devoluciones
     */
    public function obtenerDevoluciones($nroPedido, $nroOrden = null) {
        $cid = new Conexion();
        $cid_central = $cid->conectarSql('central');
        
        // Consulta simplificada - RO_T_ESTADO_PEDIDOS_ECOMMERCE solo tiene estado del pedido
        $sql = "
        SET DATEFORMAT YMD;
        SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;
        
        SELECT 
            'REINTEGRO_ECOMMERCE' as TIPO,
            CAST(EP.FECHA_PEDI AS DATE) as FECHA,
            ISNULL(EP.NCR, 'PENDIENTE') as NUMERO,
            'PEDIDO_COMPLETO' as COD_ARTICU,
            'PEDIDO_COMPLETO' as COD_ARTICU_BASE,
            1 as CANTIDAD,
            0 as IMPORTE,
            CASE 
                WHEN EP.REINTEGRADO = 1 AND (EP.NCR IS NULL OR EP.NCR = '') THEN 'NCR_PENDIENTE'
                WHEN EP.REINTEGRADO = 1 AND EP.NCR IS NOT NULL AND EP.NCR <> '' THEN 'NCR_EMITIDA'
                ELSE 'NORMAL'
            END as ESTADO,
            'Pedido con reintegro solicitado' as DESCRIPCIO,
            EP.NRO_PEDIDO as DEBUG_NRO_PEDIDO,
            EP.ORDER_ID as DEBUG_ORDER_ID,
            EP.REINTEGRADO as DEBUG_REINTEGRADO,
            EP.FACTURA as FACTURA
        FROM RO_T_ESTADO_PEDIDOS_ECOMMERCE EP
        WHERE RTRIM(LTRIM(EP.NRO_PEDIDO)) = ?
        AND EP.REINTEGRADO = 1
        ";
        
        $params = array($nroPedido);
        $stmt = sqlsrv_query($cid_central, $sql, $params);
        
        if ($stmt === false) {
            return [];
        }
        
        $data = [];
        while ($v = sqlsrv_fetch_object($stmt)) {
            $data[] = $v;
        }
        
        return $data;
    }
    
    /**
     * Verificar si un pedido está cancelado y si es total o parcial
     * @param string $nroPedido Número de pedido
     * @param string $nroOrden Número de orden (opcional)
     * @return object|null Objeto con información de cancelación
     */
    public function verificarCancelacion($nroPedido, $nroOrden = null) {
        $cid = new Conexion();
        $cid_central = $cid->conectarSql('central');
        
        // Verificar estado de cancelación/reintegro
        $sql = "SELECT 
                    EP.REINTEGRADO,
                    EP.NCR,
                    EP.FECHA_NCR,
                    EP.FACTURA,
                    EP.CANCELADO,
                    EP.FECHA_PEDI
                FROM RO_T_ESTADO_PEDIDOS_ECOMMERCE EP
                WHERE RTRIM(LTRIM(EP.NRO_PEDIDO)) = ?";
        
        $params = array($nroPedido);
        $stmt = sqlsrv_query($cid_central, $sql, $params);
        
        if ($stmt === false || !sqlsrv_has_rows($stmt)) {
            return null;
        }
        
        $estadoPedido = sqlsrv_fetch_object($stmt);
        
        if ($estadoPedido->REINTEGRADO != 1) {
            return null;
        }
        
        // Obtener detalle del pedido para verificar si es cancelación parcial
        $sqlDetalle = "SELECT 
                        COUNT(*) as TOTAL_ARTICULOS,
                        SUM(CAST(DP.CANT_PEDID as INT)) as TOTAL_CANTIDAD
                    FROM RO_DPEDI01 DP
                    WHERE RTRIM(LTRIM(DP.NRO_PEDIDO)) = ?";
        
        $stmtDetalle = sqlsrv_query($cid_central, $sqlDetalle, $params);
        $detallePedido = $stmtDetalle ? sqlsrv_fetch_object($stmtDetalle) : null;
        
        return (object) [
            'esta_cancelado' => true,
            'tiene_ncr' => !empty($estadoPedido->NCR),
            'numero_ncr' => $estadoPedido->NCR ?? null,
            'fecha_ncr' => $estadoPedido->FECHA_NCR ?? null,
            'factura' => $estadoPedido->FACTURA ?? null,
            'es_parcial' => false, // Por ahora, necesitaríamos más datos para determinarlo
            'total_articulos' => $detallePedido ? $detallePedido->TOTAL_ARTICULOS : 0,
            'total_cantidad' => $detallePedido ? $detallePedido->TOTAL_CANTIDAD : 0
        ];
    }
    
    /**
     * Obtener resumen de devoluciones agrupadas por tipo
     * @param string $nroPedido Número de pedido
     * @param string $nroOrden Número de orden (opcional)
     * @return object Objeto con el resumen de devoluciones
     */
    public function obtenerResumenDevoluciones($nroPedido, $nroOrden = null) {
        $devoluciones = $this->obtenerDevoluciones($nroPedido, $nroOrden);
        
        $resumen = (object)[
            'total_articulos' => 0,
            'total_importe' => 0,
            'tiene_devoluciones' => false,
            'tiene_ncr_pendiente' => false,
            'tipos' => []
        ];
        
        foreach ($devoluciones as $dev) {
            $resumen->tiene_devoluciones = true;
            $resumen->total_articulos += $dev->CANTIDAD;
            $resumen->total_importe += ($dev->CANTIDAD * $dev->IMPORTE);
            
            // Detectar si hay NCR pendiente
            if (in_array($dev->ESTADO, ['NCR_PENDIENTE', 'PENDIENTE'])) {
                $resumen->tiene_ncr_pendiente = true;
            }
            
            // Agrupar por tipo más legible
            $tipoDisplay = $dev->TIPO;
            if ($dev->TIPO == 'REINTEGRO_ECOMMERCE') {
                if ($dev->ESTADO == 'NCR_PENDIENTE') {
                    $tipoDisplay = 'REINTEGRO (NCR Pendiente)';
                } else {
                    $tipoDisplay = 'REINTEGRO';
                }
            } else if ($dev->TIPO == 'REFUND_VTEX') {
                $tipoDisplay = 'REINTEGRO VTEX';
            }
            
            if (!isset($resumen->tipos[$tipoDisplay])) {
                $resumen->tipos[$tipoDisplay] = (object)[
                    'cantidad' => 0,
                    'importe' => 0,
                    'registros' => []
                ];
            }
            
            $resumen->tipos[$tipoDisplay]->cantidad += $dev->CANTIDAD;
            $resumen->tipos[$tipoDisplay]->importe += ($dev->CANTIDAD * $dev->IMPORTE);
            $resumen->tipos[$tipoDisplay]->registros[] = $dev;
        }
        
        return $resumen;
    }
    
    /**
     * Verificar si hay artículos con reintegro pero sin NCR emitida
     * @param string $nroPedido Número de pedido
     * @param string $nroOrden Número de orden (opcional)
     * @return array Array con artículos pendientes de NCR
     */
    public function verificarNcrPendiente($nroPedido, $nroOrden = null) {
        $devoluciones = $this->obtenerDevoluciones($nroPedido, $nroOrden);
        
        $pendientes = [];
        
        // Buscar cualquier devolución con estado NCR_PENDIENTE
        foreach ($devoluciones as $dev) {
            if ($dev->ESTADO == 'NCR_PENDIENTE') {
                $pendientes[] = (object)[
                    'COD_ARTICU_BASE' => $dev->COD_ARTICU_BASE,
                    'DESCRIPCIO' => $dev->DESCRIPCIO,
                    'CANTIDAD_REFUND' => $dev->CANTIDAD,
                    'CANTIDAD_NCR' => 0,
                    'CANTIDAD_PENDIENTE' => $dev->CANTIDAD,
                    'FECHA' => $dev->FECHA,
                    'NUMERO' => $dev->NUMERO,
                    'FACTURA' => isset($dev->FACTURA) ? $dev->FACTURA : ''
                ];
            }
        }
        
        return $pendientes;
    }
}
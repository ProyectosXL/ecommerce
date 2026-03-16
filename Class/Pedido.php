<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/ecommerce/Class/Conexion.php';

class Pedido
{

    private function getDatos($sql)
    {
        $cid = new Conexion();
        $cid_central = $cid->conectarSql('central');

        ini_set('max_execution_time', 300);
        $result = sqlsrv_query($cid_central, $sql);
        if ($result === false) {
            error_log('sqlsrv_query error: ' . print_r(sqlsrv_errors(), true));
            return [];
        }

        $data = [];
        while ($v = sqlsrv_fetch_object($result)) {
            $data[] = array($v);
        }
        return $data;
    }

    public function traerPedidos($desde, $hasta, $tienda, $warehouse, $estado = null, $orden = '%', $pagina = 1, $porPagina = 100, $metodoEnvio = '')
    {

        // Manejar el estado: si es null, pasar NULL sin comillas al SQL
        $estadoSQL = ($estado === null || $estado === '') ? 'NULL' : "'$estado'";
        $metodoEnvioSQL = ($metodoEnvio === null || $metodoEnvio === '') ? 'NULL' : "'$metodoEnvio'";
        $paginaInt = max(1, intval($pagina));
        $porPaginaInt = max(1, intval($porPagina));

        $sql = "
        SET DATEFORMAT YMD;
        SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;
        EXEC RO_ECOMMERCE_PEDIDOS '$desde', '$hasta', '$tienda', '$warehouse', $estadoSQL, '$orden', $paginaInt, $porPaginaInt, $metodoEnvioSQL
        ";

        $array = $this->getDatos($sql);

        return $array;
    }

    public function traerWarehouse($pais = 'AR')
    {
        if (strtoupper($pais) === 'UY') {
            return $this->traerWarehouseUY();
        }

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

    public function traerMetodosEnvio()
    {
        $sql = "SELECT DISTINCT METODO_ENVIO 
                FROM RO_V_WAREHOUSE_METODO_ENVIO_VTEX 
                WHERE METODO_ENVIO IS NOT NULL AND METODO_ENVIO <> ''
                ORDER BY METODO_ENVIO";
        $array = $this->getDatos($sql);
        return $array;
    }

    public function buscarPedido($desde, $hasta, $orden, $pais = 'AR')
    {
        if (strtoupper($pais) === 'UY') {
            return $this->buscarPedidoUY($desde, $hasta, $orden);
        }

        $sql = "
        SET DATEFORMAT YMD
        EXEC RO_SP_ECOMMERCE_PEDIDOS_FLUJO '$desde', '$hasta', '$orden'
        ";
        $array = $this->getDatos($sql);
        return $array;
    }

    public function buscarDetallePedido($desde, $hasta, $orden, $pais = 'AR')
    {
        if (strtoupper($pais) === 'UY') {
            // Uruguay: el SP de búsqueda ya trae el detalle incluido
            return $this->buscarPedidoUY($desde, $hasta, $orden);
        }

        $sql = "
        SET DATEFORMAT YMD
        EXEC RO_SP_ECOMMERCE_PEDIDOS_FLUJO_DETALLE '$desde', '$hasta', '$orden'
        ";

        $array = $this->getDatos($sql);
        return $array;
    }

    public function buscarStockArticulo($sucursal, $pais = 'AR')
    {
        if (strtoupper($pais) === 'UY') {
            return $this->buscarStockArticuloUY($sucursal);
        }

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

    public function guardarHistorialReclamo($data)
    {
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
                VALUES ($fechaPedidoSQL, '" . $data['nroOrden'] . "', '" . $data['nro_pedido'] . "', '" . $data['cliente'] . "', '" . $data['sucursal'] . "', '" . $data['articulo'] . "', '" . $data['descripcion'] . "', '" . $data['modalCantidad'] . "', '" . $data['estado'] . "', '" . $data['resolucion'] . "', '" . $data['sucursal'] . "', '" . $data['modalCodigo'] . "')
        ";

        $result = sqlsrv_query($cid_central, $sql) or die(exit("Error en sqlsrv_query: " . print_r(sqlsrv_errors(), true)));
        return $result;
    }

    public function traerHistorialReclamo($nro_pedido)
    {
        $cid = new Conexion();
        $cid_central = $cid->conectarSql('central');
        $sql = "SELECT * FROM RO_T_ENC_ECOMMERCE_HISTORIAL_FALT WHERE NRO_PEDIDO = '$nro_pedido'";

        $result = sqlsrv_query($cid_central, $sql) or die(exit("Error en sqlsrv_query"));

        $data = [];
        while ($v = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
            $data[] = array($v);
        }
        ;
        if (count($data) == 0) {
            return false;
        }
        return $data[0];
    }

    public function guardarReclamoDetalle($stringValues)
    {
        $cid = new Conexion();
        $cid_central = $cid->conectarSql('central');
        $sql = "INSERT INTO RO_T_DET_ECOMMERCE_HISTORIAL_FALT (NRO_PEDIDO, COMENTARIOS, TIPO_CONTACTO, AGENTE, FECHA_PEDIDO) VALUES $stringValues";

        $result = sqlsrv_query($cid_central, $sql) or die(exit("Error en sqlsrv_query"));
        return $result;
    }

    public function listarReclamoDetalle($nro_pedido)
    {
        $cid = new Conexion();
        $cid_central = $cid->conectarSql('central');
        $sql = "SELECT * FROM RO_T_DET_ECOMMERCE_HISTORIAL_FALT WHERE NRO_PEDIDO = '$nro_pedido'";

        $result = sqlsrv_query($cid_central, $sql) or die(exit("Error en sqlsrv_query"));

        $data = [];
        while ($v = sqlsrv_fetch_object($result)) {
            $data[] = array($v);
        }
        ;
        return $data;
    }

    public function consultarEstado($nroOrden)
    {
        $cid = new Conexion();
        $cid_central = $cid->conectarSql('central');
        $sql = "SELECT ESTADO FROM RO_T_ENC_ECOMMERCE_HISTORIAL_FALT WHERE NRO_ORDEN = '$nroOrden'";

        $result = sqlsrv_query($cid_central, $sql) or die(exit("Error en sqlsrv_query"));

        $data = '';
        while ($v = sqlsrv_fetch_object($result)) {
            // retorna solo el estado 
            $data = $v->ESTADO;
        }
        ;
        return $data;
    }

    public function actualizarEstadoReclamo($nro_pedido, $estado, $nro_orden = null)
    {
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

    public function guardarHistorialReclamoConUpsert($data)
    {
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
    public function marcarPedidoComoControlado($nro_pedido, $nro_orden = null)
    {
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
    public function marcarPedidoCancelado($nro_pedido, $nro_orden = null)
    {
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

    public function getHistorialFaltantesCompletoAR($fechaInicio, $fechaFin, $warehouse = '', $estado = '', $resolucion = '')
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

            ISNULL(H.COD_ARTICULO, 'Discrepancia General') as COD_ARTICULO,
            
            -- NUEVOS CAMPOS SOLICITADOS:
            EP.FECHA_INCOMPLETO,
            COALESCE(CTA.DESC_CTA_ARTICULO, CTA2.DESC_CTA_ARTICULO) as DESCRIPCION_ARTICULO,
            COALESCE(RUBRO1.RUBRO, RUBRO2.RUBRO) as RUBRO
            
        FROM IncidentesAuditoria IA
        INNER JOIN GVA21 ON IA.NRO_ORDEN_ECOMMERCE = GVA21.ORDER_ID_TIENDA COLLATE DATABASE_DEFAULT
        LEFT JOIN GVA38 ON GVA21.NRO_PEDIDO = GVA38.N_COMP AND GVA21.TALON_PED = GVA38.TALONARIO
        LEFT JOIN RO_T_ENC_ECOMMERCE_HISTORIAL_FALT H ON GVA21.ORDER_ID_TIENDA = H.NRO_ORDEN COLLATE DATABASE_DEFAULT
        LEFT JOIN RO_V_STA22 V_STA22 ON GVA21.COD_SUCURS = V_STA22.COD_SUCURS COLLATE DATABASE_DEFAULT
        LEFT JOIN RO_T_ESTADO_PEDIDOS_ECOMMERCE EP ON GVA21.NRO_PEDIDO = EP.NRO_PEDIDO
        LEFT JOIN [LAKERBIS].[LOCALES_LAKERS].DBO.CTA_ARTICULO CTA ON H.COD_ARTICULO_CAMBIO = CTA.COD_ARTICULO
        -- Intento secundario: buscar por el primer artículo de la auditoría si COD_ARTICULO_CAMBIO no tiene match
        LEFT JOIN [LAKERBIS].[LOCALES_LAKERS].DBO.CTA_ARTICULO CTA2 ON 
            SUBSTRING(IA.ARTICULO_AUDITADO, 1, CASE WHEN CHARINDEX(',', IA.ARTICULO_AUDITADO) > 0 
                                                     THEN CHARINDEX(',', IA.ARTICULO_AUDITADO) - 1 
                                                     ELSE LEN(IA.ARTICULO_AUDITADO) END) = CTA2.COD_ARTICULO
        -- Buscar rubro del artículo
        LEFT JOIN SOF_MAESTRO_ARTICULOS_RUBRO_CATEGORIA RUBRO1 ON H.COD_ARTICULO_CAMBIO = RUBRO1.COD_ARTICU
        LEFT JOIN SOF_MAESTRO_ARTICULOS_RUBRO_CATEGORIA RUBRO2 ON 
            SUBSTRING(IA.ARTICULO_AUDITADO, 1, CASE WHEN CHARINDEX(',', IA.ARTICULO_AUDITADO) > 0 
                                                     THEN CHARINDEX(',', IA.ARTICULO_AUDITADO) - 1 
                                                     ELSE LEN(IA.ARTICULO_AUDITADO) END) = RUBRO2.COD_ARTICU
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
    public function obtenerDevoluciones($nroPedido, $nroOrden = null, $pais = 'AR')
    {
        if (strtoupper($pais) === 'UY') {
            return $this->obtenerDevolucionesUY($nroPedido, $nroOrden);
        }

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
    public function verificarCancelacion($nroPedido, $nroOrden = null, $pais = 'AR')
    {
        if (strtoupper($pais) === 'UY') {
            return $this->verificarCancelacionUY($nroPedido, $nroOrden);
        }

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
    public function obtenerResumenDevoluciones($nroPedido, $nroOrden = null)
    {
        $devoluciones = $this->obtenerDevoluciones($nroPedido, $nroOrden);

        $resumen = (object) [
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
                $resumen->tipos[$tipoDisplay] = (object) [
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
    public function verificarNcrPendiente($nroPedido, $nroOrden = null)
    {
        $devoluciones = $this->obtenerDevoluciones($nroPedido, $nroOrden);

        $pendientes = [];

        // Buscar cualquier devolución con estado NCR_PENDIENTE
        foreach ($devoluciones as $dev) {
            if ($dev->ESTADO == 'NCR_PENDIENTE') {
                $pendientes[] = (object) [
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

    // ===== MÉTODOS PARA URUGUAY =====

    /**
     * Obtiene historial de faltantes/incidentes para Uruguay (TASKY_SA)
     * IMPORTANTE: Usa la misma lógica que Argentina pero con tablas de Uruguay
     */
    public function getHistorialFaltantesCompletoUY($fechaInicio, $fechaFin, $warehouse = '', $estado = '', $resolucion = '')
    {
        try {
            $cid = new Conexion();
            $cid_uruguay = $cid->conectarSql("uy");

            if (!$cid_uruguay) {
                error_log("Error: No se pudo conectar a la base de datos de Uruguay");
                return [];
            }

            $sqlSetFormat = "SET DATEFORMAT YMD";
            sqlsrv_query($cid_uruguay, $sqlSetFormat);

            if (!empty($fechaInicio)) {
                $fechaInicio = date('Y-m-d', strtotime($fechaInicio));
            }
            if (!empty($fechaFin)) {
                $fechaFin = date('Y-m-d', strtotime($fechaFin));
            }

            // Construir condiciones WHERE base
            $whereConditions = ["EP.INCOMPLETO = 1"];
            $whereConditions[] = "CAST(EP.FECHA_PEDI AS DATE) BETWEEN ? AND ?";
            $params = array($fechaInicio, $fechaFin);

            // Filtros adicionales
            if (!empty($warehouse)) {
                $whereConditions[] = "(H.WAREHOUSE LIKE ? OR GVA21.COD_SUCURS LIKE ?)";
                $params[] = "%$warehouse%";
                $params[] = "%$warehouse%";
            }

            if (!empty($estado)) {
                $whereConditions[] = "CASE 
                    WHEN H.NRO_PEDIDO IS NULL THEN 'abierto'
                    WHEN H.ESTADO = 'resuelto' THEN 'resuelto'
                    ELSE 'proceso'
                END = ?";
                $params[] = $estado;
            }

            if (!empty($resolucion)) {
                $whereConditions[] = "H.RESOLUCION = ?";
                $params[] = $resolucion;
            }

            $whereClause = implode(" AND ", $whereConditions);

            $sql = "
                SET DATEFORMAT YMD;
                
                SELECT 
                    EP.NRO_PEDIDO,
                    EP.ORDER_ID AS NRO_ORDEN,
                    CAST(EP.FECHA_PEDI AS DATE) AS FECHA_PEDIDO,
                    EP.FECHA_INCOMPLETO,
                    COALESCE(GVA38.RAZON_SOCI, '') AS CLIENTE,
                    COALESCE(H.COD_ARTICULO_CAMBIO, '') AS ARTICULO_ORIGINAL,
                    COALESCE(H.COD_ARTICULO, '') AS COD_ARTICULO,
                    COALESCE(STA11.DESCRIPCIO, '') AS DESCRIPCION_ARTICULO,
                    '' AS RUBRO,
                    COALESCE(H.WAREHOUSE, '') AS WAREHOUSE_RECLAMO,
                    COALESCE(GVA21.COD_SUCURS, '') AS DEPOSITO_ORIGEN,
                    '' AS NOMBRE_ORIGEN,
                    CASE 
                        WHEN H.NRO_PEDIDO IS NULL THEN 'abierto'
                        WHEN H.ESTADO = 'resuelto' THEN 'resuelto'
                        ELSE 'proceso'
                    END AS ESTADO,
                    COALESCE(H.RESOLUCION, '') AS RESOLUCION,
                    H.FECHA_ALTA,
                    H.FECHA_ULT_MODIF
                FROM RO_T_ESTADO_PEDIDOS_ECOMMERCE EP
                INNER JOIN GVA21 ON EP.NRO_PEDIDO = GVA21.NRO_PEDIDO COLLATE DATABASE_DEFAULT
                LEFT JOIN GVA38 ON GVA21.NRO_PEDIDO = GVA38.N_COMP COLLATE DATABASE_DEFAULT AND GVA21.TALON_PED = GVA38.TALONARIO
                LEFT JOIN FT_T_ENC_ECOMMERCE_HISTORIAL_FALT H ON EP.NRO_PEDIDO = H.NRO_PEDIDO COLLATE DATABASE_DEFAULT
                LEFT JOIN STA11 ON H.COD_ARTICULO = STA11.COD_ARTICU COLLATE DATABASE_DEFAULT
                WHERE $whereClause
                ORDER BY EP.FECHA_PEDI DESC, EP.NRO_PEDIDO DESC
            ";

            $stmt = sqlsrv_query($cid_uruguay, $sql, $params);

            if ($stmt === false) {
                $errors = sqlsrv_errors();
                error_log("❌ ERROR en query UY: " . print_r($errors, true));
                error_log("SQL completo: " . $sql);
                error_log("Params: " . print_r($params, true));
                return [];
            }

            $data = [];
            while ($v = sqlsrv_fetch_object($stmt)) {
                $data[] = array($v);
            }

            return $data;

        } catch (Exception $e) {
            error_log("Error en getHistorialFaltantesCompletoUY: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Método wrapper que decide qué método llamar según el país
     */
    public function getHistorialFaltantesCompletoPorPais($pais, $fechaInicio, $fechaFin, $warehouse = '', $estado = '', $resolucion = '')
    {
        if (strtoupper($pais) === 'UY') {
            return $this->getHistorialFaltantesCompletoUY($fechaInicio, $fechaFin, $warehouse, $estado, $resolucion);
        } else {
            return $this->getHistorialFaltantesCompletoAR($fechaInicio, $fechaFin, $warehouse, $estado, $resolucion);
        }
    }

    /**
     * Guarda detalle de reclamo para Uruguay
     */
    public function guardarReclamoDetalleUY($stringValues)
    {
        $cid = new Conexion();
        $cid_uruguay = $cid->conectarSql('uy');

        $sql = "INSERT INTO FT_T_DET_ECOMMERCE_HISTORIAL_FALT (NRO_PEDIDO, COMENTARIOS, TIPO_CONTACTO, AGENTE, FECHA_PEDIDO) 
                VALUES $stringValues";

        $result = sqlsrv_query($cid_uruguay, $sql) or die(exit("Error en sqlsrv_query UY: " . print_r(sqlsrv_errors(), true)));
        return $result;
    }

    /**
     * Guarda o actualiza historial de reclamo para Uruguay
     */
    public function guardarHistorialReclamoUY($data)
    {
        $cid = new Conexion();
        $cid_uruguay = $cid->conectarSql('uy');

        // Establecer formato de fecha
        sqlsrv_query($cid_uruguay, "SET DATEFORMAT YMD");

        // Parsear y formatear la fecha correctamente
        $fechaPedido = null;
        if (!empty($data['fechaHora'])) {
            $fechaTexto = trim($data['fechaHora']);

            if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})/', $fechaTexto, $matches)) {
                $fechaPedido = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
            } else if (preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $fechaTexto)) {
                $fechaPedido = substr($fechaTexto, 0, 10);
            } else {
                $timestamp = strtotime($fechaTexto);
                if ($timestamp !== false) {
                    $fechaPedido = date('Y-m-d', $timestamp);
                }
            }
        }

        $fechaPedidoSQL = $fechaPedido ? "'$fechaPedido'" : "GETDATE()";

        // Verificar si ya existe el reclamo
        $checkSql = "SELECT ID FROM FT_T_ENC_ECOMMERCE_HISTORIAL_FALT WHERE NRO_ORDEN = '{$data['nroOrden']}'";
        $checkResult = sqlsrv_query($cid_uruguay, $checkSql);

        if ($checkResult && $existing = sqlsrv_fetch_object($checkResult)) {
            // UPDATE
            $sql = "UPDATE FT_T_ENC_ECOMMERCE_HISTORIAL_FALT 
                    SET RESOLUCION = '{$data['resolucion']}',
                        SUC_DESPACHO = '{$data['sucursal']}',
                        COD_ARTICULO_CAMBIO = '{$data['articulo']}',
                        DESCRIPCION = '{$data['descripcion']}',
                        ESTADO = '{$data['estado']}',
                        FECHA_ULT_MODIF = GETDATE()
                    WHERE NRO_ORDEN = '{$data['nroOrden']}'";
        } else {
            // INSERT
            $sql = "INSERT INTO FT_T_ENC_ECOMMERCE_HISTORIAL_FALT 
                    (FECHA_PEDIDO, NRO_ORDEN, NRO_PEDIDO, CLIENTE, WAREHOUSE, COD_ARTICULO, DESCRIPCION, CANTIDAD, ESTADO, RESOLUCION, SUC_DESPACHO, COD_ARTICULO_CAMBIO, FECHA_ALTA, FECHA_ULT_MODIF)
                    VALUES (
                        $fechaPedidoSQL,
                        '{$data['nroOrden']}',
                        '{$data['nro_pedido']}',
                        '{$data['cliente']}',
                        '{$data['warehouse']}',
                        '{$data['modalCodigo']}',
                        '{$data['descripcion']}',
                        {$data['modalCantidad']},
                        '{$data['estado']}',
                        '{$data['resolucion']}',
                        '{$data['sucursal']}',
                        '{$data['articulo']}',
                        GETDATE(),
                        GETDATE()
                    )";
        }

        $result = sqlsrv_query($cid_uruguay, $sql) or die(exit("Error en sqlsrv_query UY: " . print_r(sqlsrv_errors(), true)));

        // NUEVA FUNCIONALIDAD: Si la resolución es "Completado" o "Cambio", marcar el pedido como Controlado
        if ($result && in_array(strtolower($data['resolucion']), ['completado', 'cambio'])) {
            $this->marcarPedidoComoControladoUY($data['nro_pedido'], $data['nroOrden']);
        }

        // NUEVA FUNCIONALIDAD: Si la resolución es "Cancelado", marcar el pedido como cancelado
        if ($result && strtolower($data['resolucion']) === 'cancelado') {
            $this->marcarPedidoCanceladoUY($data['nro_pedido'], $data['nroOrden']);
        }

        return $result;
    }

    /**
     * Actualiza estado de reclamo para Uruguay
     */
    public function actualizarEstadoReclamoUY($nro_pedido, $estado, $nro_orden = null)
    {
        $cid = new Conexion();
        $cid_uruguay = $cid->conectarSql('uy');

        // Verificar si existe un registro en la tabla de historial
        $sqlCheck = "SELECT COUNT(*) as count FROM FT_T_ENC_ECOMMERCE_HISTORIAL_FALT WHERE NRO_PEDIDO = '$nro_pedido'";
        $resultCheck = sqlsrv_query($cid_uruguay, $sqlCheck);
        $row = sqlsrv_fetch_array($resultCheck, SQLSRV_FETCH_ASSOC);

        $nroOrdenSQL = $nro_orden ? "'$nro_orden'" : "NULL";

        if ($row['count'] > 0) {
            $updateNroOrdenSQL = $nro_orden ? ", NRO_ORDEN = $nroOrdenSQL" : "";
            $sql = "UPDATE FT_T_ENC_ECOMMERCE_HISTORIAL_FALT 
                    SET ESTADO = '$estado', FECHA_ULT_MODIF = GETDATE() $updateNroOrdenSQL
                    WHERE NRO_PEDIDO = '$nro_pedido' AND (NRO_ORDEN IS NULL OR NRO_ORDEN = '')";
        } else {
            $sql = "INSERT INTO FT_T_ENC_ECOMMERCE_HISTORIAL_FALT (NRO_PEDIDO, NRO_ORDEN, ESTADO, FECHA_PEDIDO, FECHA_ALTA, FECHA_ULT_MODIF) 
                    VALUES ('$nro_pedido', $nroOrdenSQL, '$estado', GETDATE(), GETDATE(), GETDATE())";
        }

        $result = sqlsrv_query($cid_uruguay, $sql) or die(exit("Error en sqlsrv_query UY: " . print_r(sqlsrv_errors(), true)));
        return $result;
    }

    /**
     * Lista detalle de reclamos para Uruguay
     */
    public function listarReclamoDetalleUY($nro_pedido)
    {
        $cid = new Conexion();
        $cid_uruguay = $cid->conectarSql('uy');
        $sql = "SELECT * FROM FT_T_DET_ECOMMERCE_HISTORIAL_FALT WHERE NRO_PEDIDO = '$nro_pedido'";

        $result = sqlsrv_query($cid_uruguay, $sql) or die(exit("Error en sqlsrv_query UY"));

        $data = [];
        while ($v = sqlsrv_fetch_object($result)) {
            $data[] = array($v);
        }
        return $data;
    }

    /**
     * Obtiene historial completo de un reclamo en Uruguay
     */
    public function traerHistorialReclamoUY($nro_pedido)
    {
        $cid = new Conexion();
        $cid_uruguay = $cid->conectarSql('uy');
        $sql = "SELECT * FROM FT_T_ENC_ECOMMERCE_HISTORIAL_FALT WHERE NRO_PEDIDO = '$nro_pedido'";

        $result = sqlsrv_query($cid_uruguay, $sql) or die(exit("Error en sqlsrv_query UY"));

        $data = [];
        while ($v = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
            $data[] = array($v);
        }
        if (count($data) == 0) {
            return false;
        }
        return $data[0];
    }

    /**
     * Marca un pedido como controlado en Uruguay (RO_T_ESTADO_PEDIDOS_ECOMMERCE)
     * Se ejecuta automáticamente cuando se completa o cambia un artículo
     */
    public function marcarPedidoComoControladoUY($nro_pedido, $nro_orden = null)
    {
        $cid = new Conexion();
        $cid_uruguay = $cid->conectarSql('uy');

        // NOTA: CONTROLADO es varchar en UY, usar '1' como string
        $sql = "UPDATE RO_T_ESTADO_PEDIDOS_ECOMMERCE 
                SET CONTROLADO = '1', FECHA_CONTROLADO = GETDATE(), ULT_ACTUALIZACION = GETDATE()
                WHERE NRO_PEDIDO = '$nro_pedido'";

        // Si hay ORDER_ID, agregar la condición
        if ($nro_orden) {
            $sql .= " AND ORDER_ID = '$nro_orden'";
        }

        $result = sqlsrv_query($cid_uruguay, $sql);

        if ($result === false) {
            error_log("Error al marcar pedido UY como controlado: " . print_r(sqlsrv_errors(), true));
        }

        return $result;
    }

    /**
     * Marca un pedido como cancelado en Uruguay (RO_T_ESTADO_PEDIDOS_ECOMMERCE)
     * Se ejecuta automáticamente cuando la resolución es "Cancelado"
     */
    public function marcarPedidoCanceladoUY($nro_pedido, $nro_orden = null)
    {
        $cid = new Conexion();
        $cid_uruguay = $cid->conectarSql('uy');

        // Actualizar el estado del pedido como cancelado
        $sql = "UPDATE RO_T_ESTADO_PEDIDOS_ECOMMERCE 
                SET CANCELADO = 1, ULT_ACTUALIZACION = GETDATE()
                WHERE NRO_PEDIDO = '$nro_pedido'";

        // Si hay ORDER_ID, agregar la condición
        if ($nro_orden) {
            $sql .= " AND ORDER_ID = '$nro_orden'";
        }

        $result = sqlsrv_query($cid_uruguay, $sql);

        if ($result === false) {
            error_log("Error al marcar pedido UY como cancelado: " . print_r(sqlsrv_errors(), true));
        }

        return $result;
    }

    // ===== METODOS NUEVOS PARA SEGUIMIENTO DE PEDIDOS - URUGUAY =====

    /**
     * Busca pedidos en Uruguay usando SP especifico
     */
    public function buscarPedidoUY($desde, $hasta, $orden)
    {
        $cid = new Conexion();
        $cid_uruguay = $cid->conectarSql('uy');

        if (!$cid_uruguay) {
            error_log("Error: No se pudo conectar a la base de datos de Uruguay");
            return [];
        }

        // Determinar tienda según formato del ORDER_ID
        $tienda = '%'; // Buscar en todas por defecto
        if (!empty($orden) && $orden != '%') {
            if (strpos($orden, '-') !== false) {
                $tienda = 'VTEX';
            } elseif (is_numeric($orden)) {
                // Si es solo numérico, podría ser NRO_PEDIDO, buscar en todas
                $tienda = '%';
            }
        }

        $sql = "SET DATEFORMAT YMD;
                EXEC RO_SP_ECOMMERCE_PEDIDOS_URUGUAY ?, ?, ?";

        $params = array($desde, $hasta, $tienda);
        $stmt = sqlsrv_query($cid_uruguay, $sql, $params);

        if ($stmt === false) {
            error_log("Error buscarPedidoUY: " . print_r(sqlsrv_errors(), true));
            return [];
        }

        $data = [];

        // Normalizar búsqueda
        $ordenBusqueda = trim($orden);
        $buscarTodos = (empty($ordenBusqueda) || $ordenBusqueda == '%');

        // Si es numérico, preparar para comparación flexible
        $esNumerico = is_numeric($ordenBusqueda);
        $ordenNumerico = $esNumerico ? intval($ordenBusqueda) : null;

        $pedidosProcessados = [];

        while ($v = sqlsrv_fetch_object($stmt)) {
            $nroPedidoKey = trim($v->NRO_PEDIDO ?? '');
            $coincide = false;

            if ($buscarTodos) {
                $coincide = true;
            } else {
                $orderIdTienda = trim($v->ORDER_ID_TIENDA ?? '');
                $nroPedido = trim($v->NRO_PEDIDO ?? '');
                $factura = trim($v->FACTURA ?? '');

                // ESTRATEGIA 1: ORDER_ID_TIENDA
                if (stripos($orderIdTienda, $ordenBusqueda) !== false) {
                    $coincide = true;
                }

                // ESTRATEGIA 2: NRO_PEDIDO
                if (!$coincide) {
                    // Búsqueda textual exacta
                    if ($nroPedido === $ordenBusqueda) {
                        $coincide = true;
                    }
                    // Búsqueda textual parcial
                    else if (stripos($nroPedido, $ordenBusqueda) !== false) {
                        $coincide = true;
                    }
                    // Comparación numérica (ignora ceros)
                    else if ($esNumerico && $ordenNumerico !== null) {
                        $nroPedidoNumerico = intval($nroPedido);
                        if ($nroPedidoNumerico == $ordenNumerico) {
                            $coincide = true;
                        }
                    }
                }

                // ESTRATEGIA 3: FACTURA
                if (!$coincide && !empty($factura)) {
                    if (stripos($factura, $ordenBusqueda) !== false) {
                        $coincide = true;
                    }
                }
            }

            if ($coincide) {
                // Enriquecer el objeto ORIGINAL sin reemplazarlo
                $this->enriquecerPedidoUY($v);

                // Para evitar duplicados, solo agregar si NO lo hemos visto
                if (!isset($pedidosProcessados[$nroPedidoKey])) {
                    $pedidosProcessados[$nroPedidoKey] = true;
                    $data[] = array($v);
                }
            }
        }

        return $data;
    }

    /**
     * Enriquece el objeto pedido de Uruguay DIRECTAMENTE
     * Adds mapeadas y enriquecidas sin crear un nuevo objeto
     * El objeto se modifica in-place
     */
    private function enriquecerPedidoUY(&$pedido)
    {
        // Mapear campos que vienen del SP pero con nombres diferentes
        // ORIGEN → MARKETPLACE y MARKETPLACE
        if (isset($pedido->ORIGEN) && !isset($pedido->MARKETPLACE)) {
            $pedido->MARKETPLACE = $pedido->ORIGEN;
        }

        // ORDER_ID_TIENDA → NRO_ORDEN (si no existe)
        if (isset($pedido->ORDER_ID_TIENDA) && !isset($pedido->NRO_ORDEN)) {
            $pedido->NRO_ORDEN = $pedido->ORDER_ID_TIENDA;
        }

        // RECEIVER_NAME → CLIENTE (si no existe)
        if (isset($pedido->RECEIVER_NAME) && !isset($pedido->CLIENTE)) {
            $pedido->CLIENTE = trim($pedido->RECEIVER_NAME);
        }

        // Mapear RAZON_SOCI (para compatibilidad con Argentina)
        if (!isset($pedido->RAZON_SOCI) && isset($pedido->CLIENTE)) {
            $pedido->RAZON_SOCI = $pedido->CLIENTE;
        }

        // DEPARTAMENTO → DIRECCION_ENTREGA y LUGAR_ENTREGA (si están vacíos)
        if (isset($pedido->DEPARTAMENTO)) {
            if (!isset($pedido->DIRECCION_ENTREGA) || empty($pedido->DIRECCION_ENTREGA)) {
                $pedido->DIRECCION_ENTREGA = $pedido->DEPARTAMENTO;
            }
            if (!isset($pedido->LUGAR_ENTREGA) || empty($pedido->LUGAR_ENTREGA)) {
                $pedido->LUGAR_ENTREGA = $pedido->DEPARTAMENTO;
            }
        }

        // Si DEPARTAMENTO es código corto (1-2 chars), intentar buscar nombre completo
        if (isset($pedido->DEPARTAMENTO) && strlen(trim($pedido->DEPARTAMENTO)) <= 2) {
            $cid = new Conexion();
            $cid_uruguay = $cid->conectarSql('uy');

            if ($cid_uruguay) {
                $sqlDept = "SELECT NOMBRE FROM STA01 WHERE CODIGO = ?";
                $paramsDept = array(trim($pedido->DEPARTAMENTO));
                $stmtDept = sqlsrv_query($cid_uruguay, $sqlDept, $paramsDept);

                if ($stmtDept && ($deptRow = sqlsrv_fetch_object($stmtDept))) {
                    $deptNombre = trim($deptRow->NOMBRE ?? '');
                    if (!empty($deptNombre)) {
                        $pedido->DEPARTAMENTO = $deptNombre;
                        $pedido->DIRECCION_ENTREGA = $deptNombre;
                        $pedido->LUGAR_ENTREGA = $deptNombre;
                    }
                }
                if ($stmtDept)
                    sqlsrv_free_stmt($stmtDept);
            }
        }

        // Intentar obtener información adicional de la tabla RO_T_ESTADO_PEDIDOS_ECOMMERCE
        if (!empty($pedido->NRO_PEDIDO)) {
            $cid = new Conexion();
            $cid_uruguay = $cid->conectarSql('uy');

            if ($cid_uruguay) {
                $nroPedido = trim($pedido->NRO_PEDIDO);

                $sqlEstado = "
                    SELECT TOP 1
                        WAREHOUSE,
                        METODO_ENVIO,
                        SUCURSAL_ENTREGA
                    FROM RO_T_ESTADO_PEDIDOS_ECOMMERCE
                    WHERE NRO_PEDIDO = ?
                    ORDER BY ULT_ACTUALIZACION DESC
                ";

                $paramsEstado = array($nroPedido);
                $stmtEstado = sqlsrv_query($cid_uruguay, $sqlEstado, $paramsEstado);

                if ($stmtEstado && ($estadoRow = sqlsrv_fetch_object($stmtEstado))) {
                    // Enriquecer si el campo no existe
                    if (!isset($pedido->WAREHOUSE) && !empty($estadoRow->WAREHOUSE)) {
                        $pedido->WAREHOUSE = trim($estadoRow->WAREHOUSE);
                    }
                    if (!isset($pedido->PREPARA) && !empty($estadoRow->WAREHOUSE)) {
                        $pedido->PREPARA = trim($estadoRow->WAREHOUSE);
                    }
                    if (!isset($pedido->METODO_ENVIO) && !empty($estadoRow->METODO_ENVIO)) {
                        $pedido->METODO_ENVIO = trim($estadoRow->METODO_ENVIO);
                    }
                    if (!isset($pedido->SUCURSAL_ENTREGA) && !empty($estadoRow->SUCURSAL_ENTREGA)) {
                        $pedido->SUCURSAL_ENTREGA = trim($estadoRow->SUCURSAL_ENTREGA);
                    }

                    sqlsrv_free_stmt($stmtEstado);
                }
            }
        }

        // Asegurar que campos necesarios tengan algún valor
        if (!isset($pedido->PREPARA)) {
            $pedido->PREPARA = null;
        }
        if (!isset($pedido->WAREHOUSE)) {
            $pedido->WAREHOUSE = null;
        }
        if (!isset($pedido->SUCURSAL_ENTREGA)) {
            $pedido->SUCURSAL_ENTREGA = null;
        }
        if (!isset($pedido->METODO_ENVIO)) {
            $pedido->METODO_ENVIO = null;
        }
    }

    /**
     * Trae warehouses/sucursales de Uruguay
     */
    public function traerWarehouseUY()
    {
        $cid = new Conexion();
        $cid_uruguay = $cid->conectarSql('uy');

        if (!$cid_uruguay) {
            error_log("Error: No se pudo conectar a la base de datos de Uruguay");
            return [];
        }

        $sql = "SELECT REPLACE(NOMBRE_SUC, 'RT - SUC - ', '') AS WAREHOUSE
                FROM STA22
                WHERE NOMBRE_SUC LIKE 'RT - SUC - %'
                UNION ALL
                SELECT 'CENTRAL' AS WAREHOUSE
                ORDER BY 1";

        $result = sqlsrv_query($cid_uruguay, $sql);

        if ($result === false) {
            error_log("Error traerWarehouseUY: " . print_r(sqlsrv_errors(), true));
            return [];
        }

        $data = [];
        while ($v = sqlsrv_fetch_object($result)) {
            $data[] = array($v);
        }

        return $data;
    }

    /**
     * Busca stock de artículos por sucursal en Uruguay
     */
    public function buscarStockArticuloUY($sucursal)
    {
        $cid = new Conexion();
        $cid_uruguay = $cid->conectarSql('uy');

        if (!$cid_uruguay) {
            error_log("Error: No se pudo conectar a la base de datos de Uruguay");
            return [];
        }

        // Mapeo de nombres de sucursales Uruguay
        $mapeoSucursales = [
            'MONTEVIDEO' => 'RT - SUC - MONTEVIDEO',
            'NUEVOCENTRO' => 'RT - SUC - NUEVOCENTRO',
            'NUEVO CENTRO' => 'RT - SUC - NUEVOCENTRO',
            'TRES CRUCES' => 'RT - SUC - TRES CRUCES',
            'CENTRAL' => 'CASA CENTRAL'
        ];

        $sucursalUpper = strtoupper(trim($sucursal));
        $sucursalStock = $mapeoSucursales[$sucursalUpper] ?? $sucursal;

        $sql = "SELECT 
                    S22.COD_SUCURS AS NRO_SUCURSAL,
                    S22.NOMBRE_SUC AS DESC_SUCURSAL,
                    S19.COD_ARTICU AS ARTICULO,
                    CTA.DESC_CTA_ARTICULO,
                    S19.CANT_STOCK
                FROM STA19 S19
                INNER JOIN STA22 S22 ON S19.COD_DEPOSI = S22.COD_SUCURS COLLATE DATABASE_DEFAULT
                LEFT JOIN CTA_ARTICULO CTA ON S19.COD_ARTICU = CTA.COD_ARTICULO COLLATE DATABASE_DEFAULT
                WHERE S22.NOMBRE_SUC = ?
                  AND S19.COD_ARTICU LIKE '[XO]%'
                  AND S19.CANT_STOCK > 0
                ORDER BY S19.COD_ARTICU";

        $params = array($sucursalStock);
        $stmt = sqlsrv_query($cid_uruguay, $sql, $params);

        if ($stmt === false) {
            error_log("Error buscarStockArticuloUY: " . print_r(sqlsrv_errors(), true));
            return [];
        }

        $data = [];
        while ($v = sqlsrv_fetch_object($stmt)) {
            $data[] = array($v);
        }

        return $data;
    }

    /**
     * Obtiene devoluciones para Uruguay (usa CANCELADO en lugar de REINTEGRADO)
     */
    public function obtenerDevolucionesUY($nroPedido, $nroOrden = null)
    {
        $cid = new Conexion();
        $cid_uruguay = $cid->conectarSql('uy');

        if (!$cid_uruguay) {
            return [];
        }

        // DIFERENCIA CLAVE: Uruguay usa CANCELADO en lugar de REINTEGRADO
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
                WHEN EP.CANCELADO = 1 AND (EP.NCR IS NULL OR EP.NCR = '') THEN 'NCR_PENDIENTE'
                WHEN EP.CANCELADO = 1 AND EP.NCR IS NOT NULL AND EP.NCR <> '' THEN 'NCR_EMITIDA'
                ELSE 'NORMAL'
            END as ESTADO,
            'Pedido cancelado' as DESCRIPCIO,
            EP.NRO_PEDIDO as DEBUG_NRO_PEDIDO,
            EP.ORDER_ID as DEBUG_ORDER_ID,
            EP.CANCELADO as DEBUG_CANCELADO,
            EP.FACTURA as FACTURA
        FROM RO_T_ESTADO_PEDIDOS_ECOMMERCE EP
        WHERE RTRIM(LTRIM(EP.NRO_PEDIDO)) = ?
        AND EP.CANCELADO = 1
        ";

        $params = array($nroPedido);
        $stmt = sqlsrv_query($cid_uruguay, $sql, $params);

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
     * Verifica cancelación de pedido en Uruguay
     */
    public function verificarCancelacionUY($nroPedido, $nroOrden = null)
    {
        $cid = new Conexion();
        $cid_uruguay = $cid->conectarSql('uy');

        if (!$cid_uruguay) {
            return null;
        }

        // CAMBIO CLAVE: Uruguay usa CANCELADO en lugar de REINTEGRADO
        $sql = "SELECT 
                    EP.CANCELADO,
                    EP.NCR,
                    EP.FECHA_NCR,
                    EP.FACTURA,
                    EP.FECHA_PEDI,
                    EP.CONTROLADO
                FROM RO_T_ESTADO_PEDIDOS_ECOMMERCE EP
                WHERE RTRIM(LTRIM(EP.NRO_PEDIDO)) = ?";

        $params = array($nroPedido);
        $stmt = sqlsrv_query($cid_uruguay, $sql, $params);

        if ($stmt === false || !sqlsrv_has_rows($stmt)) {
            return null;
        }

        $estadoPedido = sqlsrv_fetch_object($stmt);

        if ($estadoPedido->CANCELADO != 1) {
            return null;
        }

        // Obtener detalle del pedido
        $sqlDetalle = "SELECT 
                        COUNT(*) as TOTAL_ARTICULOS,
                        SUM(CAST(DP.CANT_PEDID as INT)) as TOTAL_CANTIDAD
                    FROM RO_DPEDI01 DP
                    WHERE RTRIM(LTRIM(DP.NRO_PEDIDO)) = ?";

        $stmtDetalle = sqlsrv_query($cid_uruguay, $sqlDetalle, $params);
        $detallePedido = $stmtDetalle ? sqlsrv_fetch_object($stmtDetalle) : null;

        return (object) [
            'esta_cancelado' => true,
            'tiene_ncr' => !empty($estadoPedido->NCR),
            'numero_ncr' => $estadoPedido->NCR ?? null,
            'fecha_ncr' => $estadoPedido->FECHA_NCR ?? null,
            'factura' => $estadoPedido->FACTURA ?? null,
            'es_parcial' => false,
            'total_articulos' => $detallePedido ? $detallePedido->TOTAL_ARTICULOS : 0,
            'total_cantidad' => $detallePedido ? $detallePedido->TOTAL_CANTIDAD : 0
        ];
    }


}
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
    
    public function traerPedidos($desde, $hasta, $tienda, $warehouse, $estado = null, $orden){
        $tienda = $_GET['tienda'];
        $warehouse = $_GET['warehouse'];
            
        $sql = "
        SET DATEFORMAT YMD
        SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;
        EXEC RO_ECOMMERCE_PEDIDOS '$desde', '$hasta', '%$tienda', '%$warehouse', '$estado', '$orden'
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
        $sql = "SELECT NRO_SUCURSAL, DESC_SUCURSAL, ARTICULO, DESC_CTA_ARTICULO, CANT_STOCK FROM [LAKERBIS].LOCALES_LAKERS.DBO.RO_STOCK_LAKERS A
                INNER JOIN [LAKERBIS].LOCALES_LAKERS.DBO.CTA_ARTICULO B ON A.ARTICULO = B.COD_ARTICULO
                WHERE DESC_SUCURSAL = '$sucursal' AND A.ARTICULO LIKE '[XO]%'
                ORDER BY ARTICULO
        ";

        $array = $this->getDatos($sql);    
        return $array;
    }

    public function guardarHistorialReclamo($data) {
        $cid = new Conexion();
        $cid_central = $cid->conectarSql('central');
    
        $sql = "INSERT INTO RO_T_ENC_ECOMMERCE_HISTORIAL_FALT (FECHA_PEDIDO, NRO_ORDEN, NRO_PEDIDO, CLIENTE, WAREHOUSE, COD_ARTICULO_CAMBIO, DESCRIPCION, CANTIDAD, ESTADO, RESOLUCION, SUC_DESPACHO, COD_ARTICULO)
        VALUES ('".$data['fechaHora']."', '".$data['nroOrden']."', '".$data['nro_pedido']."', '".$data['cliente']."', '".$data['sucursal']."', '".$data['articulo']."', '".$data['descripcion']."', '".$data['modalCantidad']."', '".$data['estado']."', '".$data['resolucion']."', '".$data['sucursal']."', '".$data['modalCodigo']."')
        ";

        $result=sqlsrv_query($cid_central,$sql)or die(exit("Error en sqlsrv_query"));
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

    public function actualizarEstadoReclamo($nro_pedido, $estado) {
        $cid = new Conexion();
        $cid_central = $cid->conectarSql('central');
        
        // Verificar si existe un registro en la tabla de historial
        $sqlCheck = "SELECT COUNT(*) as count FROM RO_T_ENC_ECOMMERCE_HISTORIAL_FALT WHERE NRO_PEDIDO = '$nro_pedido'";
        $resultCheck = sqlsrv_query($cid_central, $sqlCheck);
        $row = sqlsrv_fetch_array($resultCheck, SQLSRV_FETCH_ASSOC);
        
        if ($row['count'] > 0) {
            // Actualizar registro existente - solo estado y fecha de última modificación
            $sql = "UPDATE RO_T_ENC_ECOMMERCE_HISTORIAL_FALT 
                    SET ESTADO = '$estado', FECHA_ULT_MODIF = GETDATE() 
                    WHERE NRO_PEDIDO = '$nro_pedido'";
        } else {
            // Crear registro básico si no existe - con fecha de alta
            $sql = "INSERT INTO RO_T_ENC_ECOMMERCE_HISTORIAL_FALT (NRO_PEDIDO, ESTADO, FECHA_PEDIDO, FECHA_ALTA, FECHA_ULT_MODIF) 
                    VALUES ('$nro_pedido', '$estado', GETDATE(), GETDATE(), GETDATE())";
        }
        
        $result = sqlsrv_query($cid_central, $sql) or die(exit("Error en sqlsrv_query"));
        return $result;
    }

    public function guardarHistorialReclamoConUpsert($data) {
        $cid = new Conexion();
        $cid_central = $cid->conectarSql('central');

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
        
        if ($row['count'] > 0) {
            // UPDATE - Actualizar registro existente
            $sql = "UPDATE RO_T_ENC_ECOMMERCE_HISTORIAL_FALT SET 
                    NRO_ORDEN = '$nroOrden',
                    CLIENTE = '$cliente',
                    WAREHOUSE = '$warehouse',
                    COD_ARTICULO = '$modalCodigo',
                    CANTIDAD = '$modalCantidad',
                    SUC_DESPACHO = '$sucDespacho',
                    ESTADO = '" . ($data['estado'] ?? '') . "',
                    RESOLUCION = '" . ($data['resolucion'] ?? '') . "',
                    COD_ARTICULO_CAMBIO = '" . ($data['articulo'] ?? '') . "',
                    DESCRIPCION = '" . ($data['descripcion'] ?? '') . "',
                    FECHA_ULT_MODIF = GETDATE()
                    WHERE NRO_PEDIDO = '$nro_pedido'";
        } else {
            // INSERT - Crear nuevo registro
            $sql = "INSERT INTO RO_T_ENC_ECOMMERCE_HISTORIAL_FALT (
                    FECHA_PEDIDO, NRO_ORDEN, NRO_PEDIDO, CLIENTE, WAREHOUSE, 
                    COD_ARTICULO_CAMBIO, DESCRIPCION, CANTIDAD, ESTADO, RESOLUCION, 
                    SUC_DESPACHO, COD_ARTICULO, FECHA_ALTA, FECHA_ULT_MODIF
                    ) VALUES (
                    '" . ($data['fechaHora'] ?? '') . "', 
                    '$nroOrden', 
                    '$nro_pedido', 
                    '$cliente', 
                    '$warehouse', 
                    '" . ($data['articulo'] ?? '') . "', 
                    '" . ($data['descripcion'] ?? '') . "', 
                    '$modalCantidad', 
                    '" . ($data['estado'] ?? '') . "', 
                    '" . ($data['resolucion'] ?? '') . "', 
                    '$sucDespacho', 
                    '$modalCodigo',
                    GETDATE(),
                    GETDATE()
                    )";
        }
        
        $result = sqlsrv_query($cid_central, $sql) or die(exit("Error en sqlsrv_query: " . print_r(sqlsrv_errors(), true)));
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

    // CORRECCIÓN FINAL: Usamos el JOIN a la vista RO_V_STA22, que es la misma que genera el campo "Prepara".
    $sql = "
        WITH IncidentesAuditoria AS (
            SELECT 
                A.NRO_ORDEN_ECOMMERCE
            FROM SOF_AUDITORIA A
            WHERE 
                A.FECHA_AUDITORIA_1 IS NOT NULL 
                AND A.COD_ARTICULO LIKE '[XO]%'
                AND CAST(A.FECHA_PEDIDO AS DATE) BETWEEN ? AND ?
            GROUP BY 
                A.NRO_ORDEN_ECOMMERCE
            HAVING 
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

            -- NUEVA LÓGICA: Obtenemos el nombre de la sucursal desde la vista RO_V_STA22
            COALESCE(V_STA22.SUCURSAL_ENTREGA, CASE WHEN GVA21.COD_SUCURS = '01' THEN 'CENTRAL' ELSE 'No especificado' END) AS NOMBRE_ORIGEN,

            ISNULL(H.COD_ARTICULO, 'Discrepancia General') as COD_ARTICULO
            
        FROM IncidentesAuditoria IA
        INNER JOIN GVA21 ON IA.NRO_ORDEN_ECOMMERCE = GVA21.ORDER_ID_TIENDA COLLATE DATABASE_DEFAULT
        LEFT JOIN GVA38 ON GVA21.NRO_PEDIDO = GVA38.N_COMP AND GVA21.TALON_PED = GVA38.TALONARIO
        LEFT JOIN RO_T_ENC_ECOMMERCE_HISTORIAL_FALT H ON GVA21.ORDER_ID_TIENDA = H.NRO_ORDEN COLLATE DATABASE_DEFAULT
        -- NUEVO JOIN: Unimos con la vista RO_V_STA22 para obtener el nombre de la sucursal de origen
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
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error en la consulta SQL', 'details' => sqlsrv_errors()]);
        exit;
    }

    $data = [];
    while ($v = sqlsrv_fetch_object($stmt)) {
        $data[] = array($v);
    }

    return $data;
}
}
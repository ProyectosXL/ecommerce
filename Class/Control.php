<?php
require_once $_SERVER['DOCUMENT_ROOT']. '/ecommerce/Class/Conexion.php';

class Control {

    public function conectarSql($nameServer = null) {
        try {
            $serverDB = $this->servidor($nameServer);
            $pass = ($nameServer == 'locales') ? $this->pass_locales : $this->pass;
    
            $params = array( 
                "Database" => $serverDB[1], 
                "UID" => $this->user, 
                "PWD" => $pass, 
                "CharacterSet" => $this->character
            );
    
            $cid = sqlsrv_connect($serverDB[0], $params);
            
            if ($cid === false) {
                $errors = sqlsrv_errors();
                throw new Exception("Error de conexión: " . $errors[0]['message']);
            }
    
            return $cid;
            
        } catch (Exception $e) {
            throw new Exception("Error en la conexión: " . $e->getMessage());
        }
    }

    private function getDatos($sql) {
        try {
            $cid = new Conexion();
            $cid_central = $cid->conectarSql('central');
            
            if ($cid_central === false) {
                throw new Exception("Error de conexión a la base de datos");
            }
            
            ini_set('max_execution_time', 300);
            $result = sqlsrv_query($cid_central, $sql, array(), array("Scrollable" => "buffered"));
            
            if ($result === false) {
                $errors = sqlsrv_errors();
                throw new Exception("Error en la consulta: " . $errors[0]['message']);
            }
            
            $row = sqlsrv_fetch_object($result);
            sqlsrv_free_stmt($result);
            sqlsrv_close($cid_central);
            
            return $row ? $row : null;
            
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    
    private function getDatosMultiples($sql) {
        try {
            $cid = new Conexion();
            $cid_central = $cid->conectarSql('central');
            
            if ($cid_central === false) {
                throw new Exception("Error de conexión a la base de datos");
            }
            
            ini_set('max_execution_time', 300);
            $result = sqlsrv_query($cid_central, $sql, array(), array("Scrollable" => "buffered"));
            
            if ($result === false) {
                $errors = sqlsrv_errors();
                throw new Exception("Error en la consulta: " . $errors[0]['message']);
            }
            
            $rows = array();
            while ($row = sqlsrv_fetch_object($result)) {
                $rows[] = $row;
            }
            
            sqlsrv_free_stmt($result);
            sqlsrv_close($cid_central);
            
            return $rows;
            
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    public function traerNcPendPromociones() {
        $sql = "SELECT MIN(CAST(FECHA AS DATE)) FECHA, COUNT(*) CANT_NC_PROMO, SUM(NC) IMPORTE_NC FROM SJ_NC_ECOMMERCE_PEND WHERE NUM_NC = 'NO'";
        return $this->getDatos($sql);
    }

    public function traerDetalleNcPendPromociones() {
        $sql = "SELECT FECHA, COD_PROMOCION_TARJETA, DESC_PROMOCION_TARJETA, PORC_REINTEGRO, COD_ARTICU, NC 
                FROM SJ_NC_ECOMMERCE_PEND WHERE NUM_NC = 'NO'";
        return $this->getDatosMultiples($sql);
    }

    public function traerNcPendDevoluciones() {
        $sql = "SELECT MIN(CAST(FECHA_PEDI AS DATE)) FECHA, COUNT(*) CANT_NC_DEV, SUM(IMPORTE) IMPORTE_PEND FROM 
                (
                SELECT A.FECHA_PEDI, A.NRO_PEDIDO, A.ORDER_ID_TIENDA, C.N_COMP, D.IMPORTE FROM GVA21 A
                LEFT JOIN RO_T_ESTADO_PEDIDOS_ECOMMERCE B ON A.ORDER_ID_TIENDA = B.ORDER_ID
                LEFT JOIN GVA55 C ON A.TALON_PED = C.TALON_PED AND A.NRO_PEDIDO = C.NRO_PEDIDO
                LEFT JOIN GVA12 D ON C.N_COMP = D.N_COMP AND C.T_COMP = D.T_COMP
                WHERE A.COD_CLIENT = '000000' AND A.FECHA_PEDI >= GETDATE()-150 AND B.CANCELADO = 1 
                AND B.NCR IS NULL AND C.N_COMP IS NOT NULL AND REINTEGRADO IS NULL
                ) A";
        return $this->getDatos($sql);
    }

    public function traerDetalleNcPendDevoluciones() {
        $sql = "SELECT CAST(A.FECHA_PEDI AS DATE) FECHA_PEDI, A.NRO_PEDIDO, A.ORDER_ID_TIENDA, UPPER(E.RAZON_SOCI) CLIENTE,
                D.COD_SUCURS, C.N_COMP, CAST(D.IMPORTE AS FLOAT) IMPORTE FROM GVA21 A
                LEFT JOIN RO_T_ESTADO_PEDIDOS_ECOMMERCE B ON A.ORDER_ID_TIENDA = B.ORDER_ID
                LEFT JOIN GVA55 C ON A.TALON_PED = C.TALON_PED AND A.NRO_PEDIDO = C.NRO_PEDIDO
                LEFT JOIN GVA12 D ON C.N_COMP = D.N_COMP AND C.T_COMP = D.T_COMP
                LEFT JOIN GVA38 E ON A.TALON_PED = E.TALONARIO AND A.NRO_PEDIDO = E.N_COMP
                WHERE A.COD_CLIENT = '000000' AND A.FECHA_PEDI >= GETDATE()-150 AND B.CANCELADO = 1 
                AND B.NCR IS NULL AND C.N_COMP IS NOT NULL AND REINTEGRADO IS NULL";
        return $this->getDatosMultiples($sql);
    }

    public function traerNcrRealizadas() {
        $sql = "SELECT 
                    CAST(A.FECHA_EMIS AS DATE) AS FECHA_EMIS, 
                    COUNT(A.N_COMP) AS CANT_NCR 
                FROM GVA12 A
                INNER JOIN GVA53 B ON A.T_COMP = B.T_COMP AND A.N_COMP = B.N_COMP
                WHERE COD_CLIENT = '000000' 
                AND FECHA_EMIS >= DATEADD(DAY, -7, CAST(GETDATE() AS DATE))
                AND A.T_COMP = 'NCR' 
                AND B.COD_ARTICU LIKE '[XO]%'
                GROUP BY CAST(A.FECHA_EMIS AS DATE)
                ORDER BY CAST(A.FECHA_EMIS AS DATE)";
        return $this->getDatosMultiples($sql);
    }

    public function traerOrdenesSinIntegrar() {
        $sql = "SELECT MIN(CAST(A.FECHA_ORDEN AS DATETIME)) FECHA_ORDEN, COUNT(*) CANT_ORDENES, SUM(TOTAL_ORDEN) TOTAL_ORDEN FROM
                (
                SELECT A.FECHA_ULTIMA_SINCRONIZACION FECHA_ORDEN, A.ORDER_NRO_TIENDA, A.TOTAL_ORDEN, A.ESTADO_ORDEN FROM NEXO_PEDIDOS_ORDEN A
                LEFT JOIN GVA21 B ON A.ORDER_ID_TIENDA = B.ORDER_ID_TIENDA
                WHERE B.NRO_PEDIDO IS NULL AND A.FECHA_ORDEN >= GETDATE()-60 AND A.ESTADO_ORDEN NOT LIKE 'CANCELADA%'
                ) A";
        return $this->getDatos($sql);
    }

    public function traerDetalleOrdenesSinIntegrar() {
        $sql = "SELECT A.FECHA_ULTIMA_SINCRONIZACION FECHA_ORDEN, A.TIENDA, A.ORDER_NRO_TIENDA, A.TOTAL_ORDEN FROM NEXO_PEDIDOS_ORDEN A
                LEFT JOIN GVA21 B ON A.ORDER_ID_TIENDA = B.ORDER_ID_TIENDA
                WHERE B.NRO_PEDIDO IS NULL AND A.FECHA_ORDEN >= GETDATE()-60 AND A.ESTADO_ORDEN NOT LIKE 'CANCELADA%'
                ORDER BY FECHA_ULTIMA_SINCRONIZACION";
        return $this->getDatosMultiples($sql);
    }

    public function traerPedidosSinFactTiendas() {
        $sql = "SELECT MIN(FECHA_HORA) AS FECHA_PEDI, COUNT(*) AS CANT_PED_SIN_FACT, SUM(TOTAL_PEDI) AS TOTAL_PEDIDOS FROM 
                (SELECT TRY_CAST(CONCAT(FORMAT(A.FECHA_PEDI, 'yyyy-dd-MM'), ' ', LEFT(A.HORA_INGRESO, 2), ':', SUBSTRING(A.HORA_INGRESO, 3, 2)) AS DATETIME) AS FECHA_HORA,
                A.TOTAL_PEDI FROM GVA21 A
                LEFT JOIN GVA55 C ON A.TALON_PED = C.TALON_PED AND A.NRO_PEDIDO = C.NRO_PEDIDO
                LEFT JOIN GVA12 D ON C.N_COMP = D.N_COMP AND C.T_COMP = D.T_COMP
                WHERE A.COD_CLIENT = '000000' AND A.FECHA_PEDI >= DATEADD(DAY, -7, GETDATE()) AND C.N_COMP IS NULL 
                AND A.COD_SUCURS NOT IN ('01', '11') AND DATEADD(MINUTE, 30, TRY_CAST(CONCAT(FORMAT(A.FECHA_PEDI, 'yyyy-MM-dd'), 
                ' ', LEFT(A.HORA_INGRESO, 2),':', SUBSTRING(A.HORA_INGRESO, 3, 2),':',RIGHT(A.HORA_INGRESO, 2)) AS DATETIME)) < GETDATE()) A
                ";
        return $this->getDatos($sql);
    }

    public function traerPedidosFlex() {
        $sql = "SELECT MIN(CAST(FECHA_SINCRONIZADO AS DATETIME)) AS FECHA_PEDI, COUNT(*) AS CANT_PED_PEND, SUM(TOTAL_PEDI) AS TOTAL_PEDIDOS FROM 
                (
                SELECT C.FECHA_SINCRONIZADO, A.HORA_INGRESO, A.ORDER_ID_TIENDA, A.TOTAL_PEDI, C.ENTREGADO, C.CANCELADO FROM GVA21 A
                INNER JOIN (SELECT * FROM RO_V_WAREHOUSE_METODO_ENVIO_VTEX WHERE METODO_ENVIO = 'FLEX') B ON A.COD_TRANSP = B.COD_TRANSP
                LEFT JOIN RO_T_ESTADO_PEDIDOS_ECOMMERCE C ON A.ORDER_ID_TIENDA = C.ORDER_ID
                WHERE A.COD_CLIENT = '000000' AND A.FECHA_PEDI >= CAST(GETDATE() - 10 AS DATE) AND ((A.FECHA_PEDI = CAST(GETDATE() AS DATE) AND 
                TRY_CAST(A.HORA_INGRESO AS INT) <= 120000) OR A.FECHA_PEDI < CAST(GETDATE() AS DATE)) AND C.DESPACHADO IS NULL AND C.ENTREGADO IS NULL
				AND C.CANCELADO IS NULL AND A.COD_SUCURS = '01' AND A.ESTADO != '5'
                ) A;
        ";
        return $this->getDatos($sql);
    }

    public function traerDetallePedidosFlex() {
        $sql = "SELECT CAST(C.FECHA_SINCRONIZADO AS DATETIME) FECHA_SINCRONIZADO, 
                CASE WHEN A.TALON_PED = '98' THEN 'MERCADO LIBRE'
                    WHEN A.TALON_PED = '99' THEN 'VTEX'	
                END CANAL,
                A.NRO_PEDIDO, A.ORDER_ID_TIENDA, UPPER(D.RAZON_SOCI) CLIENTE ,CAST(A.TOTAL_PEDI AS DECIMAL(10,0)) TOTAL_PEDI FROM GVA21 A
                INNER JOIN (SELECT * FROM RO_V_WAREHOUSE_METODO_ENVIO_VTEX WHERE METODO_ENVIO = 'FLEX') B ON A.COD_TRANSP = B.COD_TRANSP
                LEFT JOIN RO_T_ESTADO_PEDIDOS_ECOMMERCE C ON A.ORDER_ID_TIENDA = C.ORDER_ID
                LEFT JOIN GVA38 D ON A.TALON_PED = D.TALONARIO AND A.NRO_PEDIDO = D.N_COMP
                WHERE A.COD_CLIENT = '000000' AND A.FECHA_PEDI >= CAST(GETDATE() - 10 AS DATE) AND ((A.FECHA_PEDI = CAST(GETDATE() AS DATE) AND 
                TRY_CAST(A.HORA_INGRESO AS INT) <= 120000) OR A.FECHA_PEDI < CAST(GETDATE() AS DATE)) AND C.DESPACHADO IS NULL AND C.ENTREGADO IS NULL
                AND C.CANCELADO IS NULL AND A.COD_SUCURS = '01' AND A.ESTADO != '5'
                ORDER BY C.FECHA_SINCRONIZADO;
        ";
        return $this->getDatosMultiples($sql);
    }

    public function traerFacturasSinRemito() {
        $sql = "SELECT MIN(CAST(FECHA_FACTURA AS DATE)) FECHA_FACTURA, COUNT(DISTINCT(FACTURA)) CANT_FACTURAS, SUM(IMPORTE) IMPORTE, MAX(FECHA_ACTUALIZACION) FECHA_ACTUALIZACION 
                FROM [LAKERBIS].[LOCALES_LAKERS].DBO.RO_FACTURAS_SIN_REMITO;
        ";
        return $this->getDatos($sql);
    }

    public function traerDetalleFacturasSinRemito() {
        $sql = "SELECT SUCURSAL, CAST(FECHA_FACTURA AS DATE) FECHA_FACTURA, FACTURA, A.COD_ARTICU, B.DESC_CTA_ARTICULO, CANTIDAD 
                FROM [LAKERBIS].[LOCALES_LAKERS].DBO.RO_FACTURAS_SIN_REMITO A  
                INNER JOIN [LAKERBIS].[LOCALES_LAKERS].DBO.CTA_ARTICULO B ON A.COD_ARTICU = B.COD_ARTICULO
                ORDER BY FECHA_FACTURA DESC, SUCURSAL";
        return $this->getDatosMultiples($sql);
    }

    public function traerOrdenesPendientesCierre() {
        $sql = "SELECT MIN(FECHA) FECHA, COUNT(*) CANT_ORDENES, AVG(DIAS_ANTIGUEDAD) PROM_RETRASO FROM
                (
                SELECT CAST(A.FECHA_ORDER AS datetime) FECHA, A.ORDER_ID, UPPER(A.NOMBRE_COMPRADOR) CLIENTE, 
                UPPER(REPLACE(REPLACE(B.DESCRIPCION, 'Franquicia ', ''), 'Cuenta principal ', '')) as SUCURSAL, A.DIAS_ANTIGUEDAD 
                FROM GC_VIEW_ECOMMERCE_ORDENES_VTEX_PENDIENTES_CIERRE A
                LEFT JOIN GC_ECOMMERCE_CUENTA B ON A.ID_GC_ECOMMERCE_CUENTA_SELLER = B.ID_GC_ECOMMERCE_CUENTA
                ) A";
        return $this->getDatos($sql);
    }

    public function traerDetalleOrdenesPendientesCierre() {
        $sql = "SELECT CAST(A.FECHA_ORDER AS datetime) FECHA, A.ORDER_ID, UPPER(A.NOMBRE_COMPRADOR) CLIENTE, 
                UPPER(REPLACE(REPLACE(B.DESCRIPCION, 'Franquicia ', ''), 'Cuenta principal ', '')) as SUCURSAL, A.DIAS_ANTIGUEDAD 
                FROM GC_VIEW_ECOMMERCE_ORDENES_VTEX_PENDIENTES_CIERRE A
                LEFT JOIN GC_ECOMMERCE_CUENTA B ON A.ID_GC_ECOMMERCE_CUENTA_SELLER = B.ID_GC_ECOMMERCE_CUENTA";
        return $this->getDatosMultiples($sql);
    }

    public function traerPedidosPendienteDespacho() {
        $sql = "SELECT MIN(CAST(FECHA_SINCRONIZADO AS DATETIME)) AS FECHA_PEDI, COUNT(*) AS CANT_PED_PEND, SUM(TOTAL_PEDI) AS TOTAL_PEDIDOS FROM
				(
				SELECT CAST(C.FECHA_SINCRONIZADO AS DATETIME) FECHA_SINCRONIZADO, 
                CASE WHEN A.TALON_PED = '98' THEN 'MERCADO LIBRE'
                    WHEN A.TALON_PED = '99' THEN 'VTEX'	
                END CANAL,
                A.NRO_PEDIDO, A.ORDER_ID_TIENDA, UPPER(D.RAZON_SOCI) CLIENTE ,CAST(A.TOTAL_PEDI AS DECIMAL(10,0)) TOTAL_PEDI FROM GVA21 A
                INNER JOIN (SELECT * FROM RO_V_WAREHOUSE_METODO_ENVIO_VTEX WHERE WAREHOUSE = 'Central' AND METODO_ENVIO != 'FLEX') B ON A.COD_TRANSP = B.COD_TRANSP
                LEFT JOIN RO_T_ESTADO_PEDIDOS_ECOMMERCE C ON A.ORDER_ID_TIENDA = C.ORDER_ID
                LEFT JOIN GVA38 D ON A.TALON_PED = D.TALONARIO AND A.NRO_PEDIDO = D.N_COMP
                WHERE A.COD_CLIENT = '000000' AND A.FECHA_PEDI >= CAST(GETDATE() - 10 AS DATE) AND ((A.FECHA_PEDI = CAST(GETDATE() AS DATE) AND 
                TRY_CAST(A.HORA_INGRESO AS INT) <= 140000) OR A.FECHA_PEDI < CAST(GETDATE() AS DATE)) AND C.DESPACHADO IS NULL AND C.ENTREGADO IS NULL
                AND C.CANCELADO IS NULL AND C.INCOMPLETO IS NULL AND A.COD_SUCURS = '01' AND A.ESTADO != '5'
				) A";
        return $this->getDatos($sql);
    }

    public function traerDetallePedidosPendienteDespacho() {
        $sql = "SELECT CAST(C.FECHA_SINCRONIZADO AS DATETIME) FECHA_SINCRONIZADO, 
                CASE WHEN A.TALON_PED = '98' THEN 'MERCADO LIBRE'
                    WHEN A.TALON_PED = '99' THEN 'VTEX'	
                END CANAL, UPPER(SUCURSAL_ENTREGA) SUCURSAL_ENTREGA, F.FECHA_DESPACHO,
                A.NRO_PEDIDO, A.ORDER_ID_TIENDA, UPPER(D.RAZON_SOCI) CLIENTE ,CAST(A.TOTAL_PEDI AS DECIMAL(10,0)) TOTAL_PEDI FROM GVA21 A
                INNER JOIN (SELECT * FROM RO_V_WAREHOUSE_METODO_ENVIO_VTEX WHERE WAREHOUSE = 'Central' AND METODO_ENVIO != 'FLEX') B ON A.COD_TRANSP = B.COD_TRANSP
                LEFT JOIN RO_T_ESTADO_PEDIDOS_ECOMMERCE C ON A.ORDER_ID_TIENDA = C.ORDER_ID
                LEFT JOIN GVA38 D ON A.TALON_PED = D.TALONARIO AND A.NRO_PEDIDO = D.N_COMP
				LEFT JOIN RO_V_SUCURSAL_ENTREGA_VTEX E ON A.LEYENDA_3 = E.ID_SUCURSAL_ENTREGA_VTEX COLLATE Latin1_General_BIN  
				LEFT JOIN
				(
					SELECT A.COD_CLIENT, B.NRO_SUCURSAL, MAX(A.FECHA_DESPACHO) FECHA_DESPACHO FROM RO_FECHA_DESPACHO_ACTUAL A
					INNER JOIN (SELECT * FROM LAKERBIS.LOCALES_LAKERS.DBO.SUCURSALES_LAKERS WHERE CANAL IN ('FRANQUICIAS','PROPIOS') AND HABILITADO = 1 AND NRO_SUC_MADRE IS NULL) B ON A.COD_CLIENT = B.COD_CLIENT
					GROUP BY A.COD_CLIENT, B.NRO_SUCURSAL
				) F
				ON E.NRO_SUCURSAL = F.NRO_SUCURSAL
                WHERE A.COD_CLIENT = '000000' AND A.FECHA_PEDI >= CAST(GETDATE() - 10 AS DATE) AND ((A.FECHA_PEDI = CAST(GETDATE() AS DATE) AND 
                TRY_CAST(A.HORA_INGRESO AS INT) <= 140000) OR A.FECHA_PEDI < CAST(GETDATE() AS DATE)) AND C.DESPACHADO IS NULL AND C.ENTREGADO IS NULL
                AND C.CANCELADO IS NULL AND C.INCOMPLETO IS NULL AND A.COD_SUCURS = '01' AND A.ESTADO != '5'
                ORDER BY C.FECHA_SINCRONIZADO DESC";
        return $this->getDatosMultiples($sql);
    }

    // Función para obtener el resumen de productos sin stock en ML Full
    public function traerResumenProductosMlFull() {
        $sql = "SELECT COUNT(*) AS CANTIDAD_PRODUCTOS, 
                    AVG(C.STOCK_DISPONIBLE) AS PROMEDIO_STOCK 
                FROM [192.168.0.143].emsys_XLEXTRALARGE.DBO.RO_V_ML_PUBLICACIONES_PAUSADAS A
                INNER JOIN STA11 B ON A.CODIGO = B.COD_ARTICU COLLATE Latin1_General_BIN
                INNER JOIN (SELECT COD_ARTICU, STOCK_DISPONIBLE FROM STOCK_CENTRAL WHERE COD_DEPOSI = '01' AND STOCK_DISPONIBLE > 0) C 
                    ON A.CODIGO = C.COD_ARTICU";
        return $this->getDatos($sql);
    }

    // Función para obtener el detalle de los productos
    public function traerDetalleProductosMlFull() {
        $sql = "SELECT A.CODIGO, B.DESCRIPCIO, C.STOCK_DISPONIBLE STOCK_CENTRAL, 
                    A.ESTADO_FULL, A.URL_FULL, A.URL_CENTRAL 
                FROM [192.168.0.143].emsys_XLEXTRALARGE.DBO.RO_V_ML_PUBLICACIONES_PAUSADAS A
                INNER JOIN STA11 B ON A.CODIGO = B.COD_ARTICU COLLATE Latin1_General_BIN
                INNER JOIN (SELECT COD_ARTICU, STOCK_DISPONIBLE FROM STOCK_CENTRAL WHERE COD_DEPOSI = '01' AND STOCK_DISPONIBLE > 0) C 
                    ON A.CODIGO = C.COD_ARTICU";
        return $this->getDatosMultiples($sql);
    }

    // Función para obtener el resumen de pedidos pendientes de marcar como recibido en tiendas//
    public function traerPedidosDespachados() {
        $sql = "SET DATEFORMAT YMD
                SELECT MIN(CAST(FECHA_DESPACHADO AS DATE)) AS FECHA_DESPACHADO, COUNT(*) AS CANT_PED_PEND, SUM(ROUND(A.TOTAL_PEDI * 1.21, 0)) AS TOTAL_PEDIDOS 
                FROM (
                    SELECT A.FECHA_SINCRONIZADO FECHA_PEDIDO, A.NRO_PEDIDO, B.SUCURSAL_ENTREGA, CAST(A.FECHA_DESPACHADO AS DATE) FECHA_DESPACHADO, C.TOTAL_PEDI 
                    FROM RO_T_ESTADO_PEDIDOS_ECOMMERCE A
                    INNER JOIN
                    (
                        SELECT NRO_PEDIDO, SUCURSAL_ENTREGA FROM GVA21 A
                        INNER JOIN RO_V_WAREHOUSE_METODO_ENVIO_VTEX B ON A.COD_TRANSP = B.COD_TRANSP
                        LEFT JOIN RO_V_SUCURSAL_ENTREGA_VTEX C ON A.LEYENDA_3 = C.ID_SUCURSAL_ENTREGA_VTEX COLLATE Latin1_General_BIN
                        WHERE COD_CLIENT = '000000' AND METODO_ENVIO = 'TIENDA'
                    ) B ON A.NRO_PEDIDO = B.NRO_PEDIDO
                    LEFT JOIN GVA21 C ON A.NRO_PEDIDO = C.NRO_PEDIDO AND A.TALON_PED = C.TALON_PED
                    WHERE FECHA_DESPACHADO >= '2025-05-06' AND A.TALON_PED = '99'
                    AND A.FECHA_RECIBIDO_TIENDA IS NULL
                    AND A.FECHA_ENTREGADO IS NULL
                ) A";
        return $this->getDatos($sql);
    }

    // Función para obtener el detalle de pedidos pendientes de marcar como recibido en tiendas//
    public function traerDetallePedidosDespachados() {
        $sql = "SET DATEFORMAT YMD
                SELECT A.FECHA_SINCRONIZADO FECHA_PEDIDO, A.NRO_PEDIDO, A.ORDER_ID, B.SUCURSAL_ENTREGA, 
                CAST(A.FECHA_DESPACHADO AS DATE) FECHA_DESPACHADO, DATEDIFF(DAY, A.FECHA_DESPACHADO, GETDATE()) DIAS_PENDIENTE, 
                ROUND(C.TOTAL_PEDI * 1.21, 0) TOTAL_PEDI
                FROM RO_T_ESTADO_PEDIDOS_ECOMMERCE A
                INNER JOIN
                (
                    SELECT NRO_PEDIDO, ORDER_ID_TIENDA, SUCURSAL_ENTREGA FROM GVA21 A
                    INNER JOIN RO_V_WAREHOUSE_METODO_ENVIO_VTEX B ON A.COD_TRANSP = B.COD_TRANSP
                    LEFT JOIN RO_V_SUCURSAL_ENTREGA_VTEX C ON A.LEYENDA_3 = C.ID_SUCURSAL_ENTREGA_VTEX COLLATE Latin1_General_BIN
                    WHERE COD_CLIENT = '000000' AND METODO_ENVIO = 'TIENDA'
                ) B ON A.NRO_PEDIDO = B.NRO_PEDIDO AND A.ORDER_ID = B.ORDER_ID_TIENDA
                LEFT JOIN GVA21 C ON A.NRO_PEDIDO = C.NRO_PEDIDO AND A.TALON_PED = C.TALON_PED
                WHERE FECHA_DESPACHADO >= '2025-05-06' AND A.TALON_PED = '99'
                AND A.FECHA_RECIBIDO_TIENDA IS NULL
                AND A.FECHA_ENTREGADO IS NULL
                ORDER BY A.FECHA_DESPACHADO";
        return $this->getDatosMultiples($sql);
    }

    // Función para obtener el resumen de pedidos pendientes de control
    public function traerResumenPedidosPendientesControl() {
        $sql = "SELECT COUNT(*) AS CANTIDAD_PEDIDOS, 
                    MIN(FECHA_SINCRONIZADO) AS FECHA_MAS_ANTIGUA
                FROM RO_T_ESTADO_PEDIDOS_ECOMMERCE A
                INNER JOIN GVA21 B ON A.TALON_PED = B.TALON_PED AND A.NRO_PEDIDO = B.NRO_PEDIDO
                WHERE CONTROLADO IS NULL AND A.CANCELADO IS NULL AND A.FECHA_PEDI >= GETDATE()-14";
        return $this->getDatos($sql);
    }

    // Función para obtener el detalle de pedidos pendientes de control
    public function traerDetallePedidosPendientesControl() {
        $sql = "SELECT FECHA_SINCRONIZADO, 
                    CASE WHEN A.TALON_PED = '98' THEN 'MERCADO LIBRE'
                            WHEN A.TALON_PED = '99' THEN 'VTEX' 
                            WHEN A.TALON_PED = '80' THEN 'ICBC' 
                    END CANAL,
                    A.NRO_PEDIDO, ORDER_ID, UPPER(D.RAZON_SOCI) CLIENTE, 
                    ISNULL(C.SUCURSAL_ENTREGA, 'CENTRAL') SUCURSAL_PREPARA
                FROM RO_T_ESTADO_PEDIDOS_ECOMMERCE A
                INNER JOIN GVA21 B ON A.TALON_PED = B.TALON_PED AND A.NRO_PEDIDO = B.NRO_PEDIDO
                LEFT JOIN RO_V_STA22 C ON B.COD_SUCURS = C.COD_SUCURS  
                LEFT JOIN GVA38 D ON A.TALON_PED = D.TALONARIO AND A.NRO_PEDIDO = D.N_COMP
                WHERE A.CONTROLADO IS NULL AND A.FECHA_PEDI >= GETDATE()-14 AND A.CANCELADO IS NULL
                ORDER BY FECHA_SINCRONIZADO";
        return $this->getDatosMultiples($sql);
    }

    // Función para obtener el ranking de pedidos pendientes de control por sucursal
    public function traerRankingPedidosPendientesControlPorSucursal() {
        $sql = "WITH PedidosPorSucursal AS (
                    SELECT 
                        ISNULL(C.SUCURSAL_ENTREGA, 'CENTRAL') AS SUCURSAL,
                        COUNT(*) AS CANTIDAD_PEDIDOS
                    FROM RO_T_ESTADO_PEDIDOS_ECOMMERCE A
                    INNER JOIN GVA21 B ON A.TALON_PED = B.TALON_PED AND A.NRO_PEDIDO = B.NRO_PEDIDO
                    LEFT JOIN RO_V_STA22 C ON B.COD_SUCURS = C.COD_SUCURS  
                    WHERE A.CONTROLADO IS NULL 
                    AND A.FECHA_PEDI >= GETDATE()-14
                    AND A.CANCELADO IS NULL
                    GROUP BY ISNULL(C.SUCURSAL_ENTREGA, 'CENTRAL')
                ),
                TotalPedidos AS (
                    SELECT SUM(CANTIDAD_PEDIDOS) AS TOTAL FROM PedidosPorSucursal
                )
                SELECT 
                    P.SUCURSAL,
                    P.CANTIDAD_PEDIDOS,
                    CAST(ROUND((P.CANTIDAD_PEDIDOS * 100.0 / T.TOTAL), 1) AS DECIMAL(5,1)) AS PORCENTAJE
                FROM PedidosPorSucursal P
                CROSS JOIN TotalPedidos T
                ORDER BY P.CANTIDAD_PEDIDOS DESC, P.SUCURSAL";
        return $this->getDatosMultiples($sql);
    }
}
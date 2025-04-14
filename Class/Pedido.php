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
            EXEC RO_ECOMMERCE_PEDIDOS '$desde', '$hasta', '%$tienda', '%$warehouse', '$estado', '$orden'

            ";

            $array = $this->getDatos($sql);    

            return $array;
        }

        public function traerWarehouse(){

                
            $sql = "SELECT WAREHOUSE FROM
                    (
                    SELECT REPLACE(NOMBRE_SUC, 'RT - SUC - ', '') WAREHOUSE FROM STA22
                    WHERE NOMBRE_SUC LIKE 'RT%'
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
        
        
    }
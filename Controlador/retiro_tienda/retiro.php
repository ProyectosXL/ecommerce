<?php
// echo 'aca estoy';

function retiroTienda(){

    
    date_default_timezone_set('UTC');
    include 'metodos.php';
    include 'metodos_vtex.php';
    include 'nuevo_ml_rt.php';
    
    ///////////////////////////////////////// PEDIDOS NUEVOS
    
    $buscar = sqlsrv_prepare($cid_central, $buscar_nuevos);
    sqlsrv_execute($buscar);
    
    //aca recorre todos los pedidos nuevos
    
    while($v=sqlsrv_fetch_array($buscar)){
        
        
        $fecha_pedi = $fecha;
        $nroPedido = $v['NRO_ORDEN_ECOMMERCE'];
        $numSuc = $v['NRO_SUCURS'];
        $tienda = $v['TIENDA'];
        
        if($tienda=='VTEX'){
            $string = string_conexion($numSuc);
            print_r($string);   
            echo $nroPedido.'<br>';
            $mail = 0;
            $mail = enviarMail($string['MAIL'], $nroPedido);
            echo 'mails enviados: '.$mail.'<br>';
            $articulos = articulosPorPedido($nroPedido);
            
            foreach ($articulos as $key => $value) {
                echo '<br>';
                echo '<-----------------------------------------------------------------------------------------------------><br>';
                $codArticu = $value['COD_ARTICU'];
                $cant = $value['CANT_PEDID'];
                $renglon = 1;
                echo $codArticu.' '.$cant.' '.$renglon.'<br>';
                echo 'DESDE ACA TODO EL LOCAL<br>';
                modificarStockLocal($codArticu, $cant, 0, $string);
                $proxtalonario = proximoTalonario($string);
                $proxInterno = proximoInterno($string);
                remitoLocalEncabezado($string, $proxtalonario, $proxInterno, $nroPedido);
                remitoLocalDetalle($string, $proxInterno, $codArticu, $cant, $renglon);
                echo 'HASTA ACA TODO EL LOCAL<br>';
                echo '----------------------------------------------------------------<br>';
                echo '----------------------------------------------------------------<br>';
                echo 'DESDE ACA TODO CENTRAL<br>';
                remitoRecibirEncabezadoCta($array_central, $proxtalonario, $proxInterno, $numSuc, $nroPedido);
                remitoRecibirDetalleCta($array_central, $proxInterno, $codArticu, $cant, $renglon, $numSuc);
                
                remitoRecibirEncabezadoSta($array_central, $proxtalonario, $proxInterno);
                remitoRecibirDetalleSta($array_central, $proxInterno, $codArticu, $cant, $renglon);
                
                modificarStock($codArticu, 'RT', $cant, 1, $array_central);
                echo 'HASTA ACA TODO CENTRAL<br>';
                echo '<-----------------------------------------------------------------------------------------------------><br>';
            }
            marcarPedidoEnviado($array_central, $nroPedido);
            
        }elseif($tienda=='ML'){
            echo '<-----------------------------------------------------------------------------------------------------><br>';
            echo '<----------------PEDIDOS DE MERCADO LIBRE------------------------------------------><br>';
            ##### A PARTIR DE ACA 
            echo $fecha_pedi.'-'.$nroPedido.'-'.$numSuc.'-'.$tienda;
            $string = string_conexion($numSuc);
            print_r($string);   
            echo $nroPedido.'<br>';
            $mail = 0;
            $mail = enviarMail($string['MAIL'], $nroPedido);
            echo 'mails enviados: '.$mail.'<br>';
            new_ml_rt($nroPedido);
            echo '<br><-----------------------------------------------------------------------------------------------------><br>';
        }
        
    }
    
	
}
?>
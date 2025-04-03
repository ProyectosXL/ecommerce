<?php
function new_ml_rt($a){

	require_once 'Class/Conexion.php';
	$cid = new Conexion();
	$cid_central = $cid->conectarSql('central');

	if (!$cid_central){exit("<strong>Ha ocurrido un error tratando de conectarse con el origen de datos.</strong>");}
	
	
	//////////////TOMA CADA UNO DE LOS PEDIDOS QUE NO ESTAN EN AUDITORIA
	$sqlPedidosNuevos = "
	SELECT 
	*
	FROM [192.168.0.143].emsys_XLEXTRALARGE.DBO.SOF_PEDIDO 
	WHERE CAST(NRO_PEDIDO COLLATE Latin1_General_BIN AS VARCHAR) NOT IN (SELECT NRO_ORDEN_ECOMMERCE FROM SOF_AUDITORIA)
	AND 
	CAST(NRO_PEDIDO AS VARCHAR) IN
	(
	SELECT 
	DISTINCT
	NRO_PEDIDO
	FROM [192.168.0.143].emsys_XLEXTRALARGE.DBO.SOF_DETALLE_PEDIDO 
	WHERE CAST(NRO_PEDIDO COLLATE Latin1_General_BIN AS VARCHAR) 
	NOT IN (SELECT NRO_ORDEN_ECOMMERCE FROM SOF_AUDITORIA)
	AND (CODIGO IS NOT NULL OR CODIGO != '')
	AND [Receiver_Address_Address_Line] like 'RETIRO%'
	)
	AND NRO_PEDIDO = '$a'
	";


	ini_set('max_execution_time', 300);
	$result=sqlsrv_query($cid_central,$sqlPedidosNuevos)or die(exit("Error en odbc_exec"));
		
	while($v=sqlsrv_fetch_array($result)){
		$ordenEcommerce = $v['NRO_PEDIDO'];
		$cliente = $v['First_Name'].' '.$v['Last_Name'];
		$cliente = str_replace("'","''", $cliente);
		$totalPedi = $v['IMPORTE'];
		$codPostal = $v['Receiver_Address_Zip_Code'];
		$direccion = $v['Receiver_Address_Address_Line'].' '.$v['Receiver_Address_Comment'];
		$direccion = substr($direccion, 0, 30);
		$localidad = $v['Receiver_Address_City_Name'];
		$localidad = substr($localidad, 0, 20);
		$dni = $v['Billing_Doc_Number'];
		$telefono = $v['Phone_Number'];
		$sucursal_ml = $v['SUCURSAL_ML'];
		$nro_sucurs = $v['NRO_SUCURS'];
		$full_filment = $v['mercado_envio_full'];
		
		$direccion_completa = $direccion.' '.$codPostal.' '.$localidad;
		
		
		$sqlAuditoria =
		"
		SET DATEFORMAT YMD
		
		INSERT INTO SOF_AUDITORIA
		(
		ORIGEN, NRO_ORDEN_ECOMMERCE, NRO_PEDIDO, FECHA_PEDIDO, RAZON_SOCIAL, COD_ARTICULO, DESCRIPCION, CANTIDAD_A_FACTURAR, 
		TOTAL_PEDIDO, NOMBRE_MEDIO_PAGO, IMPORTE_PAGO, OBSERV_AUDITORIA, 
		NRO_COMP, NRO_TRACKING, OBSERV_TRACKING, ID_MERCADOPAGO, LOCAL_ENTREGA, LOCAL_ML, FULL_FILMENT
		)
		SELECT 
		'ML', NRO_PEDIDO, 'SIN PEDIDO', Date_Created, '$cliente', CODIGO, LEFT(Item_Title, 30), Quantity, Unit_Price, 
		'MERCADOPAGO', Unit_Price, 'MERCADOPAGO', 'RETIRO TIENDA', 0, NULL, NULL, 'RETIRO EN TIENDA - $sucursal_ml', '$nro_sucurs', MERCADO_ENVIO_FULL
		FROM [192.168.0.143].emsys_XLEXTRALARGE.DBO.SOF_DETALLE_PEDIDO 
		WHERE CAST(NRO_PEDIDO COLLATE Latin1_General_BIN AS VARCHAR) 
		NOT IN (SELECT NRO_ORDEN_ECOMMERCE FROM SOF_AUDITORIA)
		AND NRO_PEDIDO = '$ordenEcommerce'
		";
	
		// echo $sqlAuditoria;
		sqlsrv_query($cid_central,$sqlAuditoria)or die(exit("Error en odbc_exec"));
		
	}

}

?>
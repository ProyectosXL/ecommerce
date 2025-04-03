<?php
function new_daf(){

	require_once 'Class/Conexion.php';
	$cid = new Conexion();
	$cid_central = $cid->conectarSql('central');


	if (!$cid_central){exit("<strong>Ha ocurrido un error tratando de conectarse con el origen de datos.</strong>");}
	
	
	//////////////TOMA CADA UNO DE LOS PEDIDOS QUE NO ESTAN EN AUDITORIA
	$sqlPedidosNuevos = "
	
	SELECT 

	[Seller SKU],COUNT([Seller SKU]) CANT,[Dafiti SKU],[Created at],[Updated at],[Order Number],[Order Source],[Order Currency],REPLACE([Customer Name], '''', '''''') [Customer Name],[National Registration Number],[Shipping Name],
	[Shipping Address],[Shipping Address2],[Shipping Address3],[Shipping Address4],[Shipping Address5],[Shipping City],[Shipping Postcode],[Shipping Country],[Billing Name],[Billing Address],
	[Billing Address2],[Billing Address3],[Billing Address4],[Billing Address5],[Billing City],[Billing Postcode],[Billing Country],[Payment Method],SUM(CAST([Paid Price] AS decimal (10,2)))[Paid Price],[Unit Price],[Shipping Fee],
	[Wallet Credits],[Item Name],[Variation],[CD Shipping Provider],[Shipping Provider],[Shipment Type Name],[Shipping Provider Type],[CD Tracking Code],[Tracking Code],[Tracking URL],
	[Shipping Provider (first mile)],[Tracking Code (first mile)],[Tracking URL (first mile)],[Promised shipping time],[Premium],[Status],[Reason] 
	FROM
	(
		SELECT * FROM SJ_DAFITI_PEDIDO
		WHERE [Order Number] COLLATE Latin1_General_BIN NOT IN 
		(SELECT NRO_ORDEN_ECOMMERCE FROM SOF_AUDITORIA)	
	)A
	--WHERE [Order Number] COLLATE Latin1_General_BIN  = '258743342'
	GROUP BY 
	[Seller SKU],[Dafiti SKU],[Created at],[Updated at],[Order Number],[Order Source],[Order Currency],[Customer Name],[National Registration Number],[Shipping Name],[Shipping Address],
	[Shipping Address2],[Shipping Address3],[Shipping Address4],[Shipping Address5],[Shipping City],[Shipping Postcode],[Shipping Country],[Billing Name],[Billing Address],
	[Billing Address2],[Billing Address3],[Billing Address4],[Billing Address5],[Billing City],[Billing Postcode],[Billing Country],[Payment Method],[Unit Price],
	[Shipping Fee],[Wallet Credits],[Item Name],[Variation],[CD Shipping Provider],[Shipping Provider],[Shipment Type Name],[Shipping Provider Type],[CD Tracking Code],[Tracking Code],
	[Tracking URL],[Shipping Provider (first mile)],[Tracking Code (first mile)],[Tracking URL (first mile)],[Promised shipping time],[Premium],[Status],[Reason] 

	";

	ini_set('max_execution_time', 300);
	$result1=sqlsrv_query($cid_central,$sqlPedidosNuevos)or die(exit("Error en odbc_exec"));
		
	while($v=sqlsrv_fetch_array($result1)){
		$ordenEcommerce = $v['Order Number'];
		$cliente = $v['Customer Name'];
		$cliente = str_replace("'","''", $cliente);
		$totalPedi = $v['Paid Price'];
		$codPostal = $v['Billing Postcode'];

		$direccion = $v['Billing Address'].' '.$v['Billing Address2'].' '.$v['Billing City'];
		$direccion = substr($direccion, 0, 30);
		$direccion = str_replace("'","''", $direccion);

		$localidad = $v['Billing City'];
		$localidad = substr($localidad, 0, 20);
		$localidad = str_replace("'","''", $localidad);

		$dni = $v['National Registration Number'];
		$telefono = '';
		
		$direccion_completa = $direccion.' '.$codPostal.' '.$localidad;
		
		//////////////MODIFICA NUMERACION
		$sqlProxNumero =
		"EXEC SJ_PEDIDOS_PROX";
		sqlsrv_query($cid_central,$sqlProxNumero)or die(exit("Error en odbc_exec"));
		
		
		//////////////VARIABLE DE NUMERO ACTUAL
		$sqlNumActual =
		"SELECT PROXIMO FROM SJ_TEMP_PEDIDOS WHERE TALONARIO = 98";
		
		ini_set('max_execution_time', 300);
		$result=sqlsrv_query($cid_central,$sqlNumActual)or die(exit("Error en odbc_exec"));
			
		while($v=sqlsrv_fetch_array($result)){
			$numPedido = ' 00002'.$v['PROXIMO'];
		}
		
		
		
		
		
		
		//////////////INSERTAR ENCABEZADO GVA21
		
		$sqlPedEncabezado =
		"
		INSERT INTO GVA21 
		(
		FILLER, APRUEBA, CIRCUITO, COD_CLIENT, COD_SUCURS, COD_TRANSP, COD_VENDED, COMENTARIO, COMP_STK, COND_VTA, COTIZ, ESTADO, EXPORTADO, FECHA_APRU, FECHA_ENTR, FECHA_PEDI, HORA_APRUE, ID_EXTERNO, 
		LEYENDA_1, LEYENDA_2, LEYENDA_3, LEYENDA_4, LEYENDA_5, MON_CTE, N_LISTA, N_REMITO, NRO_O_COMP, NRO_PEDIDO, NRO_SUCURS, ORIGEN, PORC_DESC, REVISO_FAC, REVISO_PRE, REVISO_STK, TALONARIO, TALON_PED, 
		TOTAL_PEDI, TIPO_ASIEN, MOTIVO, HORA, COD_CLASIF, ID_ASIENTO_MODELO_GV, TAL_PE_ORI, NRO_PE_ORI, FECHA_INGRESO, HORA_INGRESO, USUARIO_INGRESO,TERMINAL_INGRESO, FECHA_ULTIMA_MODIFICACION, 
		HORA_ULTIMA_MODIFICACION, USUA_ULTIMA_MODIFICACION, TERM_ULTIMA_MODIFICACION, ID_DIRECCION_ENTREGA, ES_PEDIDO_WEB, FECHA_O_COMP, ACTIVIDAD_COMPROBANTE_AFIP, ID_ACTIVIDAD_EMPRESA_AFIP, 
		TIPO_DOCUMENTO_PAGADOR, NUMERO_DOCUMENTO_PAGADOR, WEB_ORDER_ID, NRO_OC_COMP, USUARIO_TIENDA, TIENDA, ORDER_ID_TIENDA, 
		TOTAL_DESC_TIENDA, TIENDA_QUE_VENDE, PORCEN_DESC_TIENDA, USUARIO_TIENDA_VENDEDOR, ID_NEXO_PEDIDOS_ORDEN
		)
		VALUES
		(
		'', '', 1, '000000', '12', '**', 'ZZ', '', 1, 1, 1, 2, 0, '1800-01-01', '1800-01-01', CAST(GETDATE() AS DATE), '', '$ordenEcommerce', 
		'', '$cliente', 'DAFITI CARGADO DESDE APP ECOMMERCE', '', '', 1, 20, ' 0000000000000', '', '$numPedido', 0, 'E', 0, 'A', 'A', 'A', 0, 101,
		$totalPedi, 'FD', '', '', '', 3, 0, '', '1800-01-01', '', '', '', '1800-01-01',
		'', '', '', '', 0, '1800-01-01', '', NULL, 
		0, '', 0, '', '', '', '', 
		0, '', 0, '', ''
		)
		";
		
		sqlsrv_query($cid_central,$sqlPedEncabezado)or die(exit("Error en odbc_exec"));
		
		
		
		
		////////////// RECORRER DETALLE DE PEDIDO PARA INSERTAR DETALLE

		$sqlPedidoDetalle =
		"
		SELECT 
		[Seller SKU],COUNT([Seller SKU]) CANT,[Dafiti SKU],[Created at],[Updated at],[Order Number],[Order Source],[Order Currency],[Customer Name],[National Registration Number],[Shipping Name],
		[Shipping Address],[Shipping Address2],[Shipping Address3],[Shipping Address4],[Shipping Address5],[Shipping City],[Shipping Postcode],[Shipping Country],[Billing Name],[Billing Address],
		[Billing Address2],[Billing Address3],[Billing Address4],[Billing Address5],[Billing City],[Billing Postcode],[Billing Country],[Payment Method],[Paid Price],[Unit Price],[Shipping Fee],
		[Wallet Credits],[Item Name],[Variation],[CD Shipping Provider],[Shipping Provider],[Shipment Type Name],[Shipping Provider Type],[CD Tracking Code],[Tracking Code],[Tracking URL],
		[Shipping Provider (first mile)],[Tracking Code (first mile)],[Tracking URL (first mile)],[Promised shipping time],[Premium],[Status],[Reason] 
		FROM
		(
			SELECT * FROM SJ_DAFITI_PEDIDO
			WHERE [Order Number] COLLATE Latin1_General_BIN NOT IN 
			(SELECT NRO_ORDEN_ECOMMERCE FROM SOF_AUDITORIA)	
		)A
		WHERE [Order Number] COLLATE Latin1_General_BIN  = '$ordenEcommerce'
		GROUP BY 
		[Seller SKU],[Dafiti SKU],[Created at],[Updated at],[Order Number],[Order Source],[Order Currency],[Customer Name],[National Registration Number],[Shipping Name],[Shipping Address],
		[Shipping Address2],[Shipping Address3],[Shipping Address4],[Shipping Address5],[Shipping City],[Shipping Postcode],[Shipping Country],[Billing Name],[Billing Address],
		[Billing Address2],[Billing Address3],[Billing Address4],[Billing Address5],[Billing City],[Billing Postcode],[Billing Country],[Payment Method],[Paid Price],[Unit Price],
		[Shipping Fee],[Wallet Credits],[Item Name],[Variation],[CD Shipping Provider],[Shipping Provider],[Shipment Type Name],[Shipping Provider Type],[CD Tracking Code],[Tracking Code],
		[Tracking URL],[Shipping Provider (first mile)],[Tracking Code (first mile)],[Tracking URL (first mile)],[Promised shipping time],[Premium],[Status],[Reason] 



		
		";
		$resultPedidoDetalle=sqlsrv_query($cid_central,$sqlPedidoDetalle)or die(exit("Error en sqlsrv_query"));
		
		$renglonPedido = 1;
		
		while($v=odbc_fetch_array($resultPedidoDetalle)){
			$codArt = $v['Seller SKU'];
			$cantArt = $v['CANT'];
			$precioArt = $v['Unit Price'];
			
			
			////////////// INSERTAR DETALLE DE PEDIDO GVA03
			
			$sqlDetalle = 
			"
			INSERT INTO GVA03
			(
			FILLER, CAN_EQUI_V, CANT_A_DES, CANT_A_FAC, CANT_PEDID, CANT_PEN_D, CANT_PEN_F, COD_ARTICU, DESCUENTO, N_RENGLON, NRO_PEDIDO, PEN_REM_FC, PEN_FAC_RE, PRECIO, TALON_PED, COD_CLASIF, CANT_A_DES_2, 
			CANT_A_FAC_2, CANT_PEDID_2, CANT_PEN_D_2, CANT_PEN_F_2, PEN_REM_FC_2, PEN_FAC_RE_2, ID_MEDIDA_VENTAS, ID_MEDIDA_STOCK_2,ID_MEDIDA_STOCK, UNIDAD_MEDIDA_SELECCIONADA, COD_ARTICU_KIT, RENGL_PADR, 
			PROMOCION,PRECIO_ADICIONAL_KIT, KIT_COMPLETO, INSUMO_KIT_SEPARADO, PRECIO_LISTA, PRECIO_BONIF, DESCUENTO_PARAM, PRECIO_FECHA, FECHA_MODIFICACION_PRECIO, USUARIO_MODIFICACION_PRECIO, 
			TERMINAL_MODIFICACION_PRECIO, ID_NEXO_PEDIDOS_RENGLON_ORDEN
			)
			VALUES
			(
			'', 1, $cantArt, $cantArt, $cantArt, $cantArt, $cantArt, '$codArt', 0, $renglonPedido, '$numPedido', 0, 0, $precioArt, 101, '', 0, 
			0, 0, 0, 0, 0, 0, 7, NULL, 7, 'V', '', 0, 
			0, 0, 0, 0, 0, 0, 0, '1800-01-01', '1800-01-01', '', 
			'', NULL
			)
			";
			if(!@sqlsrv_query($cid_central,$sqlDetalle)){
				echo '<div class="alert alert-danger text-center" role="alert">ERROR CARGANDO DETALLE DE DAFITI!</div>';
			};//or die(exit("Error en sqlsrv_query"));
			
			$renglonPedido++;
			
		}
		
		////////////// INSERTAR DATOS DEL CLIENTE GVA38
		
		//echo $codPostal.' '.$direccion.' '.$localidad.' '.$numPedido.' '.$dni.' '.$cliente.' '.$telefono.' '.$direccion_completa.'<br>';
		//echo $localidad.' '.$codPostal.' '.$telefono;
		
		$sqlDatosCliente = 
		"
		SET DATEFORMAT YMD
		
		INSERT INTO GVA38
		(
		FILLER, ALI_ADI_IB, ALI_FIJ_IB, ALI_NOCATE, AL_FIJ_IB3, COD_PROVIN, C_POSTAL, DOMICILIO, E_MAIL, IB_L, IB_L3, II_D, II_IB3, II_L,  IVA_D, IVA_L, LOCALIDAD, N_COMP, N_CUIT, N_ING_BRUT, N_IVA, 
		PORC_EXCL, RAZON_SOCI, SOBRE_II, SOBRE_IVA, TALONARIO, TELEFONO_1, TELEFONO_2, TIPO, TIPO_DOC, T_COMP, DESTINO_DE, CLA_IMP_CL, RECIBE_DE, AUT_DE, WEB, COD_RUBRO, CTA_CLI, CTO_CLI, IDENTIF_AFIP, 
		DIRECCION_ENTREGA, CIUDAD_ENTREGA, COD_PROVINCIA_ENTREGA, LOCALIDAD_ENTREGA, CODIGO_POSTAL_ENTREGA, TELEFONO1_ENTREGA, TELEFONO2_ENTREGA, ID_CATEGORIA_IVA, CONSIDERA_IVA_BASE_CALCULO_IIBB, 
		CONSIDERA_IVA_BASE_CALCULO_IIBB_ADIC, MAIL_DE, FECHA_NACIMIENTO, SEXO
		)
		VALUES
		(
		'', 0, 0, 0, 0, '02', '$codPostal', '$direccion', '', 'N', 0, 'N', 0, 'N', 'N', 'S', '$localidad', '$numPedido', '$dni', '', '',
		0, '$cliente', 'N', 'N', 101, '$telefono', '', '', 96, 'PED', 'T', '', 0, 0, '', '', 0, '', '', 
		'$direccion', '$localidad', '02', '$localidad', '$codPostal', '$telefono', '$ordenEcommerce', 2, 'N', 'N', NULL, '1800-01-01', NULL
		)
		";
		// echo $sqlDatosCliente;
		// return;
		sqlsrv_query($cid_central,$sqlDatosCliente)or die(exit("Error en sqlsrv_query"));
		
		
		////////////// INSERTAR DATOS EN AUDITORIA
		
		$sqlAuditoria =
		"
		SET DATEFORMAT YMD
		
		INSERT INTO SOF_AUDITORIA
		(
		ORIGEN, NRO_ORDEN_ECOMMERCE, NRO_PEDIDO, FECHA_PEDIDO, RAZON_SOCIAL, COD_ARTICULO, DESCRIPCION, CANTIDAD_A_FACTURAR, TOTAL_PEDIDO, NOMBRE_MEDIO_PAGO, IMPORTE_PAGO, OBSERV_AUDITORIA, 
		NRO_COMP, NRO_TRACKING, OBSERV_TRACKING, ID_MERCADOPAGO
		)
		SELECT 
		'DAF', [Order Number], '$numPedido', CAST(GETDATE() AS date), '$cliente', [Seller SKU], LEFT(B.DESCRIPCIO, 30), [CANT], [Unit Price], 'Pay U', [Paid Price], 'Pay U', NULL, 0, NULL, NULL
		FROM (SELECT [Order Number], [Created at], [Seller SKU], COUNT([Seller SKU])CANT, [Unit Price], SUM(CAST([Paid Price] AS decimal(10,2)))[Paid Price] FROM SJ_DAFITI_PEDIDO
		GROUP BY [Order Number], [Created at], [Seller SKU], [Unit Price]) A
		INNER JOIN STA11 B
		ON A.[Seller SKU] collate Latin1_General_BIN = B.COD_ARTICU
		WHERE CAST([Order Number] AS VARCHAR) collate Latin1_General_BIN
		NOT IN (SELECT NRO_ORDEN_ECOMMERCE FROM SOF_AUDITORIA)
		AND [Order Number] collate Latin1_General_BIN = '$ordenEcommerce'
		";
		
		sqlsrv_query($cid_central,$sqlAuditoria)or die(exit("Error en sqlsrv_query"));
		
	}

}


?>
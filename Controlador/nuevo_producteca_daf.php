<?php
function new_producteca_daf(){

	require_once 'Class/Conexion.php';
	$cid = new Conexion();
	$cid_central = $cid->conectarSql('central');


	if (!$cid_central){exit("<strong>Ha ocurrido un error tratando de conectarse con el origen de datos.</strong>");}
	
	
	//////////////TOMA CADA UNO DE LOS PEDIDOS QUE NO ESTAN EN AUDITORIA
	$sqlPedidosNuevos = "
	
	SELECT * FROM NEXO_PEDIDOS_ORDEN A
	INNER JOIN NEXO_PEDIDOS_CLIENTES B
	ON A.WEB_CLIENT_ID = B.WEB_CLIENT_ID
	WHERE ORDER_ID_TIENDA NOT IN
	(SELECT NRO_ORDEN_ECOMMERCE COLLATE Latin1_General_BIN FROM SOF_AUDITORIA)
	AND A.TIENDA = 'API'
	AND A.NOTA_COMPRADOR = 'Integracion Producteca Dafiti'
	AND A.FECHA_ORDEN >= GETDATE()-7

	";



	ini_set('max_execution_time', 300);
	$result1=sqlsrv_query($cid_central,$sqlPedidosNuevos)or die(exit("Error en sqlsrv_query"));
		
	while($v=sqlsrv_fetch_array($result1)){

		$ordenEcommerce = $v['ORDER_ID_TIENDA'];
		$idPedido = $v['ID_NEXO_PEDIDOS_ORDEN'];
		$cliente = $v['RAZON_SOCIAL'];
		$totalPedi = $v['TOTAL_ORDEN'];
		$codPostal = $v['CODIGO_POSTAL'];
		$direccion = $v['DOMICILIO'].'; '.$v['DIRECCION_COMERCIAL'].' '.$v['CODIGO_POSTAL'];
		$direccion = substr($direccion, 0, 30);
		$localidad = $v['LOCALIDAD'];
		$localidad = substr($localidad, 0, 20);
		$dni = $v['NUMERO_DOCUMENTO'];
		$telefono = '';
		
		
		$direccion_completa = $direccion.' '.$codPostal.' '.$localidad;
		
		//////////////MODIFICA NUMERACION
		$sqlProxNumero =
		"EXEC SJ_PEDIDOS_PROX";
		sqlsrv_query($cid_central,$sqlProxNumero)or die(exit("Error en sqlsrv_query"));
		
		
		//////////////VARIABLE DE NUMERO ACTUAL
		$sqlNumActual =
		"SELECT PROXIMO FROM SJ_TEMP_PEDIDOS WHERE TALONARIO = 98";
		
		ini_set('max_execution_time', 300);
		$result=sqlsrv_query($cid_central,$sqlNumActual)or die(exit("Error en sqlsrv_query"));
			
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
		'', '$cliente', 'DAFITI - INTEGRACION CON PRODUCTECA', '', '', 1, 20, ' 0000000000000', '', '$numPedido', 0, 'E', 0, 'A', 'A', 'A', 0, 101,
		$totalPedi, 'FD', '', '', '', 3, 0, '', '1800-01-01', '', '', '', '1800-01-01',
		'', '', '', '', 0, '1800-01-01', '', NULL, 
		0, '', 0, '', '', '', '', 
		0, '', 0, '', ''
		)
		";



		sqlsrv_query($cid_central,$sqlPedEncabezado)or die(exit("Error en sqlsrv_query"));
		
		
		
		////////////// RECORRER DETALLE DE PEDIDO PARA INSERTAR DETALLE
		
		$sqlPedidoDetalle =
		"
		SELECT A.* FROM NEXO_PEDIDOS_RENGLON_ORDEN A
		INNER JOIN STA11 B
		ON A.COD_STA11 COLLATE Latin1_General_BIN = B.COD_ARTICU
		WHERE ID_NEXO_PEDIDOS_ORDEN = $idPedido		
		";
		
		$resultPedidoDetalle=sqlsrv_query($cid_central,$sqlPedidoDetalle)or die(exit("Error en sqlsrv_query"));
		
		$renglonPedido = 1;
		
		while($v=sqlsrv_fetch_array($resultPedidoDetalle)){
			$codArt = $v['COD_STA11'];
			$cantArt = $v['CANTIDAD_PEDIDA'];
			$precioArt = $v['PRECIO_UNITARIO'];
			
			
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
		'DAF', '$ordenEcommerce', '$numPedido', CAST(GETDATE() AS date), '$cliente', C.COD_STA11, LEFT(D.DESCRIPCIO, 30), [CANTIDAD_PEDIDA], PRECIO_UNITARIO, 'Pay U', TOTAL_ORDEN, 'Pay U', NULL, 0, NULL, NULL

		FROM NEXO_PEDIDOS_ORDEN A
		INNER JOIN NEXO_PEDIDOS_CLIENTES B
		ON A.WEB_CLIENT_ID = B.WEB_CLIENT_ID
		INNER JOIN NEXO_PEDIDOS_RENGLON_ORDEN C
		ON A.ID_NEXO_PEDIDOS_ORDEN = C.ID_NEXO_PEDIDOS_ORDEN
		INNER JOIN STA11 D
		ON C.COD_STA11 COLLATE Latin1_General_BIN = D.COD_ARTICU
		WHERE ORDER_ID_TIENDA NOT IN
		(SELECT NRO_ORDEN_ECOMMERCE COLLATE Latin1_General_BIN FROM SOF_AUDITORIA)
		AND A.TIENDA = 'API'
		AND A.ORDER_ID_TIENDA = '$ordenEcommerce'
		";


		
		sqlsrv_query($cid_central,$sqlAuditoria)or die(exit("Error en sqlsrv_query"));

		
		
	}

}


?>
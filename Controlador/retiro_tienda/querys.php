<?php

//nuevos pedidos
$buscar_nuevos = "
SET DATEFORMAT YMD

SELECT TOP 1 * FROM
(
                --VTEX, PERO NO VA MAS
                /*
                SELECT cast(A.FECHA_PEDI as date) FECHA_PEDI, B.ORDER_ID NRO_ORDEN_ECOMMERCE, B.NRO_SUCURSAL NRO_SUCURS, 'VTEX' TIENDA
                FROM
                (
                    SELECT * FROM GVA21 WHERE COD_SUCURS = 'RT' AND ESTADO = 2 AND LEYENDA_5 = ''
                )A
                INNER JOIN GC_ECOMMERCE_ORDER B
                ON A.ID_EXTERNO = B.ORDER_ID

                UNION ALL
                */    


    SELECT CAST(DATE_CREATED AS DATE) FECHA_PEDI , NRO_PEDIDO COLLATE Latin1_General_BIN NRO_ORDEN_ECOMMERCE, NRO_SUCURS, 'ML' TIENDA
    FROM [192.168.0.143].emsys_XLEXTRALARGE.DBO.SOF_PEDIDO 
    WHERE NRO_PEDIDO NOT IN (SELECT NRO_ORDEN_ECOMMERCE COLLATE Latin1_General_BIN FROM SOF_AUDITORIA)
    AND DATE_CREATED >= GETDATE()-75
    AND SUCURSAL_ML != '' AND NRO_SUCURS != ''
    AND [Receiver_Address_Address_Line] like 'RETIRO%'
)A

";





?>

<?php

require_once $_SERVER['DOCUMENT_ROOT']. '/ecommerce/Class/Conexion.php';

class Remito {
    
    private function getDatos($sql) {
        $cid = new Conexion();
        $cid_central = $cid->conectarSql('central');

        ini_set('max_execution_time', 300);
        $result = sqlsrv_query($cid_central, $sql) or die(exit("Error en sqlsrv_query"));

        $data = [];
        while($v = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
            // Convertir fechas a formato string para evitar problemas con objetos DateTime
            if (isset($v['FECHA_MOV']) && is_object($v['FECHA_MOV'])) {
                $v['FECHA_MOV'] = $v['FECHA_MOV']->format('Y-m-d');
            }
            $data[] = $v;
        }
        
        sqlsrv_close($cid_central);
        return $data;
    }

    public function obtenerRemitos($fechaDesde = null, $fechaHasta = null, $estado = null) {
        $whereConditions = [];
        
        // Condiciones base
        $whereConditions[] = "A.COD_PRO_CL IN ('GTWEB', 'GTMELI')";
        $whereConditions[] = "A.ESTADO_MOV != 'A'";
        $whereConditions[] = "RENGL_PADR != 0";
        
        // Filtro por fecha
        if ($fechaDesde && $fechaHasta) {
            $whereConditions[] = "A.FECHA_MOV BETWEEN '$fechaDesde' AND '$fechaHasta'";
        } elseif ($fechaDesde) {
            $whereConditions[] = "A.FECHA_MOV >= '$fechaDesde'";
        } elseif ($fechaHasta) {
            $whereConditions[] = "A.FECHA_MOV <= '$fechaHasta'";
        } else {
            // Sin filtro de fecha, traer los últimos 90 días por defecto para performance
            $whereConditions[] = "A.FECHA_MOV >= GETDATE()-360";
        }
        
        $sql = "SELECT 
                    CAST(A.FECHA_MOV AS DATE) FECHA_MOV, 
                    A.HORA_INGRESO, 
                    A.COD_PRO_CL, 
                    A.N_COMP, 
                    CAST(SUM(CANTIDAD) AS FLOAT) CANTIDAD,
                    CASE WHEN C.ESTADO_MOV IS NULL THEN 'SIN IMPORTAR'
                         WHEN D.NCOMP_ORIG IS NOT NULL THEN 'INGRESADO'
                         WHEN D.NCOMP_ORIG IS NULL THEN 'SIN INGRESAR' 
                    END ESTADO
                FROM STA14 A 
                LEFT JOIN STA20 B ON A.ID_STA14 = B.ID_STA14
                LEFT JOIN CTA115 C ON A.T_COMP = C.T_COMP AND A.N_COMP = C.N_COMP
                LEFT JOIN (SELECT NCOMP_ORIG FROM STA14) D ON A.N_COMP = D.NCOMP_ORIG
                WHERE " . implode(' AND ', $whereConditions) . "
                GROUP BY A.FECHA_MOV, A.HORA_INGRESO, A.COD_PRO_CL, A.N_COMP,
                CASE WHEN C.ESTADO_MOV IS NULL THEN 'SIN IMPORTAR'
                     WHEN D.NCOMP_ORIG IS NOT NULL THEN 'INGRESADO'
                     WHEN D.NCOMP_ORIG IS NULL THEN 'SIN INGRESAR' 
                END
                ORDER BY A.FECHA_MOV DESC, A.N_COMP DESC";
        
        // Filtro por estado después de la consulta si es necesario
        $datos = $this->getDatos($sql);
        
        if ($estado && $estado !== 'TODOS') {
            $datos = array_filter($datos, function($item) use ($estado) {
                return $item['ESTADO'] === $estado;
            });
        }
        
        return array_values($datos);
    }

    public function importarRemitos() {
        $cid = new Conexion();
        $cid_central = $cid->conectarSql('central');

        try {
            // Obtener remitos para importar
            $sql = "SELECT N_COMP, COD_PRO_CL FROM STA14 WHERE COD_PRO_CL IN ('GTWEB', 'GTMELI') AND EXPORTADO = 0";
            $result = sqlsrv_query($cid_central, $sql);
            
            $remitos = [];
            while($v = sqlsrv_fetch_object($result)) {
                $remitos[] = $v->N_COMP;
            }

            if (count($remitos) == 0) {
                return ['success' => true, 'message' => 'No había remitos para cargar', 'count' => 0];
            }

            $importados = 0;
            foreach ($remitos as $remito) {
                if ($this->insertarAbastecimiento($remito, $cid_central)) {
                    $importados++;
                }
            }

            return ['success' => true, 'message' => "Se importaron $importados remitos correctamente", 'count' => $importados];

        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error al importar remitos: ' . $e->getMessage()];
        } finally {
            sqlsrv_close($cid_central);
        }
    }

    private function insertarAbastecimiento($remito, $conexion) {
        try {
            $sql = "
            SET DATEFORMAT YMD
            
            INSERT INTO CTA115 (
            [FILLER],[TCOMP_IN_S],[NCOMP_IN_S],[NRO_SUCURS],[COD_PRO_CL],[COTIZ],[T_COMP],[N_COMP],[N_REMITO],
            [ESTADO_MOV],[EXPORTADO],[ESTADO],[EXP_STOCK],[FECHA_ANU],[FECHA_MOV],
            [HORA],[ID_CARPETA],[LISTA_REM],[LOTE],[LOTE_ANU],[MON_CTE],[MOTIVO_REM],[NCOMP_ORIG],[OBSERVACIO],[SUC_ORIG],[TALONARIO],[TCOMP_ORIG],[USUARIO]
            ,[COD_TRANSP],[HORA_COMP],[ID_A_RENTA],[DOC_ELECTR],[COD_CLASIF],[AUDIT_IMP],[IMP_IVA],[IMP_OTIMP],[IMPORTE_BO],[IMPORTE_TO],[DIFERENCIA],[SUC_DESTIN]
            )
            SELECT 
            [FILLER], [TCOMP_IN_S],[NCOMP_IN_S], 1, '', [COTIZ], [T_COMP], [N_COMP], [N_REMITO], 
            'P', 0, 'P', 0, [FECHA_ANU], [FECHA_MOV], 
            '', '', 0, 0, 0, 1, 'V', '', [OBSERVACIO],
            [SUC_ORIG], [TALONARIO], '', [USUARIO], [COD_TRANSP], [HORA_COMP], 0, 0, '', '', 0, 0, 0, 0, 'N', [SUC_DESTIN]
            FROM STA14 
            WHERE COD_PRO_CL IN ('GTWEB', 'GTMELI') AND N_COMP = '$remito'
            
            INSERT INTO CTA96 (
            [FILLER],[TCOMP_IN_S],[NCOMP_IN_S],[NRO_SUCURS],[CAN_EQUI_V],[CANT_DEV],[CANT_OC],[CANT_PEND],[CANT_SCRAP],[CANTIDAD],[CANT_REAL],[CANT_FACTU],[COD_ARTICU]
            ,[COD_DEPOSI],[COD_DEPENT],[DEPOSI_DDE],[EQUIVALENC],[FECHA_MOV],[N_ORDEN_CO],[N_RENGL_OC],[N_RENGL_S],[PLISTA_REM],[PPP_EX],[PPP_LO],[PRECIO],[PRECIO_REM]
            ,[TIPO_MOV],[COD_CLASIF],[ENTRA_SALE],[PREC_REAL],[CANT_DEV_2],[CANT_PEND_2],[CANTIDAD_2],[CANT_OC_2],[CANT_REAL_2],[CANT_FACTU_2],[ID_MEDIDA_COMPRA]
            ,[ID_MEDIDA_STOCK],[ID_MEDIDA_STOCK_2],[ID_MEDIDA_VENTAS],[UNIDAD_MEDIDA_SELECCIONADA],[TALONARIO_OC]
            )
            SELECT  
            [FILLER], [TCOMP_IN_S], [NCOMP_IN_S], 1, 1, 0, 0, [CANTIDAD], 0, [CANTIDAD], [CANTIDAD], 0, [COD_ARTICU], '01', 
            '01', '', 1, [FECHA_MOV], '', 0, [N_RENGL_S], 0, 0, 0, 0, 0, 'E',
            '', 'E', 0, 0, 0, 0, 0, 0, 0, NULL, 7, NULL, 7, '', ''
            FROM STA20 WHERE TCOMP_IN_S = 'RE' AND NCOMP_IN_S = (SELECT NCOMP_IN_S FROM STA14 WHERE T_COMP = 'REM' AND N_COMP = '$remito')
            
            INSERT INTO CTA116(
            [FILLER],[N_PARTIDA],[NCOMP_IN_S],[TCOMP_IN_S],[CANTIDAD],[CANT_REAL],[COD_ARTICU],[COD_DEPOSI],[COD_DEPENT],[N_RENGL_S],[NRO_SUCURS],[ENTRA_SALE],[CANTIDAD_2]
            ,[CANTIDAD_REAL_2],[CANT_DEV],[CANT_DEV_2]
            )
            SELECT '', B.N_PARTIDA, A.NCOMP_IN_S, A.TCOMP_IN_S, A.CANTIDAD, A.CANTIDAD, A.COD_ARTICU, '01', '01', [N_RENGL_S], 1, 'E', 0, 0, 0, 0
            FROM STA20 A
            INNER JOIN SJ_ARTICULOS_MAX_PARTIDAS B
            ON A.COD_ARTICU = B.COD_ARTICU AND A.COD_DEPOSI = B.COD_DEPOSI 
            WHERE TCOMP_IN_S = 'RE' 
            AND NCOMP_IN_S = (SELECT NCOMP_IN_S FROM STA14 WHERE T_COMP = 'REM' AND N_COMP = '$remito')
            
            UPDATE STA14 SET EXPORTADO = 1 WHERE COD_PRO_CL IN ('GTWEB', 'GTMELI') AND N_COMP = '$remito'
            
            UPDATE CTA96 SET COD_DEPENT = '11'
            WHERE TCOMP_IN_S = 'RE' AND NCOMP_IN_S IN
            (SELECT NCOMP_IN_S FROM STA14 WHERE COD_PRO_CL = 'GTMELI' )
            ";

            return sqlsrv_query($conexion, $sql);

        } catch (Exception $e) {
            return false;
        }
    }
}

?>
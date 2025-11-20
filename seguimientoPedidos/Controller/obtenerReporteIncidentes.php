<?php
header('Content-Type: application/json');
require_once '../../Class/Conexion.php';
require_once '../../Class/Pedido.php';

try {
    $desde = $_GET['desde'] ?? date('Y-m-d', strtotime('-30 days'));
    $hasta = $_GET['hasta'] ?? date('Y-m-d');
    $estado = $_GET['estado'] ?? '';

    if(empty($desde)) $desde = date('Y-m-d', strtotime('-30 days'));
    if(empty($hasta)) $hasta = date('Y-m-d');

    // Constante SLA: 10 días
    define('SLA_DIAS', 10); // SLA de 10 días
    define('DIAS_RIESGO', 1); // Alerta cuando queda 1 día o menos

    $pedido = new Pedido();
    $incidentesData = $pedido->getHistorialFaltantesCompleto($desde, $hasta, '', $estado, '');
    
    $incidentes = [];
    $total_dias_resolucion = 0;
    $resueltos_count = 0;

    $kpis = [
        'total' => 0,
        'abierto' => 0,
        'proceso' => 0,
        'resuelto' => 0
    ];

    // NUEVOS KPIs SLA
    $sla_metrics = [
        'casos_dentro_sla' => 0,
        'casos_fuera_sla' => 0,
        'casos_en_riesgo' => 0,
        'total_brecha_sla' => 0,  // Suma de días sobre SLA
        'casos_con_brecha' => 0,   // Cantidad de casos fuera de SLA para calcular promedio
        'resueltos_dentro_sla' => 0,
        'resueltos_fuera_sla' => 0,
        'abiertos_fuera_sla' => 0
    ];

    $desglose_resolucion = [];

    // Para el ranking por depósito con % incumplimiento SLA
    $ranking_depositos_data = [];

    if ($incidentesData) {
        foreach($incidentesData as $row) {
            $item = $row[0];

            $fecha_incidente = $item->FECHA_PEDIDO ?? $item->FECHA_ALTA;
            if (!$fecha_incidente || !($fecha_incidente instanceof DateTime)) {
                continue; 
            }

            $hoy = new DateTime();
            $dias_abierto = 0;
            $dias_sobre_sla = 0;
            $estado_sla = 'dentro'; // dentro, riesgo, fuera

            if ($item->ESTADO == 'resuelto' && !empty($item->FECHA_ULT_MODIF) && $item->FECHA_ULT_MODIF instanceof DateTime) {
                // Caso RESUELTO: calculamos días entre apertura y cierre
                $fecha_resolucion = $item->FECHA_ULT_MODIF;
                $diff = $fecha_resolucion->diff($fecha_incidente);
                $dias_abierto = $diff->days;
                $total_dias_resolucion += $dias_abierto;
                $resueltos_count++;

                // Evaluar SLA para casos resueltos
                if ($dias_abierto > SLA_DIAS) {
                    $dias_sobre_sla = $dias_abierto - SLA_DIAS;
                    $estado_sla = 'fuera';
                    $sla_metrics['casos_fuera_sla']++;
                    $sla_metrics['resueltos_fuera_sla']++;
                    $sla_metrics['total_brecha_sla'] += $dias_sobre_sla;
                    $sla_metrics['casos_con_brecha']++;
                } else {
                    $estado_sla = 'dentro';
                    $sla_metrics['casos_dentro_sla']++;
                    $sla_metrics['resueltos_dentro_sla']++;
                }

                $resolucion = strtolower(trim($item->RESOLUCION));
                if (!empty($resolucion) && $resolucion != 'pendiente') {
                    if (!isset($desglose_resolucion[$resolucion])) {
                        $desglose_resolucion[$resolucion] = 0;
                    }
                    $desglose_resolucion[$resolucion]++;
                }
            } else {
                // Caso ABIERTO o EN PROCESO: calculamos días desde la apertura hasta hoy
                $diff = $hoy->diff($fecha_incidente);
                $dias_abierto = $diff->days;

                // Evaluar estado SLA
                $dias_restantes = SLA_DIAS - $dias_abierto;

                if ($dias_abierto > SLA_DIAS) {
                    // Fuera de SLA
                    $dias_sobre_sla = $dias_abierto - SLA_DIAS;
                    $estado_sla = 'fuera';
                    $sla_metrics['casos_fuera_sla']++;
                    $sla_metrics['abiertos_fuera_sla']++;
                    $sla_metrics['total_brecha_sla'] += $dias_sobre_sla;
                    $sla_metrics['casos_con_brecha']++;
                } elseif ($dias_restantes <= DIAS_RIESGO) {
                    // En Riesgo
                    $estado_sla = 'riesgo';
                    $sla_metrics['casos_en_riesgo']++;
                    $sla_metrics['casos_dentro_sla']++; // Técnicamente aún dentro, pero en riesgo
                } else {
                    // Dentro de SLA
                    $estado_sla = 'dentro';
                    $sla_metrics['casos_dentro_sla']++;
                }
            }
            
            $art_original_final = ($item->ARTICULO_ORIGINAL && $item->ARTICULO_ORIGINAL !== 'N/A') 
                                   ? $item->ARTICULO_ORIGINAL 
                                   : $item->COD_ARTICULO;

            // Construir el array del incidente
            $incidentes[] = [
                'nro_pedido' => $item->NRO_PEDIDO,
                'nro_orden' => $item->NRO_ORDEN,
                'fecha_incidente' => $fecha_incidente->format('d/m/Y'),
                'cliente' => $item->CLIENTE,
                
                // CAMBIO AQUI: Usamos la variable calculada arriba
                'articulo_original' => $art_original_final,
                
                'articulo_reemplazante' => $item->COD_ARTICULO,

                'warehouse' => $item->WAREHOUSE_RECLAMO ?? 'N/A',
                'deposito_origen' => $item->DEPOSITO_ORIGEN ?? 'N/A',
                'nombre_origen' => $item->NOMBRE_ORIGEN ?? 'N/A',
                'estado' => $item->ESTADO,
                'resolucion' => $item->RESOLUCION ?? 'Pendiente',
                'dias_abierto' => $dias_abierto,
                'dias_sobre_sla' => $dias_sobre_sla,
                'estado_sla' => $estado_sla
            ];

            // Actualizar ranking de depósitos con métricas SLA
            $nombreOrigen = $item->NOMBRE_ORIGEN ?? 'No especificado';
            if (!isset($ranking_depositos_data[$nombreOrigen])) {
                $ranking_depositos_data[$nombreOrigen] = [
                    'total' => 0,
                    'fuera_sla' => 0
                ];
            }
            $ranking_depositos_data[$nombreOrigen]['total']++;
            if ($estado_sla === 'fuera') {
                $ranking_depositos_data[$nombreOrigen]['fuera_sla']++;
            }

            $estadoActual = strtolower(trim($item->ESTADO));
            if(isset($kpis[$estadoActual])){
                 $kpis[$estadoActual]++;
            }
        }
    }
    
    // --- PROCESAMIENTO FINAL DE KPIs ---
    $kpis['total'] = count($incidentes);
    $kpis['tasa_resolucion'] = ($kpis['total'] > 0) ? ($kpis['resuelto'] / $kpis['total']) * 100 : 0;
    $kpis['tiempo_promedio'] = ($resueltos_count > 0) ? round($total_dias_resolucion / $resueltos_count, 1) : 0;

    // Calcular % de cumplimiento SLA
    $sla_metrics['porcentaje_cumplimiento'] = ($kpis['total'] > 0) 
        ? round(($sla_metrics['casos_dentro_sla'] / $kpis['total']) * 100, 1) 
        : 0;

    // Brecha promedio en días
    $sla_metrics['brecha_promedio'] = ($sla_metrics['casos_con_brecha'] > 0)
        ? round($sla_metrics['total_brecha_sla'] / $sla_metrics['casos_con_brecha'], 1)
        : 0;

    // % de casos fuera de SLA
    $sla_metrics['porcentaje_fuera_sla'] = ($kpis['total'] > 0)
        ? round(($sla_metrics['casos_fuera_sla'] / $kpis['total']) * 100, 1)
        : 0;

    // Determinar el próximo objetivo y la brecha
    $objetivos = [70, 80, 90];
    $cumplimiento_actual = $sla_metrics['porcentaje_cumplimiento'];
    $proximo_objetivo = null;
    $gap_objetivo = 0;

    foreach ($objetivos as $objetivo) {
        if ($cumplimiento_actual < $objetivo) {
            $proximo_objetivo = $objetivo;
            $gap_objetivo = round($objetivo - $cumplimiento_actual, 1);
            break;
        }
    }

    // Si ya superó el 90%, el próximo objetivo es 100%
    if ($proximo_objetivo === null) {
        $proximo_objetivo = 100;
        $gap_objetivo = round(100 - $cumplimiento_actual, 1);
    }

    $sla_metrics['proximo_objetivo'] = $proximo_objetivo;
    $sla_metrics['gap_objetivo'] = $gap_objetivo;

    // --- PROCESAMIENTO DEL RANKING DE DEPÓSITOS CON % INCUMPLIMIENTO ---
    $ranking_depositos = [];
    $ranking_incidencias = [];
    $contadores_riesgo = [
        'critico' => 0,    // > 50%
        'alerta' => 0,     // 20-50%
        'aceptable' => 0   // <= 20%
    ];
    
    foreach ($ranking_depositos_data as $nombre => $data) {
        $porcentaje_incumplimiento = ($data['total'] > 0) 
            ? round(($data['fuera_sla'] / $data['total']) * 100, 1) 
            : 0;
        
        $porcentaje_cumplimiento = 100 - $porcentaje_incumplimiento;
        
        // Clasificar nivel de riesgo
        $nivel_riesgo = 'aceptable';
        if ($porcentaje_incumplimiento > 50) {
            $nivel_riesgo = 'critico';
            $contadores_riesgo['critico']++;
        } elseif ($porcentaje_incumplimiento >= 20) {
            $nivel_riesgo = 'alerta';
            $contadores_riesgo['alerta']++;
        } else {
            $contadores_riesgo['aceptable']++;
        }
        
        $deposito_data = [
            'nombre' => $nombre,
            'total' => $data['total'],
            'fuera_sla' => $data['fuera_sla'],
            'dentro_sla' => $data['total'] - $data['fuera_sla'],
            'porcentaje_incumplimiento' => $porcentaje_incumplimiento,
            'porcentaje_cumplimiento' => $porcentaje_cumplimiento,
            'nivel_riesgo' => $nivel_riesgo
        ];
        
        $ranking_depositos[] = $deposito_data;
        $ranking_incidencias[] = $deposito_data;
    }

    // Ordenar ranking de incumplimiento por % descendente
    usort($ranking_depositos, function($a, $b) {
        return $b['porcentaje_incumplimiento'] <=> $a['porcentaje_incumplimiento'];
    });
    
    // Ordenar ranking de incidencias por cantidad total descendente
    usort($ranking_incidencias, function($a, $b) {
        return $b['total'] <=> $a['total'];
    });

    // Calcular porcentajes de contadores de riesgo
    $total_depositos = count($ranking_depositos);
    $contadores_riesgo_pct = [
        'critico' => $total_depositos > 0 ? round(($contadores_riesgo['critico'] / $total_depositos) * 100, 1) : 0,
        'alerta' => $total_depositos > 0 ? round(($contadores_riesgo['alerta'] / $total_depositos) * 100, 1) : 0,
        'aceptable' => $total_depositos > 0 ? round(($contadores_riesgo['aceptable'] / $total_depositos) * 100, 1) : 0
    ];

    // --- INSIGHT AUTOMÁTICO: TOP 2 DEPÓSITOS CON MAYOR INCUMPLIMIENTO ---
    $insight = null;
    if (count($ranking_depositos) >= 2) {
        $top1 = $ranking_depositos[0];
        $top2 = $ranking_depositos[1];
        
        $incidentes_top2 = $top1['total'] + $top2['total'];
        $porc_incidentes_top2 = $kpis['total'] > 0 ? round(($incidentes_top2 / $kpis['total']) * 100, 1) : 0;
        $prom_incumplimiento_top2 = round(($top1['porcentaje_incumplimiento'] + $top2['porcentaje_incumplimiento']) / 2, 1);
        
        $insight = [
            'top1' => $top1['nombre'],
            'top2' => $top2['nombre'],
            'porc_incidentes_top2' => $porc_incidentes_top2,
            'prom_incumplimiento_top2' => $prom_incumplimiento_top2,
            'mensaje' => "Los depósitos {$top1['nombre']} y {$top2['nombre']} aportan {$porc_incidentes_top2}% de incidentes y tienen {$prom_incumplimiento_top2}% fuera de SLA. Requieren atención prioritaria."
        ];
    } elseif (count($ranking_depositos) === 1) {
        $top1 = $ranking_depositos[0];
        $insight = [
            'top1' => $top1['nombre'],
            'top2' => null,
            'porc_incidentes_top2' => 100,
            'prom_incumplimiento_top2' => $top1['porcentaje_incumplimiento'],
            'mensaje' => "El depósito {$top1['nombre']} concentra el 100% de incidentes con {$top1['porcentaje_incumplimiento']}% fuera de SLA."
        ];
    }

    echo json_encode([
        'success' => true,
        'kpis' => $kpis,
        'sla_metrics' => $sla_metrics,
        'sla_config' => [
            'sla_dias' => SLA_DIAS,
            'dias_riesgo' => DIAS_RIESGO
        ],
        'tablaData' => $incidentes,
        'desgloseResolucion' => $desglose_resolucion,
        'rankingDepositos' => $ranking_depositos, // Ordenado por % incumplimiento
        'rankingIncidencias' => $ranking_incidencias, // Ordenado por cantidad
        'contadores_riesgo' => $contadores_riesgo,
        'contadores_riesgo_pct' => $contadores_riesgo_pct,
        'insight' => $insight
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error en el servidor: ' . $e->getMessage()
    ]);
}
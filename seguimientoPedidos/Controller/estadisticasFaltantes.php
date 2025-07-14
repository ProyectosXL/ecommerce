
<?php
// seguimientoPedidos/Controller/estadisticasIncidentes.php

// Verificar que sea una petición GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => 'Método no permitido.'
    ]);
    exit;
}

// Incluir archivos necesarios
require_once '../../Class/Conexion.php';
require_once '../../Class/Historial.php';

try {
    // Obtener parámetros opcionales
    $periodo = isset($_GET['periodo']) ? trim($_GET['periodo']) : '30'; // días
    $warehouse = isset($_GET['warehouse']) ? trim($_GET['warehouse']) : '';
    
    // Calcular fechas
    $fechaFin = date('Y-m-d');
    $fechaInicio = date('Y-m-d', strtotime("-{$periodo} days"));
    
    $historial = new Historial();
    
    // Obtener datos base
    $historialCompleto = $historial->getHistorialIncidentesCompleto($fechaInicio, $fechaFin, '', '', $warehouse, '', '');
    
    // Inicializar estadísticas
    $stats = [
        'resumen' => [
            'total_incidentes' => 0,
            'incidentes_hoy' => 0,
            'incidentes_ayer' => 0,
            'articulos_afectados' => 0,
            'proveedores_afectados' => 0,
            'warehouses_afectados' => 0,
            'tiempo_resolucion_promedio' => 0,
            'porcentaje_resueltos' => 0
        ],
        'por_tipo' => [],
        'por_estado' => [
            'abierto' => 0,
            'proceso' => 0,
            'resuelto' => 0
        ],
        'por_impacto' => [
            'critico' => 0,
            'alto' => 0,
            'medio' => 0,
            'bajo' => 0
        ],
        'por_warehouse' => [],
        'por_proveedor' => [],
        'tendencia_diaria' => [],
        'articulos_mas_afectados' => [],
        'proveedores_problematicos' => [],
        'tiempos_resolucion' => []
    ];
    
    if ($historialCompleto && !empty($historialCompleto)) {
        $incidentes = procesarDatosParaEstadisticas($historialCompleto);
        
        // Calcular estadísticas de resumen
        $stats['resumen'] = calcularResumen($incidentes);
        
        // Estadísticas por categorías
        $stats['por_tipo'] = calcularPorTipo($incidentes);
        $stats['por_estado'] = calcularPorEstado($incidentes);
        $stats['por_impacto'] = calcularPorImpacto($incidentes);
        $stats['por_warehouse'] = calcularPorWarehouse($incidentes);
        $stats['por_proveedor'] = calcularPorProveedor($incidentes);
        
        // Tendencias y análisis
        $stats['tendencia_diaria'] = calcularTendenciaDiaria($incidentes, $fechaInicio, $fechaFin);
        $stats['articulos_mas_afectados'] = calcularArticulosMasAfectados($incidentes);
        $stats['proveedores_problematicos'] = calcularProveedoresProblematicos($incidentes);
        $stats['tiempos_resolucion'] = calcularTiemposResolucion($incidentes);
    }
    
    // Obtener datos adicionales de contexto
    $stats['contexto'] = [
        'periodo_analizado' => $periodo,
        'fecha_inicio' => $fechaInicio,
        'fecha_fin' => $fechaFin,
        'warehouse_filtro' => $warehouse,
        'fecha_generacion' => date('Y-m-d H:i:s')
    ];
    
    // Devolver resultado como JSON
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'estadisticas' => $stats
    ]);

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => 'Error del servidor: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}

// Funciones de procesamiento de estadísticas

function procesarDatosParaEstadisticas($historialData) {
    $incidentes = [];
    
    foreach ($historialData as $row) {
        $item = $row[0];
        
        // Procesar cada incidente
        $incidente = [
            'id_pedido' => $item->NRO_PEDIDO,
            'fecha_incidente' => $item->FECHA_PEDIDO ? formatearFecha($item->FECHA_PEDIDO) : '',
            'fecha_incidente_obj' => $item->FECHA_PEDIDO,
            'articulo' => $item->COD_ARTICULO_CAMBIO ?? $item->COD_ARTICULO ?? '',
            'descripcion' => $item->DESCRIPCION ?? '',
            'cantidad' => $item->CANTIDAD ?? 1,
            'warehouse' => $item->WAREHOUSE ?? $item->SUC_DESPACHO ?? '',
            'cliente' => $item->CLIENTE ?? '',
            'estado' => $item->ESTADO ?? 'abierto',
            'resolucion' => $item->RESOLUCION ?? 'pendiente',
            'fecha_resolucion' => $item->FECHA_ULT_MODIF ? formatearFecha($item->FECHA_ULT_MODIF) : '',
            'fecha_resolucion_obj' => $item->FECHA_ULT_MODIF,
            'tipo_incidente' => determinarTipoIncidenteStats($item),
            'impacto' => calcularNivelImpactoStats($item),
            'proveedor' => obtenerProveedorArticuloStats($item->COD_ARTICULO_CAMBIO ?? $item->COD_ARTICULO ?? ''),
            'dias_resolucion' => calcularDiasResolucion($item)
        ];
        
        $incidentes[] = $incidente;
    }
    
    return $incidentes;
}

function calcularResumen($incidentes) {
    $hoy = date('Y-m-d');
    $ayer = date('Y-m-d', strtotime('-1 day'));
    
    $totalIncidentes = count($incidentes);
    $incidentesHoy = count(array_filter($incidentes, function($i) use ($hoy) {
        return $i['fecha_incidente'] === $hoy;
    }));
    $incidentesAyer = count(array_filter($incidentes, function($i) use ($ayer) {
        return $i['fecha_incidente'] === $ayer;
    }));
    
    $articulosUnicos = array_unique(array_column($incidentes, 'articulo'));
    $proveedoresUnicos = array_unique(array_column($incidentes, 'proveedor'));
    $warehousesUnicos = array_unique(array_column($incidentes, 'warehouse'));
    
    $resueltos = array_filter($incidentes, function($i) {
        return $i['estado'] === 'resuelto';
    });
    
    $tiemposResolucion = array_filter(array_column($resueltos, 'dias_resolucion'), function($dias) {
        return $dias > 0;
    });
    
    $tiempoPromedioResolucion = empty($tiemposResolucion) ? 0 : array_sum($tiemposResolucion) / count($tiemposResolucion);
    $porcentajeResueltos = $totalIncidentes > 0 ? (count($resueltos) / $totalIncidentes) * 100 : 0;
    
    return [
        'total_incidentes' => $totalIncidentes,
        'incidentes_hoy' => $incidentesHoy,
        'incidentes_ayer' => $incidentesAyer,
        'variacion_diaria' => $incidentesAyer > 0 ? (($incidentesHoy - $incidentesAyer) / $incidentesAyer) * 100 : 0,
        'articulos_afectados' => count($articulosUnicos),
        'proveedores_afectados' => count($proveedoresUnicos),
        'warehouses_afectados' => count($warehousesUnicos),
        'tiempo_resolucion_promedio' => round($tiempoPromedioResolucion, 1),
        'porcentaje_resueltos' => round($porcentajeResueltos, 1)
    ];
}

function calcularPorTipo($incidentes) {
    $tipos = [];
    foreach ($incidentes as $incidente) {
        $tipo = $incidente['tipo_incidente'];
        $tipos[$tipo] = ($tipos[$tipo] ?? 0) + 1;
    }
    arsort($tipos);
    return $tipos;
}

function calcularPorEstado($incidentes) {
    $estados = [
        'abierto' => 0,
        'proceso' => 0,
        'resuelto' => 0
    ];
    
    foreach ($incidentes as $incidente) {
        $estado = $incidente['estado'];
        if (isset($estados[$estado])) {
            $estados[$estado]++;
        }
    }
    
    return $estados;
}

function calcularPorImpacto($incidentes) {
    $impactos = [
        'critico' => 0,
        'alto' => 0,
        'medio' => 0,
        'bajo' => 0
    ];
    
    foreach ($incidentes as $incidente) {
        $impacto = $incidente['impacto'];
        if (isset($impactos[$impacto])) {
            $impactos[$impacto]++;
        }
    }
    
    return $impactos;
}

function calcularPorWarehouse($incidentes) {
    $warehouses = [];
    foreach ($incidentes as $incidente) {
        $warehouse = $incidente['warehouse'] ?: 'Sin especificar';
        $warehouses[$warehouse] = ($warehouses[$warehouse] ?? 0) + 1;
    }
    arsort($warehouses);
    return array_slice($warehouses, 0, 10); // Top 10
}

function calcularPorProveedor($incidentes) {
    $proveedores = [];
    foreach ($incidentes as $incidente) {
        $proveedor = $incidente['proveedor'] ?: 'Sin especificar';
        $proveedores[$proveedor] = ($proveedores[$proveedor] ?? 0) + 1;
    }
    arsort($proveedores);
    return array_slice($proveedores, 0, 10); // Top 10
}

function calcularTendenciaDiaria($incidentes, $fechaInicio, $fechaFin) {
    $tendencia = [];
    $fechaActual = new DateTime($fechaInicio);
    $fechaFinal = new DateTime($fechaFin);
    
    while ($fechaActual <= $fechaFinal) {
        $fechaStr = $fechaActual->format('Y-m-d');
        $count = count(array_filter($incidentes, function($i) use ($fechaStr) {
            return $i['fecha_incidente'] === $fechaStr;
        }));
        
        $tendencia[] = [
            'fecha' => $fechaStr,
            'cantidad' => $count,
            'dia_semana' => $fechaActual->format('w'),
            'es_fin_semana' => in_array($fechaActual->format('w'), [0, 6])
        ];
        
        $fechaActual->add(new DateInterval('P1D'));
    }
    
    return $tendencia;
}

function calcularArticulosMasAfectados($incidentes) {
    $articulos = [];
    
    foreach ($incidentes as $incidente) {
        $articulo = $incidente['articulo'] ?: 'Sin especificar';
        if (!isset($articulos[$articulo])) {
            $articulos[$articulo] = [
                'codigo' => $articulo,
                'descripcion' => $incidente['descripcion'],
                'cantidad_incidentes' => 0,
                'cantidad_total' => 0,
                'warehouses_afectados' => [],
                'ultimo_incidente' => ''
            ];
        }
        
        $articulos[$articulo]['cantidad_incidentes']++;
        $articulos[$articulo]['cantidad_total'] += $incidente['cantidad'];
        $articulos[$articulo]['warehouses_afectados'][$incidente['warehouse']] = true;
        
        if ($incidente['fecha_incidente'] > $articulos[$articulo]['ultimo_incidente']) {
            $articulos[$articulo]['ultimo_incidente'] = $incidente['fecha_incidente'];
        }
    }
    
    // Procesar y ordenar
    foreach ($articulos as &$articulo) {
        $articulo['warehouses_afectados'] = count($articulo['warehouses_afectados']);
    }
    
    usort($articulos, function($a, $b) {
        return $b['cantidad_incidentes'] - $a['cantidad_incidentes'];
    });
    
    return array_slice(array_values($articulos), 0, 15); // Top 15
}

function calcularProveedoresProblematicos($incidentes) {
    $proveedores = [];
    
    foreach ($incidentes as $incidente) {
        $proveedor = $incidente['proveedor'] ?: 'Sin especificar';
        if (!isset($proveedores[$proveedor])) {
            $proveedores[$proveedor] = [
                'nombre' => $proveedor,
                'cantidad_incidentes' => 0,
                'articulos_afectados' => [],
                'tiempo_resolucion_promedio' => 0,
                'incidentes_criticos' => 0,
                'ultimo_incidente' => ''
            ];
        }
        
        $proveedores[$proveedor]['cantidad_incidentes']++;
        $proveedores[$proveedor]['articulos_afectados'][$incidente['articulo']] = true;
        
        if ($incidente['impacto'] === 'critico') {
            $proveedores[$proveedor]['incidentes_criticos']++;
        }
        
        if ($incidente['fecha_incidente'] > $proveedores[$proveedor]['ultimo_incidente']) {
            $proveedores[$proveedor]['ultimo_incidente'] = $incidente['fecha_incidente'];
        }
    }
    
    // Procesar y ordenar
    foreach ($proveedores as &$proveedor) {
        $proveedor['articulos_afectados'] = count($proveedor['articulos_afectados']);
    }
    
    usort($proveedores, function($a, $b) {
        if ($b['incidentes_criticos'] !== $a['incidentes_criticos']) {
            return $b['incidentes_criticos'] - $a['incidentes_criticos'];
        }
        return $b['cantidad_incidentes'] - $a['cantidad_incidentes'];
    });
    
    return array_slice(array_values($proveedores), 0, 10); // Top 10
}

function calcularTiemposResolucion($incidentes) {
    $resueltos = array_filter($incidentes, function($i) {
        return $i['estado'] === 'resuelto' && $i['dias_resolucion'] > 0;
    });
    
    $tiempos = array_column($resueltos, 'dias_resolucion');
    sort($tiempos);
    
    $count = count($tiempos);
    if ($count === 0) {
        return [
            'promedio' => 0,
            'mediana' => 0,
            'minimo' => 0,
            'maximo' => 0,
            'percentil_90' => 0
        ];
    }
    
    $promedio = array_sum($tiempos) / $count;
    $mediana = $tiempos[floor($count / 2)];
    $percentil90 = $tiempos[floor($count * 0.9)];
    
    return [
        'promedio' => round($promedio, 1),
        'mediana' => $mediana,
        'minimo' => min($tiempos),
        'maximo' => max($tiempos),
        'percentil_90' => $percentil90,
        'distribucion' => calcularDistribucionTiempos($tiempos)
    ];
}

function calcularDistribucionTiempos($tiempos) {
    $distribucion = [
        '0-1_dias' => 0,
        '2-3_dias' => 0,
        '4-7_dias' => 0,
        '8-15_dias' => 0,
        'mas_15_dias' => 0
    ];
    
    foreach ($tiempos as $tiempo) {
        if ($tiempo <= 1) {
            $distribucion['0-1_dias']++;
        } elseif ($tiempo <= 3) {
            $distribucion['2-3_dias']++;
        } elseif ($tiempo <= 7) {
            $distribucion['4-7_dias']++;
        } elseif ($tiempo <= 15) {
            $distribucion['8-15_dias']++;
        } else {
            $distribucion['mas_15_dias']++;
        }
    }
    
    return $distribucion;
}

// Funciones auxiliares específicas para estadísticas

function determinarTipoIncidenteStats($item) {
    // Reutilizar la lógica del otro archivo
    if (isset($item->DESCRIPCION)) {
        $desc = strtolower($item->DESCRIPCION);
        if (strpos($desc, 'stock') !== false) {
            return 'stock_insuficiente';
        } elseif (strpos($desc, 'descontinuado') !== false) {
            return 'articulo_descontinuado';
        } elseif (strpos($desc, 'proveedor') !== false) {
            return 'demora_proveedor';
        } elseif (strpos($desc, 'sistema') !== false) {
            return 'falla_sistema';
        }
    }
    return 'stock_insuficiente';
}

function calcularNivelImpactoStats($item) {
    $cantidad = isset($item->CANTIDAD) ? (int)$item->CANTIDAD : 1;
    $diasAbierto = calcularDiasAbierto($item->FECHA_PEDIDO ?? null, $item->ESTADO ?? 'abierto');
    
    if ($cantidad >= 10 || $diasAbierto >= 7) {
        return 'critico';
    } elseif ($cantidad >= 5 || $diasAbierto >= 3) {
        return 'alto';
    } elseif ($cantidad >= 2 || $diasAbierto >= 1) {
        return 'medio';
    }
    return 'bajo';
}

function obtenerProveedorArticuloStats($codigoArticulo) {
    if (empty($codigoArticulo)) {
        return 'Sin especificar';
    }
    
    $proveedores = [
        'X' => 'Proveedor Premium',
        'O' => 'Proveedor Estándar',
        'A' => 'Proveedor Especial'
    ];
    
    $prefijo = substr($codigoArticulo, 0, 1);
    return $proveedores[$prefijo] ?? 'Proveedor General';
}

function calcularDiasResolucion($item) {
    if (empty($item->FECHA_PEDIDO) || empty($item->FECHA_ULT_MODIF) || $item->ESTADO !== 'resuelto') {
        return 0;
    }
    
    $fechaInicio = is_string($item->FECHA_PEDIDO) ? new DateTime($item->FECHA_PEDIDO) : $item->FECHA_PEDIDO;
    $fechaFin = is_string($item->FECHA_ULT_MODIF) ? new DateTime($item->FECHA_ULT_MODIF) : $item->FECHA_ULT_MODIF;
    
    $diff = $fechaFin->diff($fechaInicio);
    return $diff->days;
}

function calcularDiasAbierto($fechaInicio, $estado) {
    if (empty($fechaInicio) || $estado === 'resuelto') {
        return 0;
    }
    
    $fechaInicio = is_string($fechaInicio) ? new DateTime($fechaInicio) : $fechaInicio;
    $fechaActual = new DateTime();
    
    $diff = $fechaActual->diff($fechaInicio);
    return $diff->days;
}

function formatearFecha($fecha) {
    if (empty($fecha)) {
        return '';
    }
    
    if (is_string($fecha)) {
        $fecha = new DateTime($fecha);
    }
    
    return $fecha->format('Y-m-d');
}

exit;
?>
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

    if ($incidentesData) {
        foreach($incidentesData as $row) {
            $item = $row[0];

            $fecha_incidente = $item->FECHA_PEDIDO ?? $item->FECHA_ALTA;
            if (!$fecha_incidente || !($fecha_incidente instanceof DateTime)) {
                continue; 
            }

            $hoy = new DateTime();
            $dias_abierto = 0;

            if ($item->ESTADO == 'resuelto' && !empty($item->FECHA_ULT_MODIF) && $item->FECHA_ULT_MODIF instanceof DateTime) {
                $fecha_resolucion = $item->FECHA_ULT_MODIF;
                $diff = $fecha_resolucion->diff($fecha_incidente);
                $dias_abierto = $diff->days;
                $total_dias_resolucion += $dias_abierto;
                $resueltos_count++;
            } else {
                $diff = $hoy->diff($fecha_incidente);
                $dias_abierto = $diff->days;
            }
            
            $incidentes[] = [
                'nro_pedido' => $item->NRO_PEDIDO,
                'nro_orden' => $item->NRO_ORDEN,
                'fecha_incidente' => $fecha_incidente->format('d/m/Y'),
                'cliente' => $item->CLIENTE,
                'articulo_faltante' => $item->COD_ARTICULO,
                'warehouse' => $item->WAREHOUSE ?? $item->SUC_DESPACHO,
                'estado' => $item->ESTADO,
                'resolucion' => $item->RESOLUCION ?? 'Pendiente',
                'dias_abierto' => $dias_abierto
            ];

            if ($item->ESTADO && isset($kpis[$item->ESTADO])) {
                $kpis[$item->ESTADO]++;
            }
        }
    }
    
    $kpis['total'] = count($incidentes);
    $kpis['tasa_resolucion'] = ($kpis['total'] > 0) ? ($kpis['resuelto'] / $kpis['total']) * 100 : 0;
    $kpis['tiempo_promedio'] = ($resueltos_count > 0) ? $total_dias_resolucion / $resueltos_count : 0;

    echo json_encode([
        'success' => true,
        'kpis' => $kpis,
        'tablaData' => $incidentes
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error en el servidor: ' . $e->getMessage()
    ]);
}
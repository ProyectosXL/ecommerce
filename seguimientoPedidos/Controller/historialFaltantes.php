
<?php
// seguimientoPedidos/Controller/historialFaltantes.php

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

require_once '../../Class/Conexion.php';
require_once '../../Class/Historial.php';

try {
    // Obtener parámetros de filtro
    $fechaInicio = isset($_GET['fechaInicio']) ? trim($_GET['fechaInicio']) : date('Y-m-d', strtotime('-30 days'));
    $fechaFin = isset($_GET['fechaFin']) ? trim($_GET['fechaFin']) : date('Y-m-d');
    $warehouse = isset($_GET['warehouse']) ? trim($_GET['warehouse']) : '';
    $estado = isset($_GET['estado']) ? trim($_GET['estado']) : '';
    $resolucion = isset($_GET['resolucion']) ? trim($_GET['resolucion']) : '';

    // Crear instancia de Pedido
    $pedido = new Pedido();
    
    // Obtener datos del historial de pedidos con faltantes
    $historialData = $pedido->getHistorialFaltantesCompleto($fechaInicio, $fechaFin, $warehouse, $estado, $resolucion);

    // Procesar y estructurar los datos
    $faltantes = [];
    if ($historialData && !empty($historialData)) {
        foreach ($historialData as $row) {
            $item = $row[0];
            
            $faltantes[] = [
                'nro_pedido' => $item->NRO_PEDIDO,
                'nro_orden' => $item->NRO_ORDEN ?? '',
                'fecha_incidente' => $item->FECHA_PEDIDO ? formatearFecha($item->FECHA_PEDIDO) : '',
                'fecha_incidente_full' => $item->FECHA_PEDIDO,
                'cliente' => $item->CLIENTE ?? '',
                'articulo_faltante' => $item->COD_ARTICULO_CAMBIO ?? $item->COD_ARTICULO ?? '',
                'descripcion_articulo' => $item->DESCRIPCION ?? 'Sin descripción',
                'cantidad' => $item->CANTIDAD ?? 1,
                'warehouse' => $item->WAREHOUSE ?? $item->SUC_DESPACHO ?? '',
                'estado' => $item->ESTADO ?? 'abierto',
                'resolucion' => $item->RESOLUCION ?? 'pendiente',
                'ultima_modificacion' => $item->FECHA_ULT_MODIF ? formatearFecha($item->FECHA_ULT_MODIF) : '',
                'ultima_modificacion_full' => $item->FECHA_ULT_MODIF,
                'comentarios_count' => contarComentarios($item->NRO_PEDIDO, $pedido),
                'dias_abierto' => calcularDiasAbierto($item->FECHA_PEDIDO, $item->ESTADO)
            ];
        }
    }

    // Aplicar filtros adicionales si es necesario
    $faltantes = aplicarFiltrosAdicionales($faltantes, [
        'fechaInicio' => $fechaInicio,
        'fechaFin' => $fechaFin,
        'warehouse' => $warehouse,
        'estado' => $estado,
        'resolucion' => $resolucion
    ]);

    // Devolver resultado como JSON
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'data' => $faltantes,
        'count' => count($faltantes),
        'filters_applied' => [
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'warehouse' => $warehouse,
            'estado' => $estado,
            'resolucion' => $resolucion
        ]
    ]);

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => 'Error del servidor: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}

// Funciones auxiliares

function contarComentarios($nroPedido, $pedido) {
    try {
        $comentarios = $pedido->listarReclamoDetalle($nroPedido);
        return count($comentarios);
    } catch (Exception $e) {
        return 0;
    }
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

function aplicarFiltrosAdicionales($faltantes, $filtros) {
    return array_filter($faltantes, function($faltante) use ($filtros) {
        // Filtro por fechas
        if (!empty($filtros['fechaInicio']) && $faltante['fecha_incidente'] < $filtros['fechaInicio']) {
            return false;
        }
        if (!empty($filtros['fechaFin']) && $faltante['fecha_incidente'] > $filtros['fechaFin']) {
            return false;
        }
        
        // Filtro por warehouse
        if (!empty($filtros['warehouse']) && $faltante['warehouse'] !== $filtros['warehouse']) {
            return false;
        }
        
        // Filtro por estado
        if (!empty($filtros['estado']) && $faltante['estado'] !== $filtros['estado']) {
            return false;
        }
        
        // Filtro por resolución
        if (!empty($filtros['resolucion']) && $faltante['resolucion'] !== $filtros['resolucion']) {
            return false;
        }
        
        return true;
    });
}

exit;
?>
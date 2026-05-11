<?php
/**
 * Exporta a Excel (CSV) todos los pedidos que coinciden con los filtros activos,
 * sin límite de paginación.
 */

// Deshabilitar output buffering INMEDIATAMENTE antes de cualquier otra cosa
while (ob_get_level()) {
    ob_end_clean();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/ecommerce/Class/Conexion.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/ecommerce/Class/Pedido.php';

// Registrar inicio de script con timer
$startTime = microtime(true);
error_log('exportarExcel.php: Iniciando exportación - ' . date('Y-m-d H:i:s'));

$pedidos = new Pedido();

// Exportaciones grandes pueden tomar varios minutos
ini_set('max_execution_time', 0);
set_time_limit(0);
ini_set('memory_limit', '1024M'); // Aumentado a 1GB
ignore_user_abort(true); // Continuar aunque el cliente se desconecte

$hoy      = date('Y-m-d');
$tienda   = (!isset($_GET['tienda'])   || trim($_GET['tienda'])   === '') ? '%' : $_GET['tienda']   . '%';
$warehouse= (!isset($_GET['warehouse'])|| trim($_GET['warehouse'])=== '') ? '%' : $_GET['warehouse']. '%';
$desde    = (!isset($_GET['desde']))   ? $hoy : $_GET['desde'];
$hasta    = (!isset($_GET['hasta']))   ? $hoy : $_GET['hasta'];
$estado   = (isset($_GET['estado'])    && trim($_GET['estado'])   !== '') ? trim($_GET['estado'])    : null;
$orden    = (!isset($_GET['orden'])    || trim($_GET['orden'])    === '') ? '%' : $_GET['orden']    . '%';
$metodoEnvio = (isset($_GET['metodo_envio']) && trim($_GET['metodo_envio']) !== '') ? trim($_GET['metodo_envio']) : '';
$busqueda    = (isset($_GET['factura'])      && trim($_GET['factura'])      !== '') ? trim($_GET['factura'])      : '';

// ---------- Generar el archivo en el servidor primero ----------
$filename = 'pedidos_' . $desde . '_' . $hasta . '.csv';
$tempDir = sys_get_temp_dir();
$tempFile = $tempDir . DIRECTORY_SEPARATOR . uniqid('export_', true) . '.csv';

error_log("exportarExcel: Generando archivo temporal en: {$tempFile}");

// Abrir archivo temporal para escritura
$output = fopen($tempFile, 'w');
if (!$output) {
    error_log("exportarExcel: ERROR - No se pudo crear el archivo temporal");
    die('Error: No se pudo crear el archivo temporal');
}

// BOM UTF-8 para que Excel lo abra correctamente con tildes / ñ
fwrite($output, "\xEF\xBB\xBF");

// Encabezados de columna
fputcsv($output, [
    'TIENDA',
    'NRO ORDEN',
    'FECHA PEDIDO',
    'HORA',
    'NRO PEDIDO',
    'NOMBRE',
    'COD ARTICULO',
    'DESC ARTICULO',
    'CANTIDAD',
    'IMPORTE',
    'NRO FACTURA',
    'DEPOSITO',
    'METODO ENVIO',
    'TIENDA ENTREGA',
    'ESTADO',
], ';');

// NO hacer flush aquí, estamos escribiendo a un archivo temporal

// ---------- Fetch paginado: 2000 registros por página para mayor velocidad ----------
$porPagina   = 2000;
$paginaActual = 1;
$busquedaLower = $busqueda !== '' ? mb_strtolower($busqueda) : '';
$totalRegistros = 0;

try {
while (true) {
    $arrayPedidos = $pedidos->traerPedidos($desde, $hasta, $tienda, $warehouse, $estado, $orden, $paginaActual, $porPagina, $metodoEnvio);

    // Guardar la cantidad RAW devuelta por el SP para decidir si hay más páginas
    $rawCount = count($arrayPedidos);

    // Filtrar pedidos sin unidades
    $arrayPedidos = array_filter($arrayPedidos, function ($value) {
        return isset($value[0]->CANTIDAD_A_FACTURAR) && $value[0]->CANTIDAD_A_FACTURAR > 0;
    });

    // Filtro de texto libre (igual que en index.php)
    if ($busquedaLower !== '') {
        $arrayPedidos = array_filter($arrayPedidos, function ($value) use ($busquedaLower) {
            $campos = [
                $value[0]->NRO_ORDEN_ECOMMERCE ?? '',
                $value[0]->NRO_PEDIDO          ?? '',
                $value[0]->RAZON_SOCIAL        ?? '',
                $value[0]->COD_ARTICULO        ?? '',
                $value[0]->DESCRIPCION         ?? '',
                $value[0]->NRO_COMP            ?? '',
                $value[0]->WAREHOUSE           ?? '',
                $value[0]->METODO_ENVIO        ?? '',
                $value[0]->DESC_SUCURSAL       ?? '',
            ];
            foreach ($campos as $campo) {
                if (mb_strpos(mb_strtolower((string) $campo), $busquedaLower) !== false) {
                    return true;
                }
            }
            return false;
        });
    }

    // Verificar si la conexión sigue activa
    if (connection_status() != CONNECTION_NORMAL) {
        error_log('exportarExcel: Conexión cerrada por el cliente');
        break;
    }

    foreach ($arrayPedidos as $value) {
        $v = $value[0];

        // Construir un campo de estado legible
        $estado_fila = '';
        if (isset($v->CANCELADO) && $v->CANCELADO == 1) {
            $estado_fila = 'CANCELADO';
        } elseif (isset($v->ENTREGADO) && $v->ENTREGADO == 1) {
            $estado_fila = 'ENTREGADO';
        } elseif (isset($v->DESPACHADO) && $v->DESPACHADO == 1) {
            $estado_fila = 'DESPACHADO';
        } elseif (isset($v->CONTROLADO) && $v->CONTROLADO == 1) {
            $estado_fila = 'CONTROLADO';
        } elseif (isset($v->FACTURADO) && $v->FACTURADO == 1) {
            $estado_fila = 'FACTURADO';
        } elseif (isset($v->PREPARADO) && $v->PREPARADO == 1) {
            $estado_fila = 'PREPARADO';
        } else {
            $estado_fila = 'SIN_CONTROLAR';
        }

        // Forzar NRO_ORDEN como texto en Excel para evitar notación científica
        $nroOrden = $v->NRO_ORDEN_ECOMMERCE ?? '';
        $nroOrdenExcel = $nroOrden !== '' ? '="' . $nroOrden . '"' : '';

        fputcsv($output, [
            $v->ORIGEN                ?? '',
            $nroOrdenExcel,
            isset($v->FECHA_PEDIDO)   ? $v->FECHA_PEDIDO->format('Y-m-d') : '',
            $v->HORA                  ?? '',
            $v->NRO_PEDIDO            ?? '',
            $v->RAZON_SOCIAL          ?? '',
            $v->COD_ARTICULO          ?? '',
            $v->DESCRIPCION           ?? '',
            $v->CANTIDAD_A_FACTURAR   ?? '',
            $v->IMPORTE_PAGO          ?? '',
            $v->NRO_COMP              ?? '',
            $v->WAREHOUSE             ?? '',
            $v->METODO_ENVIO          ?? '',
            $v->DESC_SUCURSAL         ?? '',
            $estado_fila,
        ], ';');
        
        $totalRegistros++;
    }

    // NO necesitamos flush cuando escribimos a archivo temporal
    // Escribir directamente al archivo es más eficiente

    // Si el SP devolvió menos registros que el límite, ya no hay más datos
    if ($rawCount < $porPagina) {
        break;
    }

    $paginaActual++;
}

// Log de finalización exitosa con tiempo transcurrido
$endTime = microtime(true);
$duration = round($endTime - $startTime, 2);
error_log("exportarExcel: Exportación completada exitosamente - {$totalRegistros} registros exportados en {$paginaActual} páginas - Tiempo: {$duration} segundos");

} catch (Throwable $e) {
    $endTime = microtime(true);
    $duration = round($endTime - $startTime, 2);
    $memoriaFinal = memory_get_usage(true) / 1024 / 1024;
    error_log("exportarExcel ERROR: " . $e->getMessage() . " | Línea: " . $e->getLine() . " | Registros procesados: {$totalRegistros} | Memoria: {$memoriaFinal}MB | Tiempo: {$duration}s");
    error_log("exportarExcel Stack trace: " . $e->getTraceAsString());
    
    // Intentar enviar el error al log pero continuar
    if (connection_status() == CONNECTION_NORMAL) {
        // Si la conexión sigue activa, cerrar el archivo limpiamente
        if (is_resource($output)) {
            fclose($output);
        }
    }
}

// Cerrar el archivo temporal
if (is_resource($output)) {
    fclose($output);
}

// ---------- Ahora enviar el archivo generado al cliente ----------
if (file_exists($tempFile)) {
    $fileSize = filesize($tempFile);
    $endGeneration = microtime(true);
    $generationTime = round($endGeneration - $startTime, 2);
    error_log("exportarExcel: Archivo generado exitosamente - {$fileSize} bytes, tiempo de generación: {$generationTime}s, enviando al cliente...");
    
    // Limpiar cualquier output buffer
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Enviar headers HTTP
    header('Connection: keep-alive');
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . $fileSize);
    header('Pragma: no-cache');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('X-Accel-Buffering: no'); // Desactivar buffering de Nginx si está presente
    
    // Enviar el archivo
    readfile($tempFile);
    
    // Eliminar el archivo temporal
    unlink($tempFile);
    $endTotal = microtime(true);
    $totalTime = round($endTotal - $startTime, 2);
    error_log("exportarExcel: Archivo enviado y archivo temporal eliminado - Tiempo total: {$totalTime}s");
} else {
    error_log("exportarExcel: ERROR - El archivo temporal no existe");
    http_response_code(500);
    echo "Error: El archivo no pudo ser generado";
}

exit;

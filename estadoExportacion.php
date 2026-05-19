<?php
/**
 * estadoExportacion.php
 * El frontend hace polling cada 2 segundos.
 * Devuelve JSON con el estado del job.
 */

header('Content-Type: application/json; charset=UTF-8');

$jobId  = preg_replace('/[^a-f0-9]/', '', $_GET['job_id'] ?? '');
if ($jobId === '') {
    http_response_code(400);
    echo json_encode(['error' => 'job_id inválido']);
    exit;
}

$tmpDir    = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ecommerce_exports';
$doneFile  = $tmpDir . DIRECTORY_SEPARATOR . $jobId . '.done';
$errorFile = $tmpDir . DIRECTORY_SEPARATOR . $jobId . '.error';
$csvFile   = $tmpDir . DIRECTORY_SEPARATOR . $jobId . '.csv';

// Si pasaron más de 3 minutos sin done ni error, el proceso falló silenciosamente
$paramsFile = $tmpDir . DIRECTORY_SEPARATOR . $jobId . '_params.json';
if (!file_exists($doneFile) && !file_exists($errorFile) && file_exists($paramsFile)) {
    if (time() - filemtime($paramsFile) > 180) {
        file_put_contents($errorFile, 'El proceso tardó demasiado o no pudo iniciarse. Intente con un rango de fechas más pequeño.');
    }
}

if (file_exists($errorFile)) {
    echo json_encode([
        'listo' => false,
        'error' => true,
        'mensaje' => file_get_contents($errorFile),
    ]);
    exit;
}

if (file_exists($doneFile) && file_exists($csvFile)) {
    $info = json_decode(file_get_contents($doneFile), true);
    echo json_encode([
        'listo'  => true,
        'total'  => $info['total'] ?? 0,
        'job_id' => $jobId,
    ]);
    exit;
}

// Todavía procesando — calcular progreso aproximado por tamaño del CSV parcial
$progreso = 0;
if (file_exists($csvFile)) {
    $bytes = filesize($csvFile);
    // Estimación muy simple: cada registro ~200 bytes
    $progreso = min(95, (int)($bytes / 200));
}

echo json_encode([
    'listo'    => false,
    'progreso' => $progreso,
]);
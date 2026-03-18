<?php
/**
 * descargarExportacion.php
 * Sirve el CSV generado en background para descarga y luego lo borra.
 */

$jobId = preg_replace('/[^a-f0-9]/', '', $_GET['job_id'] ?? '');
if ($jobId === '') {
    http_response_code(400);
    exit('job_id inválido');
}

$tmpDir   = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ecommerce_exports';
$csvFile  = $tmpDir . DIRECTORY_SEPARATOR . $jobId . '.csv';
$doneFile = $tmpDir . DIRECTORY_SEPARATOR . $jobId . '.done';

if (!file_exists($csvFile)) {
    http_response_code(404);
    exit('Archivo no encontrado. Puede que ya haya sido descargado.');
}

$filename = 'pedidos_' . date('Y-m-d_H-i-s') . '.csv';

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($csvFile));
header('Pragma: no-cache');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

readfile($csvFile);

// Limpiar archivos temporales
@unlink($csvFile);
@unlink($doneFile);
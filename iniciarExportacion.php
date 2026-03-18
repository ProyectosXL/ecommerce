<?php
/**
 * iniciarExportacion.php
 * Recibe los filtros, genera un job_id, lanza procesarExportacion.php
 * en background y devuelve el job_id al frontend.
 */

header('Content-Type: application/json; charset=UTF-8');

// ── Generar job_id único ──────────────────────────────────────────────────────
$jobId = bin2hex(random_bytes(16));

// ── Directorio temporal ───────────────────────────────────────────────────────
$tmpDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ecommerce_exports';
if (!is_dir($tmpDir)) {
    mkdir($tmpDir, 0775, true);
}

// ── Recopilar parámetros del formulario ───────────────────────────────────────
$params = [
    'job_id'      => $jobId,
    'desde'       => $_POST['desde']       ?? $_GET['desde']       ?? date('Y-m-d'),
    'hasta'       => $_POST['hasta']       ?? $_GET['hasta']       ?? date('Y-m-d'),
    'tienda'      => $_POST['tienda']      ?? $_GET['tienda']      ?? '',
    'warehouse'   => $_POST['warehouse']   ?? $_GET['warehouse']   ?? '',
    'estado'      => $_POST['estado']      ?? $_GET['estado']      ?? '',
    'orden'       => $_POST['orden']       ?? $_GET['orden']       ?? '',
    'metodo_envio'=> $_POST['metodo_envio']?? $_GET['metodo_envio']?? '',
    'factura'     => $_POST['factura']     ?? $_GET['factura']     ?? '',
    'tmp_dir'     => $tmpDir,
];

// Guardar parámetros en archivo JSON para que el proceso background los lea
$paramsFile = $tmpDir . DIRECTORY_SEPARATOR . $jobId . '_params.json';
file_put_contents($paramsFile, json_encode($params, JSON_UNESCAPED_UNICODE));

// ── Lanzar proceso en background ─────────────────────────────────────────────
$phpBin      = PHP_BINARY;
$scriptPath  = __DIR__ . DIRECTORY_SEPARATOR . 'procesarExportacion.php';

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    // Windows
    $cmd = 'start /B "" "' . $phpBin . '" "' . $scriptPath . '" "' . $paramsFile . '" > NUL 2>&1';
    pclose(popen($cmd, 'r'));
} else {
    // Linux / Mac
    $cmd = '"' . $phpBin . '" "' . $scriptPath . '" "' . $paramsFile . '" > /dev/null 2>&1 &';
    exec($cmd);
}

echo json_encode(['job_id' => $jobId, 'ok' => true]);
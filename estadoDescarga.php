<?php
/**
 * estadoDescarga.php
 * Indica al frontend el estado de exportarPedidos.php.
 * Responde inmediatamente con JSON.
 */
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store');

$token = preg_replace('/[^a-z0-9]/', '', strtolower($_GET['token'] ?? ''));
if ($token === '') {
    echo json_encode(['iniciado' => false, 'terminado' => false]);
    exit;
}

$dir        = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ecommerce_exports' . DIRECTORY_SEPARATOR;
$statusFile = $dir . $token . '.started';
$doneFile   = $dir . $token . '.done';

// Limpiar caché de stat de PHP para obtener estado real del disco
clearstatcache(true, $statusFile);
clearstatcache(true, $doneFile);

$terminado = file_exists($doneFile);
if ($terminado) {
    // Limpiar el archivo .done para no detectarlo dos veces
    @unlink($doneFile);
}

echo json_encode([
    'iniciado'  => file_exists($statusFile) || $terminado,
    'terminado' => $terminado,
]);

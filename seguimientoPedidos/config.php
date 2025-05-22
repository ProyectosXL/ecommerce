
<?php
// Configuración inicial de la aplicación
require_once '../Class/Conexion.php';
require_once '../Class/Pedido.php';

// Inicializar objetos principales
$pedidos = new Pedido();
$sucursales = $pedidos->traerWarehouse();

// Configuración de sucursal por defecto
$sucursal = '2';
$articulos = $pedidos->buscarStockArticulo($sucursal);

// Configuración de zona horaria
date_default_timezone_set('America/Argentina/Buenos_Aires');

// Configuración de errores para desarrollo (remover en producción)
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
?>
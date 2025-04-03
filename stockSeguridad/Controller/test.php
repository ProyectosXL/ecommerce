
<?php

require_once '../Class/conexion.php';
$cid = new Conexion();
$cid_central = $cid->conectar();

$warehouse = $_POST['inputWarehouse'];
$cuenta= $_POST['inputCuenta'];
$rubro = $_POST['inputRubro'];
$stockSeguridad = $_POST['inputStock'];

var_dump($warehouse);


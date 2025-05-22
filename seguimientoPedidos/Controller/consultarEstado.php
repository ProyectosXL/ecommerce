<?php 

require_once $_SERVER['DOCUMENT_ROOT']. '/../ecommerce/Class/Pedido.php';

$nroOrder = $_POST['nroOrder'];

$pedido = new Pedido();

$estado = $pedido->consultarEstado($nroOrder);

echo json_encode($estado);

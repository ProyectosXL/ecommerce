<?php

require_once 'dsn.php';

$sql = "
SET DATEFORMAT YMD
EXEC SJ_ECOMMERCE_PEDIDOS '$desde', '$hasta', '$tienda'
";



?>
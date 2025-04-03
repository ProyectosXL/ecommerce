
<?php

$codArticu = $_POST['codArticu'];

require_once '../Class/cuenta.php';

$articulo = new Cuenta();
$articuloArray = $articulo->traerCuentaVtex($codArticu);

if($articuloArray){
    $response = array(
        'code' => 200, 
        'msg' => json_encode($articuloArray),
    );
}else{
    $response = array(
        'code' => 404, 
        'msg' => 'articulo no encontrado!',
    );
}

echo json_encode($response);
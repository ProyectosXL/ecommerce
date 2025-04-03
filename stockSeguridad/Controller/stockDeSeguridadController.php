<?php 
require_once '../Class/matriz.php';
$accion  = $_GET['accion'];


switch ($accion) {
    case 'cambiarEntornoUy':
        cambiarEntornoUy();
        break;
    
    case 'cambiarEntornoArg':
        cambiarEntornoArg();
        break;
    
    default:
        # code...
        break;
}



function cambiarEntornoUy () {

    $matriz = new Matriz();

    $data = $matriz->traerMatrizUy();

    echo json_encode($data);
}

function cambiarEntornoArg () {

    $matriz = new Matriz();

    $data = $matriz->traerMatriz();

    echo $data;
}

?>
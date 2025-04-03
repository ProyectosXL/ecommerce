<?php 

$accion = $_GET['accion'];


switch ($accion) {
    case 'traerCategorias':
        traerCategorias();
        break;
    
    default:
        # code...
        break;
}


function traerCategorias () {

    require_once "../Class/Cliente.php";
    require_once '../../vendor/autoload.php';

    $rubros = $_POST['rubros'];

    $cliente = new Cliente();

    $categorias = $cliente->traerCategorias($rubros);

    echo json_encode($categorias);

}
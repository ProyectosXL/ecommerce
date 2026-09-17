<?php 

$accion = $_GET['accion'];


switch ($accion) {
    case 'traerCategorias':
        traerCategorias();
        break;

    case 'traerLocalidades':
        traerLocalidades();
        break;

    case 'traerTiendas':
        traerTiendas();
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

/**
 * A diferencia de traerCategorias, estos endpoints reciben ARRAYS y los metodos
 * del modelo los bindean con ?. No replicar el patron de traerCategorias, que
 * recibe un fragmento de SQL ya armado en el navegador.
 */
function traerLocalidades () {

    require_once "../Class/Cliente.php";
    require_once '../../vendor/autoload.php';

    $provincias = isset($_POST['provincias']) ? (array) $_POST['provincias'] : [];

    $cliente = new Cliente();

    echo json_encode($cliente->traerLocalidades($provincias));

}

function traerTiendas () {

    require_once "../Class/Cliente.php";
    require_once '../../vendor/autoload.php';

    $provincias  = isset($_POST['provincias'])  ? (array) $_POST['provincias']  : [];
    $localidades = isset($_POST['localidades']) ? (array) $_POST['localidades'] : [];

    $cliente = new Cliente();

    echo json_encode($cliente->traerSucursales($provincias, $localidades));

}
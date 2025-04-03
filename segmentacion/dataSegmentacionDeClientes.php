<?php 

$cliente = new Cliente();
$rubros = $cliente->traerRubros();
$categorias = $cliente->traerCategorias();
$bancos = $cliente->traerBancos();


$desde = (isset($_GET['desde'])) ? $_GET['desde'] : null;
$hasta = (isset($_GET['hasta'])) ? $_GET['hasta'] : null;

$selectBanco     =   (isset($_GET['selectBanco'])) ? $_GET['selectBanco'] : [];
$selectRubro     =   (isset($_GET['selectRubro'])) ? $_GET['selectRubro'] : [];
$selectCategoria =   (isset($_GET['selectCategoria'])) ? $_GET['selectCategoria'] : [];
$selectRangoEtario = (isset($_GET['selectRangoEtario'])) ? $_GET['selectRangoEtario'] : [];
$arrayCategorias = [];
foreach ($selectCategoria as $x => $categoria) {


    $arrayCategorias[$x] = explode("-",$categoria)[1];

}
$clientes = [];

if($desde != null){

    $clientes = $cliente->traerClientes($desde,$hasta,$selectBanco,$selectRubro,$arrayCategorias,$selectRangoEtario);
    
    
    $total = number_format(count($clientes), 0, ',', '.');
}

?>
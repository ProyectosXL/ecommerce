<?php

$cliente = new Cliente();

$desde = (isset($_GET['desde'])) ? $_GET['desde'] : null;
$hasta = (isset($_GET['hasta'])) ? $_GET['hasta'] : null;

$selectBanco     =   (isset($_GET['selectBanco'])) ? $_GET['selectBanco'] : [];
$selectRubro     =   (isset($_GET['selectRubro'])) ? $_GET['selectRubro'] : [];
$selectCategoria =   (isset($_GET['selectCategoria'])) ? $_GET['selectCategoria'] : [];
$selectRangoEtario = (isset($_GET['selectRangoEtario'])) ? $_GET['selectRangoEtario'] : [];
$selectProvincia =   (isset($_GET['selectProvincia'])) ? $_GET['selectProvincia'] : [];
$selectLocalidad =   (isset($_GET['selectLocalidad'])) ? $_GET['selectLocalidad'] : [];
$selectTienda    =   (isset($_GET['selectTienda'])) ? $_GET['selectTienda'] : [];

// Los combos se cargan despues de leer $_GET porque localidades y tiendas se acotan
// por lo ya elegido: asi el encadenado sigue siendo correcto despues de dar Filtrar
// (recarga completa de pagina), sin depender del AJAX.
$rubros = $cliente->traerRubros();
$categorias = $cliente->traerCategorias();
$bancos = $cliente->traerBancos();
$provincias  = $cliente->traerProvincias();
$localidades = $cliente->traerLocalidades($selectProvincia);
$sucursales  = $cliente->traerSucursales($selectProvincia, $selectLocalidad);

$arrayCategorias = [];
foreach ($selectCategoria as $x => $categoria) {


    $arrayCategorias[$x] = explode("-",$categoria)[1];

}
$clientes = [];

if($desde != null){

    $clientes = $cliente->traerClientes(
        $desde,
        $hasta,
        $selectBanco,
        $selectRubro,
        $arrayCategorias,
        $selectRangoEtario,
        $selectTienda,
        $selectProvincia,
        $selectLocalidad
    );


    $total = number_format(count($clientes), 0, ',', '.');
}

?>

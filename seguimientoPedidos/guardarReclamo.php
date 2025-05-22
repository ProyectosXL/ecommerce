
<?php


require_once 'Class/Conexion.php';
require_once 'Class/Pedido.php';

$resolucion = $_POST['resolucion'];
$sucursal = $_POST['sucursal'];
$articulo = $_POST['articulo'];
$dataSecciones = json_decode($_POST['dataSecciones']);
$estado = $_POST['estado'];
$descripcion = $_POST['descripcion'];
$nro_pedido = $_POST['nroPedido'];
$fechaHora = $_POST['fechaHora'];
$nroOrden = $_POST['nroOrden'];
$cliente = $_POST['cliente'];
$prepara = $_POST['prepara'];
$modalCantidad = $_POST['modalCantidad'];
$estado = $_POST['estado'];
$modalCodigo = $_POST['modalCodigo'];

$data = [
    'resolucion' => $resolucion,
    'sucursal' => $sucursal,
    'articulo' => $articulo,
    'descripcion' => $descripcion,
    'estado' => $estado,
    'nro_pedido' => $nro_pedido,
    'fechaHora' => $fechaHora,
    'nroOrden' => $nroOrden,
    'cliente' => $cliente,
    'prepara' => $prepara,
    'modalCantidad' => $modalCantidad,
    'estado' => $estado,
    'modalCodigo' => $modalCodigo
];

$pedido = new Pedido();

$resultado = $pedido->guardarHistorialReclamo($data);



    if (!$resultado) {
        $sqlError = sqlsrv_errors();
        echo json_encode([
            'success' => false,
            'error' => 'Error al ejecutar la consulta SQL.',
            'sql_error' => $sqlError
        ]);
        exit;
    }
    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => 'Reclamo guardado exitosamente.'
    ]);
    exit;


// Respuesta en caso de acceso incorrecto
http_response_code(405);
echo json_encode([
    'success' => false,
    'error'   => 'Método no permitido.'
]);
exit;
?>

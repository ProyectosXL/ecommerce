
<?php
require 'Class/matriz.php';
require 'Class/cuenta.php';
require_once 'Class/rubro.php';

$matrizStockSeguridad = new Matriz();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matriz Stock Seg. Vtex</title>
    <link rel="icon" type="image/jpg" href="images/LOGO XL 2018.jpg">

    <?php 
        require_once $_SERVER['DOCUMENT_ROOT'] . '/ecommerce/assets/css/css.php';
    ?>
    <!-- Asegúrate de incluir jQuery antes que Bootstrap y Bootstrap Toggle -->

    
    <link rel="stylesheet" href="css/stockSeguridad.css" class="rel">


<style>
    /* Estilo personalizado para el interruptor con imágenes de banderas */
    .toggle-on {
        background-image: url('images/bandera_con_sol__55757_std.jpg'); /* Ruta de la imagen para el estado encendido (ARG) */
        background-size: contain; /* Ajusta el tamaño de la imagen para que se ajuste al contenedor */
        background-repeat: no-repeat; /* Evita la repetición de la imagen */
        height: 60px; /* Establece la altura del botón */
        width: 60px; /* Establece el ancho del botón */
    }

    .toggle-off {
        background-image: url('images/UY.png'); /* Ruta de la imagen para el estado apagado (UY) */
        background-size: contain; /* Ajusta el tamaño de la imagen para que se ajuste al contenedor */
        background-repeat: no-repeat; /* Evita la repetición de la imagen */
        height: 60px; /* Establece la altura del botón */
        width: 60px; /* Establece el ancho del botón */
    }
 
</style>

   
 

</head>

<body>

    <h3><i class="bi bi-pencil-square"></i> Administracion de Stock Seguridad Vtex</h3>
    <div id="boxLoading"></div> 
    <form action="#" id="menu">
        <div class="form-row contenedor">
            <div id="busqRapida">
                <label id="textBusqueda">Busqueda rapida:</label>
                <input type="text" id="textBox" placeholder="Sobre cualquier campo..." onkeyup="myFunction()" class="form-control form-control-sm"></input>
            </div>
            <div class="form-button">
                <div>
                   
                    <button type="button" class="btn btn-primary" id="btn_active" data-toggle="modal" data-target="#modalActive">Activar <i class="bi bi-check-circle-fill"></i></button>
                </div>
                <div>
                    <button type="button" class="btn btn-warning" id="btn_edit" data-toggle="modal" data-target="#modalParameters">Editar <i class="bi bi-pencil-square"></i></button>
                </div>
              
                <input type="checkbox" checked data-toggle="toggle" data-on="ARG" data-off="UY" class="custom-toggle" style="color:black; font-size: 0;" onchange="cambiarEntorno(this)" id="checkEntorno">


            </div>
        </div>
           
    <input  type="text" value="<?=$todasLasCuentas ?>" hidden>
    <textarea name="" id='todasLasCuentas' cols="30" rows="10" hidden></textarea>

    </form>

    <?php


        $todasLasSucursales = $matrizStockSeguridad->traerMatriz();
        $todasLasSucursales = json_decode($todasLasSucursales);

        $columnasFijas = ['ID', 'WAREHOUSE_ID', 'VTEX_CUENTA', 'DESC_SUCURSAL'];
        $columnasRubro = !empty($todasLasSucursales)
            ? array_filter(
                array_diff(array_keys(get_object_vars($todasLasSucursales[0])), $columnasFijas),
                fn($columna) => !is_numeric($columna)
            )
            : [];

    ?>
        <div class="table-responsive mt-4">
            <table class="table table-hover table-condensed table-striped text-center">
                <thead class="thead-dark" id="tableHead">
                    <th scope="col" style="width: 1%; display:none;">ID</th>
                    <th scope="col" style="width: 1%; display:none;">WAREHOUSE</th>
                    <th scope="col" style="width: 10%">CUENTA VTEX</th>
                    <th scope="col" style="width: 10%">SUCURSAL</th>
                    <?php foreach ($columnasRubro as $columna) { ?>
                        <th scope="col" style="width: 1%"><?= str_replace('_', ' ', $columna) ?></th>
                    <?php } ?>
                </thead>

                <tbody id="table">
                    <?php
                    foreach ($todasLasSucursales as $valor => $value) {
                    ?>

                        <tr>
                            <td style="display:none;"><?= $value->ID; ?></td>
                            <td style="display:none;"><?= $value->WAREHOUSE_ID; ?></td>
                            <td><?= $value->VTEX_CUENTA; ?></td>
                            <td><?= $value->DESC_SUCURSAL; ?></td>
                            <?php foreach ($columnasRubro as $columna) { ?>
                                <td><input type="number" class="inputNumber" name="<?= $columna ?>" value="<?= $value->$columna ?>" disabled></td>
                            <?php } ?>
                        </tr>
                    <?php
                    }
                    ?>

                </tbody>

            </table>
        </div>

        
        <?php
        
            include 'activar.php';
            include 'activarUruguay.php';
            include 'parametrizar.php';
            include 'parametrizarUy.php';

        ?>
</body>

<?php 
    require_once $_SERVER['DOCUMENT_ROOT'] . '/ecommerce/assets/js/js.php';
?>
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script>
      $(document).ready( function () {
        
            document.querySelector(".toggle").style.width="40px"
            document.querySelector(".toggle-on").style.fontSize="0"
            document.querySelector(".toggle-off").style.fontSize="0"


      })
</script>
</html>

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="https://kit.fontawesome.com/222ca0945e.js" crossorigin="anonymous"></script>

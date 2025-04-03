<?php
require_once "Class/Cliente.php";
require_once '../vendor/autoload.php';
include "dataSegmentacionDeClientes.php";
// var_dump($clientes);

?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Segmentacion De Clientes</title>
        <?php 
            require_once $_SERVER['DOCUMENT_ROOT'].'/ecommerce/assets/css/css.php';
        ?>
        

        </link>
    </head>
    
    <body>

        <div class="alert alert-secondary">
            <div class="page-wrapper bg-secondary p-b-100 pt-2 font-robo">
                <div class="wrapper wrapper--w680"><div style="color:white; text-align:center"><h6>Segmentacion de Clientes</h6></div>
                    <div class="card card-1">
                        <div class="row" style = "height:845px;width:100%;margin-left:10px;margin-top:5px;margin-bottom:5px">
                        <div id="boxLoading"></div>
                            <div class="col-3" style="border:solid 1px;">
                                <form action="">
                                    <div style="margin-top:30px">
                                        <button type="submit"  id="btnFiltrar" name="submit"class="btn btn-primary btn-block submit"style="height:40px" onclick="mostrarSpiner()">Filtrar <i class="bi bi-funnel-fill" style="color:white"></i></button>
                                    </div>
                                    <div class="contenedor" style="margin-top:10px">
                                        <div class="row">
                                            <div class="col">
                                                <label>Desde:</label>
                                                <input type="date"  name="desde" value="<?= $desde ?>" style="width:120px">
                                            </div>

                                            <div class="col">
                                                <label>Hasta:</label>
                                                <input type="date" name="hasta" value="<?= $hasta ?>" style="width:120px">
                                            </div>
                                        </div>
                                    </div>
                                <div hidden  id="conteo"><?= (isset($total)) ? $total  : "0" ?></div>
                                    <div class="row">

                                        <label style="margin-left:20px"><i class="bi bi-bank2"></i> Banco</label>

                                        <select id="selectBanco" name="selectBanco[]" style="width:100%;margin-left:20px" class="js-states form-control select2-hidden-accessible" multiple="" data-select2-id="selectBanco" tabindex="-1" aria-hidden="true">
                                           
                                            <?php 
                                                foreach ($bancos as  $banco) {

                                                    $text = '<option value="'.$banco['COD_CTA_BANCO'].'?'.$banco['ID_CTA_BANCO'].'"';
                
                                                    if(count($selectBanco) > 0){
                                        
                                                        if(in_array($banco['COD_CTA_BANCO'].'?'.$banco['ID_CTA_BANCO'],$selectBanco)){

                                                            $text = $text.'selected="selected"';

                                                        }
                                                    }
                                                    
                                                    $text = $text.'>'.$banco['DESC_CTA_BANCO'].'</option>';
                                                    echo $text;
                                                }
                                            ?>

                                        </select>
                                    </div>

                                    <div class="row" style="margin-top:5px">

                                        <label style="margin-left:20px"><i class="bi bi-bag"></i> Rubro</label>

                                        <select id="selectRubro" name="selectRubro[]" style="width:100%;margin-left:20px" class="js-states form-control select2-hidden-accessible" multiple="" onchange="filtrarCategoria()" data-select2-id="selectRubro" tabindex="-1" aria-hidden="true">
                                            
                                            <?php 
                                                foreach ($rubros as  $rubro) {
                                                    $text = '<option value="'.$rubro['DESCRIP'].'"';

                                                    if(count($selectRubro) > 0){
                                        
                                                        if(in_array($rubro['DESCRIP'],$selectRubro)){

                                                            $text = $text.'selected="selected"';

                                                        }
                                                    }

                                                    $text = $text.'>'.$rubro['DESCRIP'].'</option>';
                                                    echo $text;
                                                }
                                            ?>

                                        </select>
                                    </div>

                                    <div class="row" style="margin-top:5px">

                                        <label style="margin-left:20px"><i class="bi bi-briefcase"></i> Categoria </label>

                                        <select id="selectCategoria" name="selectCategoria[]" style="width:100%;margin-left:20px" class="js-states form-control select2-hidden-accessible" multiple="" data-select2-id="selectCategoria" tabindex="-1" aria-hidden="true">
                                           
                                            <?php 
                                                foreach ($categorias as  $categoria) {
                                     
                                                    $text = '<option value="'.$categoria['RUBRO'].'-'.$categoria['CATEGORIA'].'"';

                                                    if(count($selectCategoria) > 0){

                                                        foreach ($selectCategoria as $caregoria) {

                                                            $rubro = explode("-",$caregoria)[0];
                                                            $descCategoria = explode("-",$caregoria)[1];

                                                            if($categoria['RUBRO'] == $rubro && $categoria['CATEGORIA'] == $descCategoria){

                                                                $text = $text.'selected="selected"';
                                                                
                                                            }
                                                            
                                                        }

                                                    }

                                                    $text = $text.'>'.$categoria['CATEGORIA'].'</option>';
                                                    echo $text;
                                                }
                                            ?>

                                        </select>
                                    </div>

                                    <div class="row" style="margin-top:5px">

                                        <label style="margin-left:20px"><i class="bi bi-person"></i> Rango Etario</label>

                                        <select id="selectRangoEtario"  name="selectRangoEtario[]" style="width:100%;margin-left:20px" class="js-states form-control select2-hidden-accessible" multiple="" data-select2-id="selectRangoEtario" tabindex="-1" aria-hidden="true">
                                           
                                            <option value="10-14" <?php if(in_array("10-14",$selectRangoEtario)) echo "selected" ?> >10-14</option>
                                            <option value="15-19" <?php if(in_array("15-19",$selectRangoEtario)) echo "selected" ?> >15-19</option>
                                            <option value="20-24" <?php if(in_array("20-24",$selectRangoEtario)) echo "selected" ?> >20-24</option>
                                            <option value="25-29" <?php if(in_array("25-29",$selectRangoEtario)) echo "selected" ?> >25-29</option>
                                            <option value="30-34" <?php if(in_array("30-34",$selectRangoEtario)) echo "selected" ?> >30-34</option>
                                            <option value="35-39" <?php if(in_array("35-39",$selectRangoEtario)) echo "selected" ?> >35-39</option>
                                            <option value="40-44" <?php if(in_array("40-44",$selectRangoEtario)) echo "selected" ?> >40-44</option>
                                            <option value="45-49" <?php if(in_array("45-49",$selectRangoEtario)) echo "selected" ?> >45-49</option>
                                            <option value="50-54" <?php if(in_array("50-54",$selectRangoEtario)) echo "selected" ?> >50-54</option>
                                            <option value="55-59" <?php if(in_array("55-59",$selectRangoEtario)) echo "selected" ?> >55-59</option>
                                            <option value="60-64" <?php if(in_array("60-64",$selectRangoEtario)) echo "selected" ?> >60-64</option>
                                            <option value="65-69" <?php if(in_array("65-69",$selectRangoEtario)) echo "selected" ?> >65-69</option>
                                            <option value="70-74" <?php if(in_array("70-74",$selectRangoEtario)) echo "selected" ?> >70-74</option>
                                            <option value="75-79" <?php if(in_array("75-79",$selectRangoEtario)) echo "selected" ?> >75-79</option>
                                            <option value="80-84" <?php if(in_array("80-84",$selectRangoEtario)) echo "selected" ?> >80-84</option>
                                            <option value="85-89" <?php if(in_array("85-89",$selectRangoEtario)) echo "selected" ?> >85-89</option>
                                            <option value="90-94" <?php if(in_array("90-94",$selectRangoEtario)) echo "selected" ?> >90-94</option>

                                        </select>
                                    </div>
                                </form>

                            </div>

                            <div class="col"  style="border:solid 1px;margin-left:20px;margin-right:20px">
                        
                                    <div class="row" style="margin-left:50px">
                                        <h3><i class="bi bi-archive-fill" style="margin-right: 20px; font-size: 50px"></i>Datos de Clientes</h3>
                                    </div>
                            
                                    <div class="row" style="margin-left:5px;margin-right:30px;;margin-top:30px">

                                        <div class="table-responsive" id="tableIndex">
                                            <div class="table-wrapper" id="tableIndex">
                                                <table class="table table-hover table-condensed table-striped text-center" id="tablaClientes" style="width: 100%;" cellspacing="0" data-page-length="100">
                                                    
                                                    <thead class="thead-dark" style="font-size: small;">
                                                        <th scope="col" style="width: 6%">NOMBRE Y APELLIDO</th>
                                                        <th scope="col" style="width: 15%">DNI</th>
                                                        <th scope="col" style="width: 8%">RANGO ETARIO</th>
                                                        <th scope="col" style="width: 8%">EMAIL</th>
                                                        <th scope="col" style="width: 8%">CANT.COMPRAS</th>

                                                    </thead>

                                                    <tbody id="tableVb" style="font-size: small;">
                                                        <?php 
                                                            foreach ($clientes as $key => $value) {
                                           
                                                                $dataArray = (array) $value;
                                                                $cantidad = 0;
                                                                if(!isset($dataArray['ARTICULOS'])){
                                                                    break;
                                                                }
                                                                foreach ($dataArray['ARTICULOS'] as $v) {
                                                                    $cantidad += $v['CANTIDAD'];
                                                                } 

                                                            
                                                                echo '<tr>';
                                                                    echo '<td>'.$value['NOMBRE_CLI'].'</td>';
                                                                    echo '<td>'.$value['DNI'].'</td>';
                                                                    echo '<td>'.$value['RANGO_ETARIO'].'</td>';
                                                                    echo '<td>'.$value['E_MAIL'].'</td>';
                                                                    echo '<td>'.$cantidad.'</td>';
                                                                echo '</tr>';
                                                            }
                                                        ?>
                                                    </tbody>

                                                </table>
                                            </div>
                                        </div>

                                    </div>
                        
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap4.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
        <script src="//cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="js/segmentacionDeClientes.js"></script>
    </body>

    </html>
    <script>    
mostrarSpiner()
        $(document).ready( function () {

        $('#tablaClientes').DataTable({
            "bLengthChange": false,
            "bInfo": false,
            "aaSorting": false,
            'columnDefs': [
                {
                    "targets": "_all", 
                    "className": "text-center",
                    "sortable": false,
             
                },
            ],
            "oLanguage": {
  
                "sSearch": "Busqueda rapida:",
                "sSearchPlaceholder" : "Sobre cualquier campo"
                

            },
        });
        $("#tablaClientes_filter").append('<button class="btn btn-success btn_exportar" style="margin-bottom:4px;margin-left:10px;height:40px;margin-right:5px" onclick ="exportTable()"> Exportar<i class="bi bi-file-earmark-excel"></i></button>');
        $('.dataTables_filter input[type="search"]').css(
            {'height':'40px'}
        );


        let newdiv2 = document.createElement( "strong" );
        let newdiv1 =  "<?= $total ?> Registros Encontrado" ;
        newdiv2.append(newdiv1)

        $("#tablaClientes_filter").parent().parent().children()[0].appendChild(newdiv2);
        document.querySelector("#boxLoading").classList.remove("loading")
    
    } );

    // document.querySelector("#selectBanco").selectedOptions[0].value
    </script>

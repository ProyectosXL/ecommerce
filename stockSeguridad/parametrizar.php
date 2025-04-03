<?php

require_once 'Class/cuenta.php'; 

$cuenta2 = new Cuenta();
$todasLasCuentas = $cuenta2->traerCuentas3();
$todasLasCuentas = json_decode($todasLasCuentas);

?>

<!-- Modal -->
<div class="modal fade hide" id="modalParameters" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Establecer stock de seguridad</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!--          <form action="Controller/guardarForm.php" method="POST"> -->
                <form>
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Warehouse:</label>
                        <select id="inputWarehouse2" class="form-control form-control-sm selected" name="warehouse">
                            <option selected disabled>Seleccione el ID</option>
                            <?php

                            foreach ($todasLasCuentas as $valor => $value) {
                                /* $cuenta=$value-> */
                            ?>
                                <option id="WAREHOUSE_ID2" value="<?= $value->WAREHOUSE_ID; ?>"><?= $value->WAREHOUSE_ID.' - '.$value->DESCRIPCION?></option>
                            <?php
                            }
                            ?>
                        </select>

                        <label for="recipient-name" class="col-form-label">Cuenta:</label>
                        <input value="" type="text" class="form-control cuentaVtex" id="inputCuentaEditar" name="cuenta" disabled>
                        <input value="" type="text" id="localCuenta" disabled hidden>
                    </div>

                    <div class="form-group">
                        <!-- <label for="recipient-name" class="col-form-label">Rubro:</label> -->
                        <div class="contRubro">
                            <select id="inputRubro" class="form-control form-control-sm selected" name="rubro[]">
                                <option selected disabled>Seleccione rubro</option>
                                <?php
                                $todosLosRubros = json_decode($todosLosRubros);
                                foreach ($todosLosRubros as $valor => $value) {
                                ?>
                                    <option value="<?= $value->PATH_CLASIF; ?>"><?= $value->RUBRO; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                            <i class="fa-solid fa-plus" id="agregarRubro"></i>
                        </div>
                        <table id="tablaRubroStockSeguridad" name="rubros&stockSeguridad">
                            <thead>
                                <td>Rubro</td>
                                <td>Stock Seguridad</td>
                            </thead>
                            <!--  <tr><td>Calzado</td><td><input type="number" ></td> </tr>
                                <tr><td>Cuero</td><td><input type="number"></td> </tr> -->
                        </table>
                    </div>
                    <!--  <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Stock Seguridad:</label>
                            <input value="" type="number" min="1" class="form-control" id="inputStock" name="inputStock">
                        </div> -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btnClose" data-dismiss="modal"><i class="bi bi-x-circle"></i> Cerrar</button>
                <button type="button" id="btnSaveFormEditar" class="btn btn-info btnSaveForm " onclick ="guardarForm()"><i class="bi bi-check-circle-fill"></i> Activar</button>
            </div>
        </div>
    </div>
    <!-- <script src="js/parametrizar.js"></script> -->
</div>
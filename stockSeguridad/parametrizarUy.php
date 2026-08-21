<?php

require_once 'Class/cuenta.php'; 

$cuenta2 = new Cuenta();
$todasLasCuentas = $cuenta2->traerCuentas3("uy");
$todasLasCuentas = json_decode($todasLasCuentas);

?>

<!-- Modal -->
<div class="modal fade hide" id="modalParametersUy" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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
                        <div class="contWarehouses">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkTodosWarehouseUy">
                                <label class="form-check-label" for="checkTodosWarehouseUy"><b>Seleccionar todos</b></label>
                            </div>
                            <div class="listaWarehouse">
                                <?php
                                foreach ($todasLasCuentas as $valor => $value) {
                                ?>
                                    <div class="form-check">
                                        <input class="form-check-input checkWarehouseUy2" type="checkbox" id="wh2uy_<?= $value->WAREHOUSE_ID; ?>" value="<?= $value->WAREHOUSE_ID; ?>" data-cuenta="<?= $value->VTEX_CUENTA; ?>">
                                        <label class="form-check-label" for="wh2uy_<?= $value->WAREHOUSE_ID; ?>"><?= $value->WAREHOUSE_ID . ' - ' . $value->DESCRIPCION . ' (' . $value->VTEX_CUENTA . ')'; ?></label>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <!-- <label for="recipient-name" class="col-form-label">Rubro:</label> -->
                        <div class="contRubro">
                            
                            <select id="inputRubroUy" class="form-control form-control-sm selected" name="rubro[]">
                                <option selected disabled>Seleccione rubro</option>
                                <?php

                                foreach ($todosLosRubros as $valor => $value) {
                                ?>
                                    <option value="<?= $value->PATH_CLASIF; ?>"><?= $value->RUBRO; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                            <i class="fa-solid fa-plus" id="agregarRubroUy"></i>
                        </div>
                        <table id="tablaRubroStockSeguridadUy" name="rubros&stockSeguridad">
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
                <button type="button" id="btnSaveFormEditarUy" class="btn btn-info btnSaveForm " onclick="guardarForm('uy')"><i class="bi bi-check-circle-fill"></i> Activar</button>
            </div>
        </div>
    </div>
    <!-- <script src="js/parametrizar.js"></script> -->
</div>
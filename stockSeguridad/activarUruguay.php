<?php

require_once 'Class/cuenta.php';
require_once 'Class/rubro.php';

$rubro = new Rubro();
$todosLosRubros = $rubro->traerRubros('uy');

$cuenta = new Cuenta();
$todasLasCuentas = $cuenta->traerCuentas('uy');
$todasLasCuentas = json_decode($todasLasCuentas); 

?>

<!-- Modal -->
<div class="modal fade hide" id="modalActiveUruguay" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Activar Warehouse</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <form>
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Warehouse:</label>
                            <select id="inputWarehouseUy" onchange="completarModalUY(this)" class="form-control form-control-sm selected" name="warehouseUY" >
                                <option selected disabled>Seleccione el ID</option>
                                <?php
                            
                                foreach($todasLasCuentas as $valor => $value){
                                /* $cuenta=$value-> */
                                ?>
                                <option id="WAREHOUSE_ID" value="<?= $value->WAREHOUSE_ID; ?>" valor-cuenta="<?= $value->VTEX_CUENTA ?>"><?= $value->WAREHOUSE_ID; ?></option>
                                <?php   
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Cuenta:</label>
                            <input value="" type="text" class="form-control cuentaVtex" id="inputCuentaUy" name="inputCuentaUy" disabled>
                        </div>
                        </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btnClose" data-dismiss="modal"><i class="bi bi-x-circle"></i> Cerrar</button>
                    <button type="button" class="btn btn-info btnSaveForm" onclick="activarWarehouse('uy');"><i class="bi bi-check-circle-fill"></i> Activar</button>
                </div>
                </div>
            </div>
        </div>

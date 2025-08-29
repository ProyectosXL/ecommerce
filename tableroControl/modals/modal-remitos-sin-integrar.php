<?php
// modal-remitos-sin-integrar.php
?>
<div class="modal fade" id="modalRemitosSinIntegrar" tabindex="-1" aria-labelledby="modalRemitosSinIntegrarLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title">
                    <i class="fas fa-file-invoice"></i> Remitos Abastecimiento Sin Ingresar
                </h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" onclick="exportarRemitosSinIntegrar()">
                        <i class="fas fa-file-excel me-2"></i>Exportar
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="tablaRemitosSinIntegrar">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Canal</th>
                                <th>N° Comprobante</th>
                                <th class="text-end">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (isset($remitosSinIntegrar) && is_array($remitosSinIntegrar)): ?>
                            <?php foreach ($remitosSinIntegrar as $remito): ?>
                                <tr>
                                    <td><?php echo $remito->FECHA_MOV->format('d/m/Y'); ?></td>
                                    <td><?php echo htmlspecialchars($remito->COD_PRO_CL); ?></td>
                                    <td><?php echo htmlspecialchars($remito->N_COMP); ?></td>
                                    <td class="text-end"><?php echo number_format($remito->CANTIDAD, 0); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

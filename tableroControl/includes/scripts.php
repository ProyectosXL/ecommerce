
<!-- includes/scripts.php -->

<!-- Scripts personalizados -->
<script src="js/modal-fix.js"></script>
<script src="js/chart-config.js"></script>
<script src="js/export-functions.js"></script>
<script src="js/refresh-functions.js"></script>

<script>
    // Definir chartData como variable global para que esté disponible en chart-config.js
    const chartData = {
        labels: [<?php 
            $ncrData = $control->traerNcrRealizadas();
            if (!empty($ncrData)) {
                echo implode(',', array_map(function($row) {
                    return "'" . $row->FECHA_EMIS->format('d/m/Y') . "'";
                }, $ncrData));
            }
        ?>],
        values: [<?php 
            if (!empty($ncrData)) {
                echo implode(',', array_map(function($row) {
                    return $row->CANT_NCR;
                }, $ncrData));
            }
        ?>]
    };
    
    // Inicializar tooltips de Bootstrap
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
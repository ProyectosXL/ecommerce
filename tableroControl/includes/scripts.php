
<!-- includes/scripts.php -->

<!-- Scripts personalizados -->
<script src="js/modal-fix.js"></script>
<script src="js/chart-config.js"></script>
<script src="js/export-functions.js"></script>
<script src="js/refresh-functions.js"></script>
<script src="js/tab-loader.js"></script>
<script src="js/modal-loader.js"></script>

<script>
    // Inicializar tooltips de Bootstrap (los de la página estática; los de cards/modales se init por tab-loader/modal-loader)
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
            new bootstrap.Tooltip(el);
        });
    });
</script>

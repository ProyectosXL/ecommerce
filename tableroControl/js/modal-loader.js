// js/modal-loader.js
// Carga lazy del contenido de modales vía AJAX

(function () {
    'use strict';

    // Cache en memoria: { loaderKey: { html, extra } }
    const modalCache = {};

    const ENDPOINT = 'includes/modal-loader.php';

    // ── Carga de contenido de modal ────────────────────────────────────────────

    function loadModalDetail(modalEl, loaderKey, force) {
        if (!force && modalCache[loaderKey]) {
            injectModalContent(modalEl, modalCache[loaderKey]);
            return;
        }

        setModalLoading(modalEl);

        fetch(`${ENDPOINT}?modal=${encodeURIComponent(loaderKey)}`)
            .then(r => r.json())
            .then(data => {
                if (!data.success) throw new Error(data.error || 'Error desconocido');

                const cached = { html: data.html, extra: data.extra || '' };
                modalCache[loaderKey] = cached;
                injectModalContent(modalEl, cached);
            })
            .catch(err => {
                const tbody = modalEl.querySelector('.modal-lazy-tbody');
                if (tbody) {
                    const cols = tbody.closest('table') ? tbody.closest('table').querySelectorAll('thead th').length || 6 : 6;
                    tbody.innerHTML =
                        `<tr><td colspan="${cols}" class="text-center text-danger">` +
                        `<i class="fas fa-exclamation-triangle me-2"></i>Error: ${err.message}</td></tr>`;
                }
            });
    }

    function injectModalContent(modalEl, cached) {
        const tbody = modalEl.querySelector('.modal-lazy-tbody');
        if (tbody) tbody.innerHTML = cached.html;

        const extraEl = modalEl.querySelector('.modal-lazy-extra');
        if (extraEl) extraEl.innerHTML = cached.extra || '';

        // Re-inicializar tooltips dentro del modal
        if (typeof bootstrap !== 'undefined') {
            modalEl.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                new bootstrap.Tooltip(el);
            });
        }
    }

    function setModalLoading(modalEl) {
        const tbody = modalEl.querySelector('.modal-lazy-tbody');
        if (!tbody) return;
        const cols = tbody.closest('table')
            ? tbody.closest('table').querySelectorAll('thead th').length || 6
            : 6;
        tbody.innerHTML =
            `<tr><td colspan="${cols}" class="text-center py-4">` +
            `<div class="spinner-border spinner-border-sm text-primary me-2" role="status">` +
            `<span class="visually-hidden">Cargando...</span></div>Cargando...</td></tr>`;

        const extraEl = modalEl.querySelector('.modal-lazy-extra');
        if (extraEl) extraEl.innerHTML = '';
    }

    // ── Refresh forzado (invalida caché) ───────────────────────────────────────

    function refreshModalDetail(modalEl) {
        const loaderKey = modalEl.dataset.modalLoader;
        if (!loaderKey) return;
        delete modalCache[loaderKey];
        loadModalDetail(modalEl, loaderKey, true);
    }

    // ── Inicialización ─────────────────────────────────────────────────────────

    document.addEventListener('DOMContentLoaded', function () {
        // Disparar carga en cada apertura de modal que tenga data-modal-loader
        document.addEventListener('show.bs.modal', function (e) {
            const modalEl   = e.target;
            const loaderKey = modalEl.dataset.modalLoader;
            if (!loaderKey) return; // modal sin lazy-load (e.g. modalNcr)
            loadModalDetail(modalEl, loaderKey);
        });
    });

    // ── Exportar al scope global ───────────────────────────────────────────────

    window.refreshModalDetail = refreshModalDetail;

})();

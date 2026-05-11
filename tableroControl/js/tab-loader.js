// js/tab-loader.js
// Carga lazy de pestañas y refresh granular de cards

(function () {
    'use strict';

    // Mapeo de nombre de pestaña → endpoint data-loader
    const TAB_ENDPOINTS = {
        'documentacion':         'includes/data-loader-documentacion.php',
        'integraciones':         'includes/data-loader-integraciones.php',
        'operaciones-central':   'includes/data-loader-operaciones-central.php',
        'operaciones-sucursales':'includes/data-loader-operaciones-sucursales.php',
        'uruguay':               'includes/data-loader-uruguay.php',
    };

    // Mapeo de nombre de pestaña → id del botón Actualizar
    const TAB_BTN_IDS = {
        'documentacion':         'btnActualizarDocumentacion',
        'integraciones':         'btnActualizarIntegraciones',
        'operaciones-central':   'btnActualizarOperacionesCentral',
        'operaciones-sucursales':'btnActualizarOperacionesSucursales',
        'uruguay':               'btnActualizarUruguay',
    };

    // Cache en memoria: { tabName: htmlString }
    const tabCache = {};

    // ── Helpers ────────────────────────────────────────────────────────────────

    function getContainer(tabName) {
        return document.querySelector(`[data-tab-container="${tabName}"]`);
    }

    function setLoading(container) {
        container.innerHTML =
            '<div class="d-flex justify-content-center py-5">' +
            '<div class="spinner-border text-primary" role="status">' +
            '<span class="visually-hidden">Cargando...</span></div></div>';
    }

    function applyBadges(badges) {
        if (!badges) return;
        Object.entries(badges).forEach(([tabId, count]) => {
            actualizarBadgeTab(tabId, count);
        });
    }

    function applyChartData(chartData) {
        if (chartData) window.chartData = chartData;
    }

    function reinitTooltips(container) {
        if (typeof bootstrap === 'undefined') return;
        container.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            new bootstrap.Tooltip(el);
        });
    }

    // ── Carga de pestaña completa ──────────────────────────────────────────────

    function loadTab(tabName, force) {
        if (!force && tabCache[tabName]) return; // ya cargado

        const endpoint = TAB_ENDPOINTS[tabName];
        if (!endpoint) return;

        const container = getContainer(tabName);
        if (!container) return;

        setLoading(container);

        fetch(endpoint)
            .then(r => r.json())
            .then(data => {
                if (!data.success) throw new Error(data.error || 'Error desconocido');

                tabCache[tabName] = data.html;
                container.innerHTML = data.html;
                reinitTooltips(container);
                applyBadges(data.badges);
                applyChartData(data.chartData);
                actualizarTimestamp(data.timestamp);
            })
            .catch(err => {
                container.innerHTML =
                    '<div class="alert alert-danger m-3"><i class="fas fa-exclamation-triangle me-2"></i>' +
                    'Error al cargar la pestaña: ' + err.message + '</div>';
            });
    }

    // ── Refresh de pestaña completa (invalida caché) ───────────────────────────

    function refreshTab(tabName) {
        const btnId = TAB_BTN_IDS[tabName];
        const btn   = btnId ? document.getElementById(btnId) : null;
        const icon  = btn ? btn.querySelector('i') : null;

        if (btn)  btn.disabled = true;
        if (icon) icon.classList.add('fa-spin');

        delete tabCache[tabName]; // invalidar caché

        const endpoint = TAB_ENDPOINTS[tabName];
        if (!endpoint) return;

        const container = getContainer(tabName);
        if (!container) return;

        fetch(endpoint)
            .then(r => r.json())
            .then(data => {
                if (!data.success) throw new Error(data.error || 'Error desconocido');

                tabCache[tabName] = data.html;
                container.innerHTML = data.html;
                reinitTooltips(container);
                applyBadges(data.badges);
                applyChartData(data.chartData);
                actualizarTimestamp(data.timestamp);
            })
            .catch(err => {
                mostrarError('Error al actualizar ' + tabName + ': ' + err.message);
            })
            .finally(() => {
                if (btn)  btn.disabled = false;
                if (icon) icon.classList.remove('fa-spin');
            });
    }

    // ── Refresh de card individual ─────────────────────────────────────────────

    function refreshCard(tabName, cardId) {
        const endpoint = TAB_ENDPOINTS[tabName];
        if (!endpoint) return;

        // Localizar el elemento card por data-card-id
        const cardEl = document.querySelector(`[data-card-id="${cardId}"]`);
        const btn     = cardEl ? cardEl.querySelector('.btn-card-refresh') : null;
        const icon    = btn ? btn.querySelector('i') : null;

        if (btn)  { btn.disabled = true; }
        if (icon) { icon.classList.add('fa-spin'); }

        // Invalidar caché de la pestaña completa para que el próximo refreshTab sea honesto
        delete tabCache[tabName];

        fetch(`${endpoint}?card=${encodeURIComponent(cardId)}`)
            .then(r => r.json())
            .then(data => {
                if (!data.success) throw new Error(data.error || 'Error desconocido');

                if (cardEl) {
                    // Reemplazar la card en el DOM preservando su posición
                    const tmp = document.createElement('div');
                    tmp.innerHTML = data.html;
                    const newCard = tmp.firstElementChild;
                    if (newCard) {
                        cardEl.replaceWith(newCard);
                        reinitTooltips(newCard);
                    }
                }
                actualizarTimestamp(data.timestamp);
            })
            .catch(err => {
                mostrarError('Error al actualizar card: ' + err.message);
                if (btn)  btn.disabled = false;
                if (icon) icon.classList.remove('fa-spin');
            });
    }

    // ── Inicialización ─────────────────────────────────────────────────────────

    document.addEventListener('DOMContentLoaded', function () {
        // Cargar documentacion al inicio
        loadTab('documentacion');

        // Lazy-load al activar una pestaña por primera vez
        document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tabEl => {
            tabEl.addEventListener('shown.bs.tab', function (e) {
                const target = e.target.getAttribute('data-bs-target') || e.target.getAttribute('href');
                if (!target) return;

                // Derivar nombre de pestaña desde el id del panel (e.g. "#documentacion" → "documentacion")
                const tabName = target.replace('#', '');
                if (TAB_ENDPOINTS[tabName]) {
                    loadTab(tabName);
                }
            });
        });
    });

    // ── Exportar al scope global ───────────────────────────────────────────────

    window.loadTab     = loadTab;
    window.refreshTab  = refreshTab;
    window.refreshCard = refreshCard;

})();

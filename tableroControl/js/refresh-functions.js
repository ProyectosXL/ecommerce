// js/refresh-functions.js
// Funciones de refresco de pestañas — delegan en tab-loader.js

function actualizarDocumentacion()         { refreshTab('documentacion'); }
function actualizarIntegraciones()         { refreshTab('integraciones'); }
function actualizarOperacionesCentral()    { refreshTab('operaciones-central'); }
function actualizarOperacionesSucursales() { refreshTab('operaciones-sucursales'); }
function actualizarUruguay()               { refreshTab('uruguay'); }

// ── Utilidades ─────────────────────────────────────────────────────────────────

function actualizarTimestamp(timestamp) {
    const el = document.getElementById('ultimaActualizacion');
    if (el) el.innerHTML = '<i class="fas fa-clock"></i> Última actualización: ' + timestamp;
}

function actualizarBadgeTab(tabId, totalPendientes) {
    const tab = document.getElementById(tabId);
    if (!tab) return;

    let badge = tab.querySelector('.badge.bg-danger');

    if (totalPendientes > 0) {
        if (badge) {
            badge.textContent = totalPendientes;
        } else {
            badge = document.createElement('span');
            badge.className = 'badge bg-danger ms-1';
            badge.textContent = totalPendientes;
            tab.appendChild(badge);
        }
    } else if (badge) {
        badge.remove();
    }
}

function mostrarError(mensaje) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
    alertDiv.style.zIndex = '9999';
    alertDiv.innerHTML =
        '<strong>Error:</strong> ' + mensaje +
        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';

    document.body.appendChild(alertDiv);
    setTimeout(() => alertDiv.remove(), 5000);
}

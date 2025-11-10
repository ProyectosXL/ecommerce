// js/refresh-functions.js
// Funciones para actualizar cada pestaña individualmente

/**
 * Actualizar pestaña de Documentación
 */
function actualizarDocumentacion() {
    const btn = document.getElementById('btnActualizarDocumentacion');
    const icon = btn.querySelector('i');
    
    // Deshabilitar botón y mostrar animación
    btn.disabled = true;
    icon.classList.add('fa-spin');
    
    fetch('includes/data-loader-documentacion.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar timestamp
                actualizarTimestamp(data.timestamp);
                
                // Actualizar badge del tab
                actualizarBadgeTab('documentacion-tab', data.totalPendientes);
                
                // Recargar la página para mostrar los nuevos datos
                // En una implementación más compleja, se actualizarían los elementos del DOM directamente
                location.reload();
            } else {
                mostrarError('Error al actualizar Documentación: ' + data.error);
            }
        })
        .catch(error => {
            mostrarError('Error en la solicitud: ' + error);
        })
        .finally(() => {
            btn.disabled = false;
            icon.classList.remove('fa-spin');
        });
}

/**
 * Actualizar pestaña de Integraciones
 */
function actualizarIntegraciones() {
    const btn = document.getElementById('btnActualizarIntegraciones');
    const icon = btn.querySelector('i');
    
    btn.disabled = true;
    icon.classList.add('fa-spin');
    
    fetch('includes/data-loader-integraciones.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                actualizarTimestamp(data.timestamp);
                actualizarBadgeTab('integraciones-tab', data.totalPendientes);
                location.reload();
            } else {
                mostrarError('Error al actualizar Integraciones: ' + data.error);
            }
        })
        .catch(error => {
            mostrarError('Error en la solicitud: ' + error);
        })
        .finally(() => {
            btn.disabled = false;
            icon.classList.remove('fa-spin');
        });
}

/**
 * Actualizar pestaña de Operaciones Central
 */
function actualizarOperacionesCentral() {
    const btn = document.getElementById('btnActualizarOperacionesCentral');
    const icon = btn.querySelector('i');
    
    btn.disabled = true;
    icon.classList.add('fa-spin');
    
    fetch('includes/data-loader-operaciones-central.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                actualizarTimestamp(data.timestamp);
                actualizarBadgeTab('operaciones-central-tab', data.totalPendientes);
                location.reload();
            } else {
                mostrarError('Error al actualizar Operaciones Central: ' + data.error);
            }
        })
        .catch(error => {
            mostrarError('Error en la solicitud: ' + error);
        })
        .finally(() => {
            btn.disabled = false;
            icon.classList.remove('fa-spin');
        });
}

/**
 * Actualizar pestaña de Operaciones Sucursales
 */
function actualizarOperacionesSucursales() {
    const btn = document.getElementById('btnActualizarOperacionesSucursales');
    const icon = btn.querySelector('i');
    
    btn.disabled = true;
    icon.classList.add('fa-spin');
    
    fetch('includes/data-loader-operaciones-sucursales.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                actualizarTimestamp(data.timestamp);
                actualizarBadgeTab('operaciones-sucursales-tab', data.totalPendientes);
                location.reload();
            } else {
                mostrarError('Error al actualizar Operaciones Sucursales: ' + data.error);
            }
        })
        .catch(error => {
            mostrarError('Error en la solicitud: ' + error);
        })
        .finally(() => {
            btn.disabled = false;
            icon.classList.remove('fa-spin');
        });
}

/**
 * Actualizar pestaña de Uruguay
 */
function actualizarUruguay() {
    const btn = document.getElementById('btnActualizarUruguay');
    const icon = btn.querySelector('i');
    
    btn.disabled = true;
    icon.classList.add('fa-spin');
    
    fetch('includes/data-loader-uruguay.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                actualizarTimestamp(data.timestamp);
                actualizarBadgeTab('uruguay-tab', data.totalPendientes);
                location.reload();
            } else {
                mostrarError('Error al actualizar Uruguay: ' + data.error);
            }
        })
        .catch(error => {
            mostrarError('Error en la solicitud: ' + error);
        })
        .finally(() => {
            btn.disabled = false;
            icon.classList.remove('fa-spin');
        });
}

/**
 * Actualizar el timestamp de última actualización
 */
function actualizarTimestamp(timestamp) {
    const timestampElement = document.getElementById('ultimaActualizacion');
    if (timestampElement) {
        timestampElement.innerHTML = '<i class="fas fa-clock"></i> Última actualización: ' + timestamp;
    }
}

/**
 * Actualizar el badge de un tab con el número de pendientes
 */
function actualizarBadgeTab(tabId, totalPendientes) {
    const tab = document.getElementById(tabId);
    if (!tab) return;
    
    // Buscar si ya existe un badge
    let badge = tab.querySelector('.badge.bg-danger');
    
    if (totalPendientes > 0) {
        if (badge) {
            // Actualizar el badge existente
            badge.textContent = totalPendientes;
        } else {
            // Crear un nuevo badge
            badge = document.createElement('span');
            badge.className = 'badge bg-danger';
            badge.textContent = totalPendientes;
            tab.appendChild(badge);
        }
    } else {
        // Si no hay pendientes, eliminar el badge si existe
        if (badge) {
            badge.remove();
        }
    }
}

/**
 * Mostrar mensaje de error
 */
function mostrarError(mensaje) {
    // Crear un alert temporal
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
    alertDiv.style.zIndex = '9999';
    alertDiv.innerHTML = `
        <strong>Error:</strong> ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto-cerrar después de 5 segundos
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

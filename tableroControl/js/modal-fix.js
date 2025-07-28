// js/modal-fix.js
// Script para solucionar problemas con modales de Bootstrap 5

document.addEventListener('DOMContentLoaded', function() {
    
    // Función para inicializar modales de forma segura
    function initializeModals() {
        const modalElements = document.querySelectorAll('.modal');
        
        modalElements.forEach(function(modalElement) {
            try {
                // Verificar si el modal ya tiene una instancia
                let modalInstance = bootstrap.Modal.getInstance(modalElement);
                
                // Si no existe, crear una nueva instancia
                if (!modalInstance) {
                    modalInstance = new bootstrap.Modal(modalElement, {
                        backdrop: 'static',
                        keyboard: false,
                        focus: true
                    });
                }
            } catch (error) {
                console.warn('Error al inicializar modal:', modalElement.id, error);
            }
        });
    }
    
    // Función para manejar la apertura de modales de forma segura
    function safeModalShow(modalId) {
        // Esperar 300ms para asegurar que el modal esté en el DOM
        setTimeout(() => {
            const modalElement = document.getElementById(modalId);
            if (!modalElement) {
                console.error("Modal no encontrado:", modalId);
                return;
            }

            try {
                const modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
                modalInstance.show();
            } catch (error) {
                console.error("Error al mostrar el modal:", modalId, error);
            }
        }, 300); // Podés ajustar el tiempo si es necesario
    }
    
    // Función para cerrar modales de forma segura
    function safeModalHide(modalId) {
        try {
            const modalElement = document.getElementById(modalId);
            if (!modalElement) {
                return false;
            }
            
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (modalInstance) {
                modalInstance.hide();
            }
            return true;
            
        } catch (error) {
            console.error('Error al cerrar modal:', modalId, error);
            return false;
        }
    }
    
    // Inicializar modales al cargar la página
    initializeModals();
    
    // Reinitializar modales después de cambios en el DOM (por ejemplo, al cambiar de pestaña)
    const tabElements = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabElements.forEach(function(tabElement) {
        tabElement.addEventListener('shown.bs.tab', function() {
            setTimeout(function() {
                initializeModals();
            }, 100);
        });
    });
    
    // Manejar eventos de botones que abren modales
    document.addEventListener('click', function(event) {
        const target = event.target.closest('[data-bs-toggle="modal"]');
        if (target) {
            event.preventDefault();
            const modalId = target.getAttribute('data-bs-target');
            if (modalId) {
                const modalElementId = modalId.replace('#', '');
                safeModalShow(modalElementId);
            }
        }
    });
    
    // Limpiar instancias de modales al cerrar
    document.addEventListener('hidden.bs.modal', function(event) {
        try {
            // Remover backdrop manualmente si queda colgado
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(function(backdrop) {
                if (backdrop.parentNode) {
                    backdrop.parentNode.removeChild(backdrop);
                }
            });
            
            // Restaurar scroll del body
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('padding-right');
            document.body.style.removeProperty('overflow');
            
        } catch (error) {
            console.warn('Error al limpiar modal:', error);
        }
    });
    
    // Exponer funciones globalmente para uso manual si es necesario
    window.safeModalShow = safeModalShow;
    window.safeModalHide = safeModalHide;
    window.initializeModals = initializeModals;
    
    // Debug: Logging para ayudar a identificar problemas
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        console.log('Modal fix script loaded. Modales inicializados:', document.querySelectorAll('.modal').length);
    }
});

// Función adicional para reinicializar modales manualmente
function reinitializeModals() {
    if (typeof window.initializeModals === 'function') {
        window.initializeModals();
    }
}

// Manejar errores globales relacionados con modales
window.addEventListener('error', function(event) {
    if (event.error && event.error.message && event.error.message.includes('backdrop')) {
        console.warn('Error de backdrop detectado, reinicializando modales...');
        setTimeout(function() {
            reinitializeModals();
        }, 500);
    }
});

/**
 * Main JavaScript file for Order Management System
 * Modern ES6+ implementation with improved performance
 */

// DOM elements cache
const elements = {
    table: null,
    tbody: null,
    countInput: null,
    articlesInput: null,
    searchInput: null,
    loadingBox: null
};

// State management
const state = {
    originalRows: [],
    filteredRows: [],
    currentFilter: 'all'
};

/**
 * Initialize the application when DOM is loaded
 */
document.addEventListener('DOMContentLoaded', function() {
    initializeElements();
    initializeTooltips();
    cacheTableRows();
    updateCounters();
    setupEventListeners();
});

/**
 * Cache DOM elements for better performance
 */
function initializeElements() {
    elements.table = document.getElementById('id_tabla');
    elements.tbody = document.getElementById('table');
    elements.countInput = document.getElementById('cantidad');
    elements.articlesInput = document.getElementById('cantidadArticulos');
    elements.searchInput = document.getElementById('textBox');
    elements.loadingBox = document.getElementById('boxLoading');
}

/**
 * Initialize Bootstrap tooltips
 */
function initializeTooltips() {
    if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"], [title]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                trigger: 'hover'
            });
        });
    }
}

/**
 * Cache table rows for filtering operations
 */
function cacheTableRows() {
    if (elements.tbody) {
        state.originalRows = Array.from(elements.tbody.querySelectorAll('tr.data-row'));
        state.filteredRows = [...state.originalRows];
    }
}

/**
 * Setup event listeners
 */
function setupEventListeners() {
    // Search input with debounce
    if (elements.searchInput) {
        let searchTimeout;
        elements.searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                busquedaRapida();
            }, 300);
        });

        // Enter key support
        elements.searchInput.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                busquedaRapida();
            }
        });
    }

    // Form submission with loading indicator
    const searchForm = document.querySelector('.filters-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function() {
            showLoading(true);
        });
    }
}

/**
 * Show/hide loading indicator
 */
function showLoading(show) {
    if (elements.loadingBox) {
        elements.loadingBox.classList.toggle('d-none', !show);
    }
}

/**
 * Update order and article counters
 */
function updateCounters() {
    if (!elements.tbody || !elements.countInput || !elements.articlesInput) return;

    const visibleRows = elements.tbody.querySelectorAll('tr.data-row:not([style*="display: none"])');
    const uniqueOrders = new Set();
    let totalArticles = 0;

    visibleRows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length >= 9) {
            // Get order number (column 4, index 4)
            const orderNumber = cells[4]?.textContent?.trim();
            if (orderNumber) {
                uniqueOrders.add(orderNumber);
            }

            // Get article code and quantity
            const articleCode = cells[6]?.textContent?.trim();
            const quantity = parseFloat(cells[8]?.textContent?.trim()) || 0;

            // Exclude shipping costs from article count
            if (articleCode !== '***COSTO ENVIO') {
                totalArticles += quantity;
            }
        }
    });

    elements.countInput.value = uniqueOrders.size.toLocaleString('es-ES');
    elements.articlesInput.value = totalArticles.toLocaleString('es-ES');
}

/**
 * Enhanced search functionality with multiple criteria
 */
function busquedaRapida() {
    if (!elements.searchInput || !elements.tbody) return;

    const searchTerm = elements.searchInput.value.toLowerCase().trim();
    
    if (searchTerm === '') {
        // Show all rows
        state.originalRows.forEach(row => {
            row.style.display = '';
        });
    } else {
        // Filter rows based on search term
        state.originalRows.forEach(row => {
            const cells = row.querySelectorAll('td');
            let found = false;

            // Search in visible text content of each cell
            for (let i = 0; i < cells.length; i++) {
                const cell = cells[i];
                if (cell.classList.contains('noExl')) continue; // Skip icon columns
                
                const cellText = cell.textContent.toLowerCase();
                if (cellText.includes(searchTerm)) {
                    found = true;
                    break;
                }
            }

            row.style.display = found ? '' : 'none';
        });
    }

    updateCounters();
}

/**
 * Filter pending orders with toggle functionality
 */
window.filterPendientes = function() {
    const button = document.getElementById('buttonPendientes');
    const table = document.getElementById("table");
    
    if (!button || !table) {
        console.error('Button or table not found');
        return;
    }
    
    const tr = table.getElementsByTagName('tr');
    
    // Check current state by text content (more reliable)
    const isActive = button.textContent.trim() === 'Pendientes';
    
    console.log('Button state:', isActive ? 'inactive' : 'active', 'Text:', button.textContent);
    
    for (let i = 0; i < tr.length; i++) {
        let visible = false;
        const td = tr[i].querySelectorAll("td#cancelado");

        if (isActive) {
            // Filter mode - show only pending (items without invoice and not cancelled)
            for (let j = 0; j < td.length; j++) {
                if (td[0] && td[0].querySelector(".pendiente")) {
                    visible = true;
                    break;
                }
            }
        } else {
            // Show all mode
            visible = true;
        }

        tr[i].style.display = visible ? "" : "none";
    }

    // Toggle button appearance and text
    if (isActive) {
        // Activating filter
        button.style.color = '#f7f7f7';
        button.style.backgroundColor = '#ffc107';
        button.style.borderColor = '#ffc107';
        button.textContent = "Todos";
    } else {
        // Deactivating filter
        button.style.color = '#ffc107';
        button.style.backgroundColor = '#f7f7f7';
        button.style.borderColor = '#ffc107';
        button.textContent = "Pendientes";
    }
    
    updateCounters();
}

/**
 * Filter cancelled orders without credit note (NC) with toggle functionality
 */
window.filterCancelados = function() {
    const button = document.getElementById('buttonCancelados');
    const table = document.getElementById("table");
    
    if (!button || !table) {
        console.error('Button or table not found');
        return;
    }
    
    const tr = table.getElementsByTagName('tr');
    
    // Check current state by text content
    const isActive = button.textContent.trim() === 'Sin NC';
    
    console.log('Cancelados button state:', isActive ? 'inactive' : 'active', 'Text:', button.textContent);
    
    for (let i = 0; i < tr.length; i++) {
        let visible = false;
        const td = tr[i].querySelectorAll("td#cancelado");

        if (isActive) {
            // Filter mode - show only cancelled without NC
            for (let j = 0; j < td.length; j++) {
                if (td[0] && td[0].querySelector(".cancelado")) {
                    visible = true;
                    break;
                }
            }
        } else {
            // Show all mode
            visible = true;
        }

        tr[i].style.display = visible ? "" : "none";
    }

    // Toggle button appearance and text
    if (isActive) {
        // Activating filter
        button.style.color = '#f7f7f7';
        button.style.backgroundColor = '#007bff';
        button.style.borderColor = '#007bff';
        button.textContent = "Todos";
    } else {
        // Deactivating filter
        button.style.color = '#007bff';
        button.style.backgroundColor = '#f7f7f7';
        button.style.borderColor = '#007bff';
        button.textContent = "Sin NC";
    }
    
    updateCounters();
}

/**
 * Filter incomplete orders with toggle functionality
 */
window.filterIncompletos = function() {
    const button = document.getElementById('buttonIncompletos');
    const table = document.getElementById("table");
    
    if (!button || !table) {
        console.error('Button or table not found');
        return;
    }
    
    const tr = table.getElementsByTagName('tr');
    
    // Check current state by text content
    const isActive = button.textContent.trim() === 'Incompletos';
    
    console.log('Incompletos button state:', isActive ? 'inactive' : 'active', 'Text:', button.textContent);
    
    for (let i = 0; i < tr.length; i++) {
        let visible = false;
        const td = tr[i].querySelectorAll("td#incompleto");

        if (isActive) {
            // Filter mode - show only incomplete
            for (let j = 0; j < td.length; j++) {
                if (td[0] && td[0].querySelector(".incompleto")) {
                    visible = true;
                    break;
                }
            }
        } else {
            // Show all mode
            visible = true;
        }

        tr[i].style.display = visible ? "" : "none";
    }

    // Toggle button appearance and text
    if (isActive) {
        // Activating filter
        button.style.color = '#f7f7f7';
        button.style.backgroundColor = '#dc3545';
        button.style.borderColor = '#dc3545';
        button.textContent = "Todos";
    } else {
        // Deactivating filter
        button.style.color = '#dc3545';
        button.style.backgroundColor = '#f7f7f7';
        button.style.borderColor = '#dc3545';
        button.textContent = "Incompletos";
    }
    
    updateCounters();
}

/**
 * Update order and article counters (keeping the original contar function name for compatibility)
 */
window.contar = function() {
    updateCounters();
}

// Main counter function
const updateCounters = () => {
    const countInput = document.getElementById('cantidad');
    const articlesInput = document.getElementById('cantidadArticulos');
    const tbody = document.getElementById('table');
    
    if (!tbody || !countInput || !articlesInput) return;

    const visibleRows = tbody.querySelectorAll('tr.data-row:not([style*="display: none"])');
    const uniqueOrders = new Set();
    let totalArticles = 0;

    visibleRows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length >= 9) {
            // Get order number (column 4, index 4)
            const orderNumber = cells[4]?.textContent?.trim();
            if (orderNumber) {
                uniqueOrders.add(orderNumber);
            }

            // Get article code and quantity
            const articleCode = cells[6]?.textContent?.trim();
            const quantity = parseFloat(cells[8]?.textContent?.trim()) || 0;

            // Exclude shipping costs from article count
            if (articleCode !== '***COSTO ENVIO') {
                totalArticles += quantity;
            }
        }
    });

    countInput.value = uniqueOrders.size.toLocaleString('es-ES');
    articlesInput.value = totalArticles.toLocaleString('es-ES');
}

/**
 * Export table data to Excel
 */
function exportar() {
    if (!elements.table) {
        showNotification('Error: No se encontró la tabla para exportar', 'error');
        return;
    }

    try {
        showLoading(true);
        
        // Create a copy of the table for export
        const tableClone = elements.table.cloneNode(true);
        
        // Remove elements with noExl class (icons)
        tableClone.querySelectorAll('.noExl').forEach(el => el.remove());
        
        // Remove hidden rows
        tableClone.querySelectorAll('tr[style*="display: none"]').forEach(el => el.remove());
        
        // Clean up the data for export
        tableClone.querySelectorAll('td, th').forEach(cell => {
            // Remove HTML tags and keep only text
            cell.innerHTML = cell.textContent || cell.innerText || '';
        });

        // Generate filename with current date
        const now = new Date();
        const dateStr = now.toISOString().slice(0, 10);
        const filename = `pedidos_ecommerce_${dateStr}`;

        // Use Table2Excel if available, otherwise fallback to CSV
        if (typeof Table2Excel !== 'undefined') {
            Table2Excel.export(tableClone, {
                filename: filename,
                sheet: {
                    name: 'Pedidos'
                }
            });
        } else {
            exportToCSV(tableClone, filename);
        }

        showNotification('Exportación completada exitosamente', 'success');
        
    } catch (error) {
        console.error('Error durante la exportación:', error);
        showNotification('Error durante la exportación: ' + error.message, 'error');
    } finally {
        showLoading(false);
    }
}

/**
 * Fallback CSV export function
 */
function exportToCSV(table, filename) {
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td, th');
        const rowData = Array.from(cells).map(cell => {
            let text = cell.textContent || cell.innerText || '';
            // Escape quotes and wrap in quotes if contains comma
            text = text.replace(/"/g, '""');
            return text.includes(',') ? `"${text}"` : text;
        });
        csv.push(rowData.join(','));
    });

    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    
    // Create download link
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', filename + '.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

/**
 * Show help modal
 */
function showHelp() {
    const helpModal = document.getElementById('modalAyuda');
    if (helpModal && typeof bootstrap !== 'undefined') {
        const modal = new bootstrap.Modal(helpModal);
        modal.show();
    } else {
        // Fallback if modal is not available
        alert('Ayuda: Use los filtros para buscar pedidos específicos. El campo de búsqueda permite filtrar por cualquier texto visible en la tabla.');
    }
}

/**
 * Show notification to user
 */
function showNotification(message, type = 'info') {
    // Try to use SweetAlert if available
    if (typeof swal !== 'undefined') {
        const icon = type === 'error' ? 'error' : type === 'success' ? 'success' : 'info';
        swal({
            text: message,
            icon: icon,
            timer: 3000,
            buttons: false
        });
    } else {
        // Fallback to browser alert
        alert(message);
    }
}

/**
 * Handle keyboard shortcuts
 */
document.addEventListener('keydown', function(event) {
    // Ctrl+F to focus search
    if (event.ctrlKey && event.key === 'f') {
        event.preventDefault();
        if (elements.searchInput) {
            elements.searchInput.focus();
        }
    }
    
    // Escape to clear search
    if (event.key === 'Escape') {
        if (elements.searchInput && elements.searchInput === document.activeElement) {
            elements.searchInput.value = '';
            busquedaRapida();
        }
    }
});

/**
 * Handle window resize for responsive behavior
 */
let resizeTimeout;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
        updateCounters();
    }, 250);
});

/**
 * Legacy function support for backwards compatibility
 */
function pulsar(event) {
    return event.key === 'Enter' ? false : true;
}

// export-functions.js

function exportarRemitosSinIntegrar() {
    exportTableToExcel(
        '#tablaRemitosSinIntegrar',
        'Remitos Sin Integrar',
        'Remitos_Sin_Integrar.xlsx'
    );
}

/**
 * Función base para exportar cualquier tabla a Excel
 * @param {string} tableSelector - Selector CSS para la tabla
 * @param {string} sheetName - Nombre de la hoja en Excel
 * @param {string} fileName - Nombre del archivo a generar
 * @param {boolean} skipLastColumn - Si debe omitir la última columna (ej. para íconos)
 * @param {Object} options - Opciones adicionales
 * @param {Array} options.textColumns - Índices de columnas que deben mantenerse como texto (0-based)
 */
function exportTableToExcel(tableSelector, sheetName, fileName, skipLastColumn = false, options = {}) {
    const table = document.querySelector(tableSelector);
    if (!table) return;
    
    const tableClone = table.cloneNode(true);
    const rows = tableClone.querySelectorAll('tr');
    const wb = XLSX.utils.book_new();
    const data = [];
    
    // Determinar qué columnas deben ser tratadas como texto
    const textColumns = options.textColumns || [];
    
    rows.forEach((row, rowIndex) => {
        const rowData = [];
        // Seleccionar las celdas, omitiendo la última columna si es necesario
        let selector = skipLastColumn ? 'th:not(:last-child), td:not(:last-child)' : 'th, td';
        row.querySelectorAll(selector).forEach((cell, colIndex) => {
            let value = cell.textContent.trim();
            
            // Si esta columna debe mantenerse como texto, simplemente almacenar el valor
            if (textColumns.includes(colIndex)) {
                rowData.push(value);
                return;
            }
            
            // Convertir fechas (dd/mm/yyyy)
            if (value.match(/^\d{2}\/\d{2}\/\d{4}/)) {
                const [datePart, timePart] = value.split(' ');
                const [day, month, year] = datePart.split('/');
                const dateStr = `${year}-${month}-${day}`;
                value = timePart ? `${dateStr} ${timePart}` : dateStr;
            }
            // Convertir valores monetarios
            else if (value.startsWith('$')) {
                value = parseFloat(value.replace('$', '').replace(/,/g, ''));
            }
            // Convertir valores numéricos con comas
            else if (value.match(/^[\d,.]+$/)) {
                value = parseFloat(value.replace(/,/g, ''));
            }
            // Convertir porcentajes
            else if (value.includes('%')) {
                value = parseFloat(value.replace('%', ''));
            }
            
            rowData.push(value);
        });
        data.push(rowData);
    });
    
    const ws = XLSX.utils.aoa_to_sheet(data);
    
    // Configurar formato de columnas de texto
    if (textColumns.length > 0) {
        if (!ws['!cols']) ws['!cols'] = [];
        textColumns.forEach(colIndex => {
            // Convertir el índice de columna al formato de letras de Excel (A, B, C, ...)
            const colLetter = String.fromCharCode(65 + colIndex);
            
            // Establecer el formato de celda como texto para toda la columna
            for (let i = 0; i < data.length; i++) {
                const cellRef = `${colLetter}${i+1}`;
                if (!ws[cellRef]) continue;
                
                if (!ws[cellRef].t) ws[cellRef].t = 's'; // Establecer el tipo de celda como texto (string)
            }
        });
    }
    
    XLSX.utils.book_append_sheet(wb, ws, sheetName);
    
    // Generar nombre de archivo con fecha actual
    const today = new Date().toISOString().slice(0,10);
    XLSX.writeFile(wb, `${fileName}_${today}.xlsx`);
}

// Funciones específicas para cada tipo de exportación
function exportToExcel() {
    // En la tabla de Pedidos Flex, la columna "Order ID" es la columna 3 (índice 3, 0-based)
    exportTableToExcel('#modalFlexDetalle table', "Pedidos Flex", "pedidos_flex", false, {
        textColumns: [3] // El índice 3 corresponde a la columna "Order ID"
    });
}

function exportToExcelPendientesPreparar() {
    // En la tabla de Pedidos Pendientes de Preparar, la columna "Order ID" es la columna 4 (índice 4, 0-based)
    exportTableToExcel('#modalPendientesPreparar table', "Pedidos Pendientes Preparar", "pedidos_pendientes_preparar", false, {
        textColumns: [4] // El índice 4 corresponde a la columna "Order ID"
    });
}

function exportToExcelFacturas() {
    exportTableToExcel('#modalFacturasDetalle table', "Facturas sin Remito", "facturas_sin_remito", true);
}

function exportToExcelNcDevoluciones() {
    // En la tabla de NC Devoluciones, las columnas "Nro. Pedido" (índice 1) y "Order ID" (índice 2) deben mantenerse como texto
    exportTableToExcel('#modalNcDevolucionesDetalle table', "NC Pendientes Devoluciones", "nc_pendientes_devoluciones", false, {
        textColumns: [1, 2] // Índices 1 y 2 corresponden a "Nro. Pedido" y "Order ID"
    });
}

function exportToExcelNcPromociones() {
    exportTableToExcel('#modalNcPromocionesDetalle table', "NC Pendientes Promociones", "nc_pendientes_promociones");
}

function exportToExcelOrdenes() {
    // En la tabla de Órdenes sin Integrar, la columna "Nro. Orden" es la columna 2 (índice 2, 0-based)
    exportTableToExcel('#modalOrdenesSinIntegrar table', "Ordenes sin Integrar", "ordenes_sin_integrar", false, {
        textColumns: [2] // El índice 2 corresponde a la columna "Nro. Orden"
    });
}

function exportToExcelOrdenesCierre() {
    // En la tabla de Órdenes Pendientes de Cierre, la columna "Order ID" es la columna 1 (índice 1, 0-based)
    exportTableToExcel('#modalOrdenesPendientesCierre table', "Ordenes Pendientes Cierre", "ordenes_pendientes_cierre", true, {
        textColumns: [1] // El índice 1 corresponde a la columna "Order ID"
    });
}

function exportToExcelPendingDispatch() {
    // En la tabla de Pedidos Pendientes Despacho, la columna "Order ID" es la columna 5 (índice 5, 0-based)
    exportTableToExcel('#modalPendingDispatch table', "Pedidos Pendientes Despacho", "pedidos_pendientes_despacho", false, {
        textColumns: [5] // El índice 5 corresponde a la columna "Order ID"
    });
}

function exportToExcelPedidosDespachados() {
    // En la tabla de Pedidos Despachados, la columna "Order ID" es la columna 2 (índice 2, 0-based)
    exportTableToExcel('#modalPedidosDespachados table', "Pedidos Despachados", "pedidos_despachados_pendientes", true, {
        textColumns: [2] // El índice 2 corresponde a la columna "Order ID"
    });
}

function exportToExcelPedidosControl() {
    exportTableToExcel('#tablaPedidosControl', "Pedidos Pendientes Control", "pedidos_pendientes_control");
}

function exportToExcelMlFull() {
    // Obtener la tabla
    const table = document.querySelector('#tablaProductosMlFull');
    if (!table) return;
    
    const tableClone = table.cloneNode(true);
    
    // Remover filas de filtro
    const filterRow = tableClone.querySelector('thead tr:first-child');
    if (filterRow) filterRow.remove();
    
    const rows = tableClone.querySelectorAll('tr');
    const wb = XLSX.utils.book_new();
    const data = [];
    
    // Identificar columnas que podrían contener IDs largos o alfanuméricos
    const textColumnIndices = [];
    const headerRow = tableClone.querySelector('thead tr');
    if (headerRow) {
        headerRow.querySelectorAll('th:not(:nth-child(5)):not(:nth-child(6))').forEach((cell, index) => {
            if (cell.textContent.includes('ID')) {
                textColumnIndices.push(index);
            }
        });
    }
    
    rows.forEach((row) => {
        const rowData = [];
        let colIndex = 0;
        
        // Omitir las columnas de enlaces al exportar
        row.querySelectorAll('th:not(:nth-child(5)):not(:nth-child(6)), td:not(:nth-child(5)):not(:nth-child(6))').forEach((cell) => {
            let value = cell.textContent.trim();
            
            // Mantener como texto si es una columna de ID
            if (textColumnIndices.includes(colIndex)) {
                rowData.push(value);
            }
            // Convertir valores numéricos con comas
            else if (value.match(/^[\d,]+$/)) {
                value = parseFloat(value.replace(/,/g, ''));
                rowData.push(value);
            }
            else {
                rowData.push(value);
            }
            
            colIndex++;
        });
        data.push(rowData);
    });
    
    const ws = XLSX.utils.aoa_to_sheet(data);
    
    // Configurar formato de columnas de texto
    if (textColumnIndices.length > 0) {
        if (!ws['!cols']) ws['!cols'] = [];
        textColumnIndices.forEach(colIndex => {
            // Establecer el formato de celda como texto para toda la columna
            for (let i = 0; i < data.length; i++) {
                const cellRef = `${String.fromCharCode(65 + colIndex)}${i+1}`;
                if (!ws[cellRef]) continue;
                if (!ws[cellRef].t) ws[cellRef].t = 's'; // Establecer el tipo como texto
            }
        });
    }
    
    XLSX.utils.book_append_sheet(wb, ws, "Productos ML Full");
    
    // Generar nombre de archivo con fecha actual
    const today = new Date().toISOString().slice(0,10);
    XLSX.writeFile(wb, `productos_ml_full_${today}.xlsx`);
}


function exportToExcelRankingControl() {
    // Función especializada para exportar el ranking de pedidos control
    const table = document.querySelector('#tablaRankingControl');
    if (!table) return;
    
    const tableClone = table.cloneNode(true);
    const rows = tableClone.querySelectorAll('tr');
    const wb = XLSX.utils.book_new();
    const data = [];
    
    rows.forEach((row, rowIndex) => {
        const rowData = [];
        row.querySelectorAll('th, td').forEach((cell, colIndex) => {
            let value = cell.textContent.trim();
            
            // Manejar la columna de posición (puede contener íconos)
            if (colIndex === 0 && rowIndex > 0) {
                // Si contiene íconos, extraer solo el número o convertir íconos a texto
                if (value.includes('👑') || cell.querySelector('.fa-crown')) {
                    value = '1';
                } else if (value.includes('🥈') || cell.querySelector('.fa-medal')) {
                    value = '2';
                } else if (value.includes('🥉') || cell.querySelector('.fa-award')) {
                    value = '3';
                } else {
                    // Extraer solo números
                    value = value.replace(/[^0-9]/g, '') || value;
                }
            }
            
            // Manejar la columna de cantidad (quitar badges)
            if (colIndex === 2 && rowIndex > 0) {
                value = value.replace(/[^0-9,]/g, '');
                if (value.match(/^[\d,]+$/)) {
                    value = parseFloat(value.replace(/,/g, ''));
                }
            }
            
            // Manejar la columna de porcentaje
            if (colIndex === 3 && rowIndex > 0) {
                if (value.includes('%')) {
                    value = parseFloat(value.replace('%', ''));
                }
            }
            
            // Omitir la columna de progreso (índice 4)
            if (colIndex !== 4) {
                rowData.push(value);
            }
        });
        data.push(rowData);
    });
    
    const ws = XLSX.utils.aoa_to_sheet(data);
    
    // Configurar anchos de columna
    ws['!cols'] = [
        { width: 10 },  // Posición
        { width: 25 },  // Sucursal
        { width: 12 },  // Cantidad
        { width: 12 }   // Porcentaje
    ];
    
    XLSX.utils.book_append_sheet(wb, ws, "Ranking Pedidos Control");
    
    // Generar nombre de archivo con fecha actual
    const today = new Date().toISOString().slice(0,10);
    XLSX.writeFile(wb, `ranking_pedidos_control_${today}.xlsx`);
}
// Función para exportar pedidos recibidos no entregados a Excel
function exportToExcelPedidosRecibidosNoEntregados() {
    exportTableToExcel('#modalPedidosRecibidosNoEntregados table', "Pedidos Recibidos No Entregados", "pedidos_recibidos_no_entregados", true, {
        textColumns: [2]
    });
}
// Función para exportar pedidos de retiro en tienda a Excel
function exportToExcelPedidosRetiroTienda() {
    // En la tabla de Pedidos de Retiro en Tienda, la columna "Order ID" es la columna 3 (índice 3, 0-based)
    exportTableToExcel('#modalPedidosRetiroTienda table', "Pedidos Retiro en Tienda", "pedidos_retiro_tienda", true, {
        textColumns: [3] // El índice 3 corresponde a la columna "Order ID"
    });
}

// Funciones para los nuevos modales de control separado
function exportToExcelPedidosControlCentral() {
    exportTableToExcel('#tablaPedidosControlCentral', "Pedidos Pendientes Control Central", "pedidos_pendientes_control_central");
}

function exportToExcelPedidosControlSucursales() {
    exportTableToExcel('#tablaPedidosControlSucursales', "Pedidos Pendientes Control Sucursales", "pedidos_pendientes_control_sucursales");
}

// ============================================
// FUNCIONES DE EXPORTACIÓN PARA URUGUAY
// ============================================

// Función para exportar pedidos sin facturar de Uruguay
function exportToExcelPedidosUruguay() {
    exportTableToExcel('#tablaPedidosUruguay', "Pedidos Uruguay", "pedidos_sin_facturar_uruguay", false, {
        textColumns: [3] // El índice 3 corresponde a la columna "Order ID"
    });
}

// Función para exportar facturas sin remito de Uruguay
function exportToExcelFacturasSinRemitoUruguay() {
    exportTableToExcel('#tablaFacturasSinRemitoUruguay', "Facturas sin Remito Uruguay", "facturas_sin_remito_uruguay", true);
}

// Función para exportar NC devoluciones de Uruguay
function exportToExcelNcDevolucionesUruguay() {
    exportTableToExcel('#tablaNcDevolucionesUruguay', "NC Devoluciones Uruguay", "nc_devoluciones_uruguay", false, {
        textColumns: [2] // El índice 2 corresponde a la columna "Order ID"
    });
}

// Función para exportar órdenes sin integrar de Uruguay
function exportToExcelOrdenesSinIntegrarUruguay() {
    exportTableToExcel('#tablaOrdenesSinIntegrarUruguay', "Ordenes sin Integrar Uruguay", "ordenes_sin_integrar_uruguay", false, {
        textColumns: [2] // El índice 2 corresponde a la columna "Nro. Orden"
    });
}

// Función para exportar órdenes pendientes de cierre de Uruguay
function exportToExcelOrdenesPendientesCierreUruguay() {
    exportTableToExcel('#tableOrdenesPendientesCierreUruguay', "Órdenes Pendientes Cierre UY", "ordenes_pendientes_cierre_uruguay", true, {
        textColumns: [1] // El índice 1 corresponde a la columna "Order ID"
    });
}

// Función para exportar pedidos de retiro en tienda de Uruguay
function exportToExcelPedidosRetiroTiendaUruguay() {
    exportTableToExcel('#tablePedidosRetiroTiendaUruguay', "Pedidos Retiro en Tienda Uruguay", "pedidos_retiro_tienda_uruguay", true, {
        textColumns: [3] // El índice 3 corresponde a la columna "Order ID"
    });
}

// Función para exportar pedidos pendientes de control de sucursales de Uruguay
function exportToExcelPedidosControlSucursalesUruguay() {
    exportTableToExcel('#tablePedidosControlSucursalesUruguay', "Pedidos Pendientes Control Sucursales Uruguay", "pedidos_pendientes_control_sucursales_uruguay", false, {
        textColumns: [3] // El índice 3 corresponde a la columna "Order ID"
    });
}

// Función eliminada - Modal de ranking de Central no es necesario
/* function exportToExcelRankingControlCentral() {
    // Función especializada para exportar el ranking de pedidos control central
    const table = document.querySelector('#tablaRankingControlCentral');
    if (!table) return;
    
    const tableClone = table.cloneNode(true);
    const rows = tableClone.querySelectorAll('tr');
    const wb = XLSX.utils.book_new();
    const data = [];
    
    rows.forEach((row, rowIndex) => {
        const rowData = [];
        row.querySelectorAll('th, td').forEach((cell, colIndex) => {
            let value = cell.textContent.trim();
            
            // Manejar la columna de posición (puede contener íconos)
            if (colIndex === 0 && rowIndex > 0) {
                // Si contiene íconos, extraer solo el número o convertir íconos a texto
                if (value.includes('👑') || cell.querySelector('.fa-crown')) {
                    value = '1';
                } else if (value.includes('🥈') || cell.querySelector('.fa-medal')) {
                    value = '2';
                } else if (value.includes('🥉') || cell.querySelector('.fa-award')) {
                    value = '3';
                } else {
                    // Extraer solo números
                    value = value.replace(/[^0-9]/g, '') || value;
                }
            }
            
            // Manejar la columna de cantidad (quitar badges)
            if (colIndex === 2 && rowIndex > 0) {
                value = value.replace(/[^0-9,]/g, '');
                if (value.match(/^[\d,]+$/)) {
                    value = parseFloat(value.replace(/,/g, ''));
                }
            }
            
            // Manejar la columna de porcentaje
            if (colIndex === 3 && rowIndex > 0) {
                if (value.includes('%')) {
                    value = parseFloat(value.replace('%', ''));
                }
            }
            
            // Omitir la columna de progreso (índice 4)
            if (colIndex !== 4) {
                rowData.push(value);
            }
        });
        data.push(rowData);
    });
    
    const ws = XLSX.utils.aoa_to_sheet(data);
    
    // Configurar anchos de columna
    ws['!cols'] = [
        { width: 10 },  // Posición
        { width: 25 },  // Departamento/Sucursal
        { width: 12 },  // Cantidad
        { width: 12 }   // Porcentaje
    ];
    
    XLSX.utils.book_append_sheet(wb, ws, "Ranking Control Central");
    
    // Generar nombre de archivo con fecha actual
    const today = new Date().toISOString().slice(0,10);
    XLSX.writeFile(wb, `ranking_control_central_${today}.xlsx`);
} */

function exportToExcelRankingControlSucursales() {
    // Función especializada para exportar el ranking de pedidos control sucursales
    const table = document.querySelector('#tablaRankingControlSucursales');
    if (!table) return;
    
    const tableClone = table.cloneNode(true);
    const rows = tableClone.querySelectorAll('tr');
    const wb = XLSX.utils.book_new();
    const data = [];
    
    rows.forEach((row, rowIndex) => {
        const rowData = [];
        row.querySelectorAll('th, td').forEach((cell, colIndex) => {
            let value = cell.textContent.trim();
            
            // Manejar la columna de posición (puede contener íconos)
            if (colIndex === 0 && rowIndex > 0) {
                // Si contiene íconos, extraer solo el número o convertir íconos a texto
                if (value.includes('👑') || cell.querySelector('.fa-crown')) {
                    value = '1';
                } else if (value.includes('🥈') || cell.querySelector('.fa-medal')) {
                    value = '2';
                } else if (value.includes('🥉') || cell.querySelector('.fa-award')) {
                    value = '3';
                } else {
                    // Extraer solo números
                    value = value.replace(/[^0-9]/g, '') || value;
                }
            }
            
            // Manejar la columna de cantidad (quitar badges)
            if (colIndex === 2 && rowIndex > 0) {
                value = value.replace(/[^0-9,]/g, '');
                if (value.match(/^[\d,]+$/)) {
                    value = parseFloat(value.replace(/,/g, ''));
                }
            }
            
            // Manejar la columna de porcentaje
            if (colIndex === 3 && rowIndex > 0) {
                if (value.includes('%')) {
                    value = parseFloat(value.replace('%', ''));
                }
            }
            
            // Omitir la columna de progreso (índice 4)
            if (colIndex !== 4) {
                rowData.push(value);
            }
        });
        data.push(rowData);
    });
    
    const ws = XLSX.utils.aoa_to_sheet(data);
    
    // Configurar anchos de columna
    ws['!cols'] = [
        { width: 10 },  // Posición
        { width: 25 },  // Sucursal
        { width: 12 },  // Cantidad
        { width: 12 }   // Porcentaje
    ];
    
    XLSX.utils.book_append_sheet(wb, ws, "Ranking Control Sucursales");
    
    // Generar nombre de archivo con fecha actual
    const today = new Date().toISOString().slice(0,10);
    XLSX.writeFile(wb, `ranking_control_sucursales_${today}.xlsx`);
}

// ============================================
// FUNCIONES DE EXPORTACIÓN PARA PEDIDOS INCOMPLETOS
// ============================================

// Función para exportar pedidos incompletos de Central
function exportToExcelPedidosIncompletosCentral() {
    exportTableToExcel('#modalPedidosIncompletosCentral table', "Pedidos Incompletos Central", "pedidos_incompletos_central", false, {
        textColumns: [1, 2] // NRO_ORDEN_ECOMMERCE (índice 1) y NRO_PEDIDO (índice 2) como texto
    });
}

// Función para exportar pedidos incompletos de Sucursales
function exportToExcelPedidosIncompletosSucursales() {
    exportTableToExcel('#modalPedidosIncompletosSucursales table', "Pedidos Incompletos Sucursales", "pedidos_incompletos_sucursales", false, {
        textColumns: [1, 2] // NRO_ORDEN_ECOMMERCE (índice 1) y NRO_PEDIDO (índice 2) como texto
    });
}
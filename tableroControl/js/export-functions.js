
// export-functions.js

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

function exportToExcelFacturas() {
    exportTableToExcel('#modalFacturasDetalle table', "Facturas sin Remito", "facturas_sin_remito", true);
}

function exportToExcelNcDevoluciones() {
    exportTableToExcel('#modalNcDevolucionesDetalle table', "NC Pendientes Devoluciones", "nc_pendientes_devoluciones");
}

function exportToExcelNcPromociones() {
    exportTableToExcel('#modalNcPromocionesDetalle table', "NC Pendientes Promociones", "nc_pendientes_promociones");
}

function exportToExcelOrdenes() {
    // En caso de que la tabla tenga una columna de ID similar
    exportTableToExcel('#modalOrdenesSinIntegrar table', "Ordenes sin Integrar", "ordenes_sin_integrar", false, {
        textColumns: [3] // Ajusta este índice según la posición de la columna Order ID
    });
}

function exportToExcelOrdenesCierre() {
    // En caso de que la tabla tenga una columna de ID similar
    exportTableToExcel('#modalOrdenesPendientesCierre table', "Ordenes Pendientes Cierre", "ordenes_pendientes_cierre", true, {
        textColumns: [3] // Ajusta este índice según la posición de la columna Order ID
    });
}

function exportToExcelPendingDispatch() {
    // En la tabla de Pedidos Pendientes Despacho, suponiendo que la columna "Order ID" también es la columna 3
    exportTableToExcel('#modalPendingDispatch table', "Pedidos Pendientes Despacho", "pedidos_pendientes_despacho", false, {
        textColumns: [3] // Ajusta este índice según la posición de la columna Order ID
    });
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

class RemitoManager {
    constructor() {
        this.datosCompletos = [];
        this.dataTable = null;
        this.init();
    }

    init() {
        console.log('Inicializando RemitoManager con DataTables...');
        
        // Verificar que jQuery esté disponible
        if (typeof $ === 'undefined') {
            console.error('jQuery no está disponible');
            setTimeout(() => this.init(), 100);
            return;
        }
        
        // Verificar que los elementos existan
        const fechaDesde = document.getElementById('fechaDesde');
        const fechaHasta = document.getElementById('fechaHasta');
        const estado = document.getElementById('estado');
        const tabla = document.getElementById('tablaRemitos');
        
        if (!fechaDesde || !fechaHasta || !estado || !tabla) {
            console.error('Elementos del DOM no encontrados');
            setTimeout(() => this.init(), 100);
            return;
        }
        
        this.bindEvents();
        this.inicializarDataTable();
        
        // Cargar datos iniciales
        setTimeout(() => {
            console.log('Iniciando carga de datos...');
            this.cargarRemitos();
        }, 200);
    }

    bindEvents() {
        // Botón filtrar
        document.getElementById('btnFiltrar').addEventListener('click', () => {
            this.cargarRemitos();
        });

        // Botón limpiar filtros
        document.getElementById('btnLimpiar').addEventListener('click', () => {
            this.limpiarFiltros();
        });

        // Botón importar remitos
        document.getElementById('btnImportar').addEventListener('click', () => {
            this.importarRemitos();
        });

        // Filtrar automáticamente cuando cambian los filtros
        document.getElementById('fechaDesde').addEventListener('change', () => {
            this.cargarRemitos();
        });

        document.getElementById('fechaHasta').addEventListener('change', () => {
            this.cargarRemitos();
        });

        document.getElementById('estado').addEventListener('change', () => {
            this.cargarRemitos();
        });

        // Nuevo evento para el botón de actualizar remito
        document.getElementById('btnActualizarRemito').addEventListener('click', () => {
            this.abrirModalActualizarRemito();
        });

        // Eventos del modal de actualizar remito
        document.getElementById('btnVerificarRemito').addEventListener('click', () => {
            this.verificarRemito();
        });

        document.getElementById('btnEjecutarActualizacion').addEventListener('click', () => {
            this.ejecutarActualizacionRemito();
        });

        // Evento para el input de número de remito (Enter para verificar)
        document.getElementById('inputNRemito').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.verificarRemito();
            }
        });

        // Limpiar modal cuando se cierra
        document.getElementById('modalActualizarRemito').addEventListener('hidden.bs.modal', () => {
            this.limpiarModalActualizarRemito();
        });

        // Alta de Partidas
        document.getElementById('btnAltaPartida').addEventListener('click', () => {
            this.abrirModalAltaPartida();
        });

        document.getElementById('btnVerificarPartida').addEventListener('click', () => {
            this.verificarPartida();
        });

        document.getElementById('btnEjecutarAltaPartida').addEventListener('click', () => {
            this.ejecutarAltaPartida();
        });

        // Al cambiar artículo, depósito o partida, invalidar la verificación
        $('#inputCodArticu, #inputCodDepo, #inputNPartida').on('change', () => {
            document.getElementById('btnEjecutarAltaPartida').disabled = true;
            document.getElementById('infoPartida').classList.add('d-none');
            document.getElementById('alertaPartida').classList.add('d-none');
        });

        // Al cambiar artículo o depósito, recargar las partidas asociadas a esa combinación
        $('#inputCodArticu, #inputCodDepo').on('change', () => {
            this.cargarPartidasNPartida();
        });

        // Inicializar Select2 la primera vez que se abre el modal
        document.getElementById('modalAltaPartida').addEventListener('shown.bs.modal', () => {
            if (!this.select2AltaPartidaInit) {
                this.inicializarSelect2AltaPartida();
                this.select2AltaPartidaInit = true;
            }
        });

        document.getElementById('modalAltaPartida').addEventListener('hidden.bs.modal', () => {
            this.limpiarModalAltaPartida();
        });
    }

    inicializarDataTable() {
        // Configuración de DataTables
        this.dataTable = $('#tablaRemitos').DataTable({
            // Configuración básica
            processing: true,
            serverSide: false, // Usaremos datos del cliente
            responsive: true,
            pageLength: 50,
            lengthMenu: [[25, 50, 100, 200, -1], [25, 50, 100, 200, "Todos"]],
            
            // Configuración de idioma
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            },
            
            // Configuración de columnas
            columnDefs: [
                {
                    targets: [0], // Fecha
                    type: 'date',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return window.remitoManager.formatearFecha(data);
                        }
                        return data;
                    }
                },
                {
                    targets: [1], // Hora
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return window.remitoManager.formatearHora(data);
                        }
                        return data;
                    }
                },
                {
                    targets: [2], // Proveedor
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return `<span class="proveedor-${data.toLowerCase()}">${data}</span>`;
                        }
                        return data;
                    }
                },
                {
                    targets: [3], // N° Comprobante
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return `<strong>${data}</strong>`;
                        }
                        return data;
                    }
                },
                {
                    targets: [4], // Cantidad
                    className: 'text-end',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return window.remitoManager.formatearNumero(data);
                        }
                        return data;
                    }
                },
                {
                    targets: [5], // Estado
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return `<span class="estado-${data.toLowerCase().replace(/ /g, '-')}">${data}</span>`;
                        }
                        return data;
                    }
                }
            ],
            
            // Configuración de DOM y estilo
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            
            // Configuración de búsqueda
            search: {
                placeholder: "Buscar remitos..."
            },
            
            // Eventos
            drawCallback: function(settings) {
                // Se ejecuta después de cada redibujado
                window.remitoManager.actualizarContadores();
            }
        });
    }

    async cargarRemitos() {
        try {
            const fechaDesde = document.getElementById('fechaDesde').value;
            const fechaHasta = document.getElementById('fechaHasta').value;
            const estado = document.getElementById('estado').value;

            console.log('Enviando datos:', { fechaDesde, fechaHasta, estado });

            const formData = new FormData();
            formData.append('action', 'obtenerRemitos');
            formData.append('fechaDesde', fechaDesde);
            formData.append('fechaHasta', fechaHasta);
            formData.append('estado', estado);

            const response = await fetch('/ecommerce/Abastecimiento/Controller/importarRemitos.php', {
                method: 'POST',
                body: formData
            });

            console.log('Response status:', response.status);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const responseText = await response.text();
            console.log('Response raw:', responseText);

            let data;
            try {
                data = JSON.parse(responseText);
            } catch (parseError) {
                console.error('JSON Parse Error:', parseError);
                throw new Error('Respuesta del servidor no es JSON válido: ' + responseText.substring(0, 100));
            }

            console.log('Response parsed:', data);

            if (data.success) {
                this.datosCompletos = data.data;
                this.actualizarDataTable(data.data);
                this.actualizarContadores();
                console.log('Datos cargados correctamente:', data.data.length, 'registros');
            } else {
                console.error('Error del servidor:', data.message);
                this.mostrarToast('Error al cargar los datos: ' + (data.message || 'Error desconocido'), 'error');
            }

        } catch (error) {
            console.error('Error completo:', error);
            this.mostrarToast('Error de conexión: ' + error.message, 'error');
        }
    }

    actualizarDataTable(datos) {
        // Limpiar y agregar nuevos datos
        this.dataTable.clear();
        
        if (datos && datos.length > 0) {
            // Agregar datos fila por fila
            datos.forEach(remito => {
                this.dataTable.row.add([
                    remito.FECHA_MOV,
                    remito.HORA_INGRESO,
                    remito.COD_PRO_CL,
                    remito.N_COMP,
                    remito.CANTIDAD,
                    remito.ESTADO
                ]);
            });
        }
        
        // Redibujar la tabla
        this.dataTable.draw();
    }

    actualizarContadores() {
        if (!this.datosCompletos || this.datosCompletos.length === 0) {
            document.getElementById('totalRemitos').textContent = '0';
            document.getElementById('sinImportar').textContent = '0';
            document.getElementById('ingresado').textContent = '0';
            document.getElementById('sinIngresar').textContent = '0';
            return;
        }

        const total = this.datosCompletos.length;
        const sinImportar = this.datosCompletos.filter(r => r.ESTADO === 'SIN IMPORTAR').length;
        const ingresado = this.datosCompletos.filter(r => r.ESTADO === 'INGRESADO').length;
        const sinIngresar = this.datosCompletos.filter(r => r.ESTADO === 'SIN INGRESAR').length;

        document.getElementById('totalRemitos').textContent = total;
        document.getElementById('sinImportar').textContent = sinImportar;
        document.getElementById('ingresado').textContent = ingresado;
        document.getElementById('sinIngresar').textContent = sinIngresar;
    }

    limpiarFiltros() {
        document.getElementById('fechaDesde').value = '';
        document.getElementById('fechaHasta').value = '';
        document.getElementById('estado').value = 'TODOS';
        this.cargarRemitos();
    }

    // Función para ampliar la búsqueda
    ampliarBusqueda() {
        const hoy = new Date();
        const haceUnAno = new Date();
        haceUnAno.setFullYear(hoy.getFullYear() - 1);

        const formatoInput = (fecha) => {
            return fecha.toISOString().split('T')[0];
        };

        document.getElementById('fechaDesde').value = formatoInput(haceUnAno);
        document.getElementById('fechaHasta').value = formatoInput(hoy);
        document.getElementById('estado').value = 'TODOS';

        this.mostrarToast('Búsqueda ampliada al último año', 'info');
        this.cargarRemitos();
    }

    async importarRemitos() {
        const btnImportar = document.getElementById('btnImportar');
        const originalText = btnImportar.innerHTML;

        // Confirmar acción con un modal Bootstrap
        const confirmacion = await this.mostrarConfirmacion(
            'Confirmar Importación',
            '¿Desea proceder con la importación de remitos pendientes? Esta acción procesará todos los remitos que están marcados como EXPORTADO = 0.',
            'Sí, Importar',
            'Cancelar'
        );

        if (!confirmacion) {
            return;
        }

        try {
            // Mostrar loading
            btnImportar.innerHTML = '<span class="loading-spinner"></span> Importando...';
            btnImportar.disabled = true;

            const formData = new FormData();
            formData.append('action', 'importarRemitos');

            const response = await fetch('/ecommerce/Abastecimiento/Controller/importarRemitos.php', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                this.mostrarToast(data.message, 'success');
                // Recargar la tabla después de importar
                setTimeout(() => {
                    this.cargarRemitos();
                }, 1000);
            } else {
                this.mostrarToast(data.message || 'Error al importar', 'error');
            }

        } catch (error) {
            console.error('Error:', error);
            this.mostrarToast('Error al importar remitos: ' + error.message, 'error');
        } finally {
            // Restaurar botón
            btnImportar.innerHTML = originalText;
            btnImportar.disabled = false;
        }
    }

    // Crear modal de confirmación personalizado
    mostrarConfirmacion(titulo, mensaje, textoConfirmar, textoCancelar) {
        return new Promise((resolve) => {
            // Crear modal si no existe
            let modal = document.getElementById('modalConfirmacion');
            if (!modal) {
                const modalHtml = `
                    <div class="modal fade" id="modalConfirmacion" tabindex="-1" aria-labelledby="modalConfirmacionLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalConfirmacionLabel">
                                        <i class="bi bi-question-circle text-warning me-2"></i>
                                        <span id="modalTitulo"></span>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p id="modalMensaje"></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" id="btnCancelar" data-bs-dismiss="modal">
                                        <i class="bi bi-x-circle me-1"></i>
                                        <span id="textoCancelar"></span>
                                    </button>
                                    <button type="button" class="btn btn-primary" id="btnConfirmar">
                                        <i class="bi bi-check-circle me-1"></i>
                                        <span id="textoConfirmar"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                modal = document.getElementById('modalConfirmacion');
            }

            // Actualizar contenido del modal
            document.getElementById('modalTitulo').textContent = titulo;
            document.getElementById('modalMensaje').textContent = mensaje;
            document.getElementById('textoConfirmar').textContent = textoConfirmar;
            document.getElementById('textoCancelar').textContent = textoCancelar;

            // Manejar eventos
            const btnConfirmar = document.getElementById('btnConfirmar');
            const btnCancelar = document.getElementById('btnCancelar');

            const handleConfirmar = () => {
                resolve(true);
                bootstrap.Modal.getInstance(modal).hide();
                cleanup();
            };

            const handleCancelar = () => {
                resolve(false);
                cleanup();
            };

            const cleanup = () => {
                btnConfirmar.removeEventListener('click', handleConfirmar);
                btnCancelar.removeEventListener('click', handleCancelar);
                modal.removeEventListener('hidden.bs.modal', handleCancelar);
            };

            btnConfirmar.addEventListener('click', handleConfirmar);
            btnCancelar.addEventListener('click', handleCancelar);
            modal.addEventListener('hidden.bs.modal', handleCancelar);

            // Mostrar modal
            const bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();
        });
    }

    formatearFecha(fecha) {
        if (!fecha) return '-';
        
        try {
            let date;
            
            // Si es un objeto (como viene de SQL Server)
            if (typeof fecha === 'object' && fecha.date) {
                date = new Date(fecha.date);
            } else if (typeof fecha === 'string') {
                // Si la fecha viene como string
                if (fecha.includes('T')) {
                    date = new Date(fecha);
                } else {
                    // Formato YYYY-MM-DD
                    const parts = fecha.split('-');
                    if (parts.length === 3) {
                        date = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
                    } else {
                        date = new Date(fecha);
                    }
                }
            } else {
                date = new Date(fecha);
            }
            
            // Verificar si la fecha es válida
            if (isNaN(date.getTime())) {
                console.log('Fecha inválida:', fecha);
                return fecha.toString();
            }
            
            return date.toLocaleDateString('es-AR', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit'
            });
            
        } catch (error) {
            console.error('Error al formatear fecha:', error, fecha);
            return fecha.toString();
        }
    }

    formatearHora(hora) {
        if (!hora) return '-';
        
        // Si la hora viene como string numérica (ej: "154033")
        const horaStr = hora.toString();
        if (horaStr.length === 6) {
            const hh = horaStr.substring(0, 2);
            const mm = horaStr.substring(2, 4);
            return `${hh}:${mm}`;
        } else if (horaStr.length === 5) {
            const h = horaStr.substring(0, 1);
            const mm = horaStr.substring(1, 3);
            return `0${h}:${mm}`;
        } else if (horaStr.length === 4) {
            const hh = horaStr.substring(0, 2);
            const mm = horaStr.substring(2, 4);
            return `${hh}:${mm}`;
        }
        
        // Si ya viene formateada o en otro formato
        return hora;
    }

    formatearNumero(numero) {
        if (!numero) return '0';
        return parseFloat(numero).toLocaleString('es-AR', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        });
    }

    mostrarToast(mensaje, tipo = 'info') {
        // Crear toast container si no existe
        let toastContainer = document.getElementById('toastContainer');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toastContainer';
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }

        // Crear toast único (remover anteriores del mismo tipo)
        const toastsExistentes = toastContainer.querySelectorAll('.toast');
        toastsExistentes.forEach(toast => {
            if (toast.classList.contains(`toast-${tipo}`)) {
                const instance = bootstrap.Toast.getInstance(toast);
                if (instance) {
                    instance.hide();
                }
                toast.remove();
            }
        });

        // Definir iconos y títulos por tipo
        const config = {
            success: { icon: 'check-circle-fill text-success', titulo: 'Éxito' },
            error: { icon: 'exclamation-triangle-fill text-danger', titulo: 'Error' },
            info: { icon: 'info-circle-fill text-info', titulo: 'Información' }
        };

        const tipoConfig = config[tipo] || config.info;

        // Crear toast
        const toastId = 'toast-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
        const toastHtml = `
            <div id="${toastId}" class="toast toast-custom toast-${tipo}" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i class="bi bi-${tipoConfig.icon} me-2"></i>
                    <strong class="me-auto">${tipoConfig.titulo}</strong>
                    <small class="text-muted">${new Date().toLocaleTimeString('es-AR', {hour: '2-digit', minute: '2-digit'})}</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    ${mensaje}
                </div>
            </div>
        `;

        toastContainer.insertAdjacentHTML('beforeend', toastHtml);

        // Mostrar toast
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, { 
            delay: tipo === 'success' ? 3000 : 5000,
            autohide: true
        });
        toast.show();

        // Limpiar después de que se oculte
        toastElement.addEventListener('hidden.bs.toast', () => {
            if (toastElement.parentNode) {
                toastElement.remove();
            }
        });

        // Auto-cerrar también por timeout como respaldo
        setTimeout(() => {
            if (toastElement && toastElement.parentNode) {
                const instance = bootstrap.Toast.getInstance(toastElement);
                if (instance) {
                    instance.hide();
                }
                toastElement.remove();
            }
        }, tipo === 'success' ? 3500 : 5500);
    }

    // Método para exportar datos a XLSX usando DataTables
    exportarExcel() {
        if (!this.datosCompletos || this.datosCompletos.length === 0) {
            this.mostrarToast('No hay datos para exportar', 'error');
            return;
        }

        try {
            // Preparar datos para exportar
            const datos = [];
            
            // Headers
            datos.push(['Fecha', 'Hora', 'Proveedor', 'N° Comprobante', 'Cantidad', 'Estado']);
            
            // Datos de los remitos
            this.datosCompletos.forEach(remito => {
                datos.push([
                    this.formatearFecha(remito.FECHA_MOV),
                    this.formatearHora(remito.HORA_INGRESO),
                    remito.COD_PRO_CL,
                    remito.N_COMP,
                    this.formatearNumero(remito.CANTIDAD),
                    remito.ESTADO
                ]);
            });

            // Crear archivo XLSX usando SheetJS
            this.crearArchivoXLSX(datos);
            
        } catch (error) {
            console.error('Error al preparar datos para exportar:', error);
            this.mostrarToast('Error al preparar datos para exportar', 'error');
        }
    }

    crearArchivoXLSX(datos) {
        try {
            // Crear un nuevo workbook
            const wb = XLSX.utils.book_new();
            
            // Crear worksheet desde los datos
            const ws = XLSX.utils.aoa_to_sheet(datos);
            
            // Configurar ancho de columnas
            const colWidths = [
                { wch: 12 }, // Fecha
                { wch: 8 },  // Hora
                { wch: 12 }, // Proveedor
                { wch: 15 }, // N° Comprobante
                { wch: 12 }, // Cantidad
                { wch: 15 }  // Estado
            ];
            ws['!cols'] = colWidths;
            
            // Agregar worksheet al workbook
            XLSX.utils.book_append_sheet(wb, ws, 'Remitos');
            
            // Generar nombre de archivo con fecha actual
            const fecha = new Date().toISOString().split('T')[0];
            const nombreArchivo = `remitos_${fecha}.xlsx`;
            
            // Descargar archivo
            XLSX.writeFile(wb, nombreArchivo);
            
            this.mostrarToast('Archivo Excel descargado correctamente', 'success');
            
        } catch (error) {
            console.error('Error al crear archivo Excel:', error);
            this.mostrarToast('Error al generar archivo Excel', 'error');
        }
    }

    abrirModalActualizarRemito() {
    const modal = new bootstrap.Modal(document.getElementById('modalActualizarRemito'));
    modal.show();
    
    // Focus en el input
    setTimeout(() => {
        document.getElementById('inputNRemito').focus();
    }, 500);
}

limpiarModalActualizarRemito() {
    document.getElementById('inputNRemito').value = '';
    document.getElementById('infoRemito').classList.add('d-none');
    document.getElementById('alertaRemito').classList.add('d-none');
    document.getElementById('btnEjecutarActualizacion').disabled = true;
    
    // Limpiar campos de detalle
    document.getElementById('detalleNumero').textContent = '-';
    document.getElementById('detalleFecha').textContent = '-';
    document.getElementById('detalleProveedor').textContent = '-';
    document.getElementById('detalleEstado').textContent = '-';
    document.getElementById('detalleTotalArticulos').textContent = '-';
    document.getElementById('detalleCantidadTotal').textContent = '-';
}

async verificarRemito() {
    const nRemito = document.getElementById('inputNRemito').value.trim();
    const btnVerificar = document.getElementById('btnVerificarRemito');
    const originalText = btnVerificar.innerHTML;
    
    if (!nRemito) {
        this.mostrarToast('Ingrese un número de remito', 'error');
        return;
    }

    try {
        // Mostrar loading
        btnVerificar.innerHTML = '<span class="loading-spinner"></span> Verificando...';
        btnVerificar.classList.add('btn-verificando');
        
        const formData = new FormData();
        formData.append('action', 'verificarRemito');
        formData.append('nComp', nRemito);

        const response = await fetch('/ecommerce/Abastecimiento/Controller/importarRemitos.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log('Verificación resultado:', data);

        if (data.success) {
            if (data.existe) {
                this.mostrarInformacionRemito(data);
                
                if (data.yaIngresado) {
                    this.mostrarAlertaRemito('El remito ya está marcado como ingresado (ESTADO = "I"). No se puede actualizar.', 'warning');
                    document.getElementById('btnEjecutarActualizacion').disabled = true;
                } else {
                    this.mostrarAlertaRemito('Remito encontrado y disponible para actualizar.', 'success');
                    document.getElementById('btnEjecutarActualizacion').disabled = false;
                }
            } else {
                this.ocultarInformacionRemito();
                this.mostrarAlertaRemito('Remito no encontrado o fuera del rango de fechas válidas (últimos 45 días).', 'danger');
                document.getElementById('btnEjecutarActualizacion').disabled = true;
            }
        } else {
            this.ocultarInformacionRemito();
            this.mostrarAlertaRemito('Error al verificar remito: ' + data.message, 'danger');
            document.getElementById('btnEjecutarActualizacion').disabled = true;
        }

    } catch (error) {
        console.error('Error:', error);
        this.ocultarInformacionRemito();
        this.mostrarAlertaRemito('Error de conexión: ' + error.message, 'danger');
        document.getElementById('btnEjecutarActualizacion').disabled = true;
    } finally {
        // Restaurar botón
        btnVerificar.innerHTML = originalText;
        btnVerificar.classList.remove('btn-verificando');
    }
}

mostrarInformacionRemito(data) {
    const infoRemito = document.getElementById('infoRemito');
    
    if (data.detalle) {
        const detalle = data.detalle;
        
        document.getElementById('detalleNumero').textContent = detalle.N_COMP || '-';
        document.getElementById('detalleFecha').textContent = this.formatearFecha(detalle.FECHA_MOV) || '-';
        document.getElementById('detalleProveedor').textContent = detalle.COD_PRO_CL || '-';
        
        // Estado con clase CSS
        const estadoElement = document.getElementById('detalleEstado');
        estadoElement.textContent = detalle.ESTADO || '-';
        estadoElement.className = `fw-bold estado-badge estado-${(detalle.ESTADO || '').toLowerCase()}`;
        
        document.getElementById('detalleTotalArticulos').textContent = detalle.TOTAL_ARTICULOS || '0';
        document.getElementById('detalleCantidadTotal').textContent = this.formatearNumero(detalle.CANTIDAD_TOTAL) || '0';
    }
    
    infoRemito.classList.remove('d-none');
    infoRemito.classList.add('fade-in-up');
}

ocultarInformacionRemito() {
    document.getElementById('infoRemito').classList.add('d-none');
}

mostrarAlertaRemito(mensaje, tipo) {
    const alerta = document.getElementById('alertaRemito');
    const alertDiv = alerta.querySelector('.alert');
    const mensajeSpan = document.getElementById('mensajeAlerta');
    
    // Remover clases de tipo previas
    alertDiv.classList.remove('alert-info', 'alert-success', 'alert-warning', 'alert-danger');
    
    // Agregar clase del tipo actual
    alertDiv.classList.add(`alert-${tipo}`);
    
    // Cambiar icono según el tipo
    const iconos = {
        success: 'check-circle',
        warning: 'exclamation-triangle',
        danger: 'x-circle',
        info: 'info-circle'
    };
    
    const icono = iconos[tipo] || 'info-circle';
    mensajeSpan.innerHTML = `<i class="bi bi-${icono} me-2"></i>${mensaje}`;
    
    alerta.classList.remove('d-none');
    alerta.classList.add('fade-in-up');
}

async ejecutarActualizacionRemito() {
    const nRemito = document.getElementById('inputNRemito').value.trim();
    const btnEjecutar = document.getElementById('btnEjecutarActualizacion');
    const originalText = btnEjecutar.innerHTML;

    if (!nRemito) {
        this.mostrarToast('Número de remito requerido', 'error');
        return;
    }

    // Confirmar acción
    const confirmacion = await this.mostrarConfirmacion(
        'Confirmar Actualización',
        `¿Está seguro que desea actualizar el remito ${nRemito}? Esta acción:\n\n• Cambiará el estado a 'P' (Procesado)\n• Actualizará las cantidades reales\n• No se puede deshacer`,
        'Sí, Actualizar',
        'Cancelar'
    );

    if (!confirmacion) {
        return;
    }

    try {
        // Mostrar loading
        btnEjecutar.innerHTML = '<span class="loading-spinner"></span> Actualizando...';
        btnEjecutar.disabled = true;

        const formData = new FormData();
        formData.append('action', 'actualizarRemito');
        formData.append('nComp', nRemito);

        const response = await fetch('/ecommerce/Abastecimiento/Controller/importarRemitos.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log('Actualización resultado:', data);

        if (data.success) {
            this.mostrarToast(data.message, 'success');
            
            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalActualizarRemito'));
            modal.hide();
            
            // Recargar datos de la tabla
            setTimeout(() => {
                this.cargarRemitos();
            }, 1000);
            
        } else {
            this.mostrarToast('Error: ' + data.message, 'error');
        }

    } catch (error) {
        console.error('Error:', error);
        this.mostrarToast('Error de conexión: ' + error.message, 'error');
    } finally {
        // Restaurar botón
        btnEjecutar.innerHTML = originalText;
        btnEjecutar.disabled = false;
    }
}

// Agregar método auxiliar para formatear estado
formatearEstado(estado) {
    const estados = {
        'P': { texto: 'Procesado', clase: 'estado-p' },
        'I': { texto: 'Ingresado', clase: 'estado-i' },
        'A': { texto: 'Anulado', clase: 'estado-a' }
    };
    
    const estadoInfo = estados[estado] || { texto: estado, clase: '' };
    return `<span class="${estadoInfo.clase}">${estadoInfo.texto}</span>`;
}

// ===== Alta de Partidas (RO_SP_ALTA_PARTIDAS) =====

inicializarSelect2AltaPartida() {
    const $modal = $('#modalAltaPartida');
    const endpoint = '/ecommerce/Abastecimiento/Controller/importarRemitos.php';

    // Artículo: búsqueda AJAX (mínimo 2 caracteres)
    $('#inputCodArticu').select2({
        theme: 'bootstrap-5',
        dropdownParent: $modal,
        placeholder: 'Buscar por código o descripción...',
        allowClear: true,
        minimumInputLength: 2,
        language: {
            inputTooShort: () => 'Escribí al menos 2 caracteres para buscar',
            searching: () => 'Buscando...',
            noResults: () => 'Sin resultados'
        },
        ajax: {
            url: endpoint,
            type: 'POST',
            dataType: 'json',
            delay: 300,
            data: (params) => ({ action: 'buscarArticulos', term: params.term }),
            processResults: (data) => ({ results: data.results || [] })
        }
    });

    // Depósito: carga completa con filtro local (pocos registros)
    $('#inputCodDepo').select2({
        theme: 'bootstrap-5',
        dropdownParent: $modal,
        placeholder: 'Seleccionar depósito...',
        allowClear: true,
        minimumInputLength: 0,
        language: {
            searching: () => 'Cargando depósitos...',
            noResults: () => 'Sin resultados'
        },
        ajax: {
            url: endpoint,
            type: 'POST',
            dataType: 'json',
            delay: 0,
            cache: true,
            data: () => ({ action: 'buscarDepositos' }),
            processResults: (data) => ({ results: data.results || [] })
        }
    });

    // Número de Partida: se puebla dinámicamente según artículo + depósito
    $('#inputNPartida').select2({
        theme: 'bootstrap-5',
        dropdownParent: $modal,
        placeholder: 'Seleccioná artículo y depósito primero'
    });
}

async cargarPartidasNPartida() {
    const codArticu = ($('#inputCodArticu').val() || '').trim();
    const codDepo   = ($('#inputCodDepo').val()   || '').trim();
    const $select   = $('#inputNPartida');

    $select.empty();

    if (!codArticu || !codDepo) {
        $select.append(new Option('Seleccioná artículo y depósito primero', '', true, true));
        $select.prop('disabled', true).trigger('change');
        return;
    }

    $select.append(new Option('Buscando partidas...', '', true, true));
    $select.prop('disabled', true).trigger('change');

    try {
        const formData = new FormData();
        formData.append('action', 'buscarPartidas');
        formData.append('codArticu', codArticu);
        formData.append('codDepo', codDepo);

        const response = await fetch('/ecommerce/Abastecimiento/Controller/importarRemitos.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        const results = data.results || [];

        $select.empty();

        if (results.length === 0) {
            $select.append(new Option('Sin partidas asociadas a este artículo/depósito', '', true, true));
            $select.prop('disabled', true).trigger('change');
            this.mostrarAlertaPartida('No se encontró ninguna partida asociada a este artículo y depósito. Esta herramienta solo puede usarse cuando existe una partida de importación pendiente de registrar.', 'warning');
            document.getElementById('alertaPartida').classList.remove('d-none');
        } else {
            $select.append(new Option('', '', true, true));
            results.forEach(r => {
                $select.append(new Option(r.text, r.id, false, false));
            });
            $select.prop('disabled', false).trigger('change');
        }

    } catch (error) {
        console.error('Error al cargar partidas:', error);
        $select.empty();
        $select.append(new Option('Error al cargar partidas', '', true, true));
        $select.prop('disabled', true).trigger('change');
    }
}

abrirModalAltaPartida() {
    const modal = new bootstrap.Modal(document.getElementById('modalAltaPartida'));
    modal.show();
    setTimeout(() => {
        document.getElementById('inputCodArticu').focus();
    }, 500);
}

limpiarModalAltaPartida() {
    // Limpiar Select2 (requiere la API de jQuery)
    $('#inputCodArticu').val(null).trigger('change');
    $('#inputCodDepo').val(null).trigger('change');
    document.getElementById('inputCantidad').value = '';
    document.getElementById('inputCantidad').placeholder = 'Automático';
    document.getElementById('infoPartida').classList.add('d-none');
    document.getElementById('alertaPartida').classList.add('d-none');
    document.getElementById('btnEjecutarAltaPartida').disabled = true;

    document.getElementById('detalleStock').textContent = '-';
    document.getElementById('detalleSta10').textContent = '-';
    document.getElementById('detalleSta11').textContent = '-';
    document.getElementById('detalleSta22').textContent = '-';
}

async verificarPartida() {
    const codArticu = ($('#inputCodArticu').val() || '').trim();
    const codDepo   = ($('#inputCodDepo').val()   || '').trim();
    const btnVerificar = document.getElementById('btnVerificarPartida');
    const originalText = btnVerificar.innerHTML;

    if (!codArticu || !codDepo) {
        this.mostrarToast('Ingrese código de artículo y depósito', 'error');
        return;
    }

    try {
        btnVerificar.innerHTML = '<span class="loading-spinner"></span> Verificando...';
        btnVerificar.disabled = true;

        const formData = new FormData();
        formData.append('action', 'verificarPartida');
        formData.append('codArticu', codArticu);
        formData.append('codDepo', codDepo);

        const response = await fetch('/ecommerce/Abastecimiento/Controller/importarRemitos.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log('Verificación partida:', data);

        if (!data.success) {
            this.ocultarInfoPartida();
            this.mostrarAlertaPartida('Error al verificar: ' + (data.message || 'Error desconocido'), 'danger');
            document.getElementById('btnEjecutarAltaPartida').disabled = true;
            return;
        }

        // Mostrar info
        const stockTexto = (data.stockSta19 !== null && data.stockSta19 !== undefined)
            ? this.formatearNumero(data.stockSta19)
            : 'Sin stock';
        document.getElementById('detalleStock').textContent = stockTexto;
        document.getElementById('detalleSta10').textContent = data.existeEnSta10 ? 'Ya existe' : 'No existe';
        document.getElementById('detalleSta11').textContent = data.existeEnSta11 ? 'Sí' : 'No';
        document.getElementById('detalleSta22').textContent = data.existeEnSta22 ? 'Sí' : 'No';
        document.getElementById('infoPartida').classList.remove('d-none');
        document.getElementById('infoPartida').classList.add('fade-in-up');

        // Sugerir cantidad si el campo está vacío
        const inputCantidad = document.getElementById('inputCantidad');
        if (!inputCantidad.value && data.stockSta19 !== null && data.stockSta19 !== undefined) {
            inputCantidad.placeholder = `Automático (STA19: ${stockTexto})`;
        }

        const btnEjecutar = document.getElementById('btnEjecutarAltaPartida');

        // Reglas de habilitación según las validaciones del SP
        if (data.existeEnSta10) {
            this.mostrarAlertaPartida('Este artículo ya tiene una partida registrada para ese depósito. No es necesario volver a darla de alta.', 'warning');
            btnEjecutar.disabled = true;
        } else if (!data.existeEnSta11) {
            this.mostrarAlertaPartida('El código de artículo no existe en el sistema.', 'danger');
            btnEjecutar.disabled = true;
        } else if (!data.existeEnSta22) {
            this.mostrarAlertaPartida('El depósito seleccionado no existe en el sistema.', 'danger');
            btnEjecutar.disabled = true;
        } else if ((data.stockSta19 === null || data.stockSta19 === undefined) && !inputCantidad.value) {
            this.mostrarAlertaPartida('No hay stock disponible registrado para este artículo y depósito. Ingresá una cantidad manualmente.', 'warning');
            btnEjecutar.disabled = true;
        } else if (!data.tienePartidasCandidatas) {
            this.mostrarAlertaPartida('No se encontró ninguna partida asociada a este artículo y depósito. Esta herramienta solo puede usarse cuando existe una partida de importación pendiente de registrar.', 'warning');
            btnEjecutar.disabled = true;
        } else {
            this.mostrarAlertaPartida('Verificación correcta. Podés proceder con el alta.', 'success');
            btnEjecutar.disabled = false;
        }

    } catch (error) {
        console.error('Error:', error);
        this.ocultarInfoPartida();
        this.mostrarAlertaPartida('Error de conexión: ' + error.message, 'danger');
        document.getElementById('btnEjecutarAltaPartida').disabled = true;
    } finally {
        btnVerificar.innerHTML = originalText;
        btnVerificar.disabled = false;
    }
}

ocultarInfoPartida() {
    document.getElementById('infoPartida').classList.add('d-none');
}

mostrarAlertaPartida(mensaje, tipo) {
    const alerta = document.getElementById('alertaPartida');
    const alertDiv = alerta.querySelector('.alert');
    const mensajeSpan = document.getElementById('mensajeAlertaPartida');

    alertDiv.classList.remove('alert-info', 'alert-success', 'alert-warning', 'alert-danger');
    alertDiv.classList.add(`alert-${tipo}`);

    const iconos = {
        success: 'check-circle',
        warning: 'exclamation-triangle',
        danger: 'x-circle',
        info: 'info-circle'
    };
    const icono = iconos[tipo] || 'info-circle';
    mensajeSpan.innerHTML = `<i class="bi bi-${icono} me-2"></i>${mensaje}`;

    alerta.classList.remove('d-none');
    alerta.classList.add('fade-in-up');
}

async ejecutarAltaPartida() {
    const codArticu = ($('#inputCodArticu').val() || '').trim();
    const codDepo   = ($('#inputCodDepo').val()   || '').trim();
    const nPartida  = document.getElementById('inputNPartida').value.trim();
    const cantidad = document.getElementById('inputCantidad').value.trim();
    const btnEjecutar = document.getElementById('btnEjecutarAltaPartida');
    const originalText = btnEjecutar.innerHTML;

    if (!codArticu || !codDepo) {
        this.mostrarToast('Seleccioná artículo y depósito antes de continuar', 'error');
        return;
    }

    if (!nPartida) {
        this.mostrarToast('Seleccioná un número de partida antes de continuar', 'error');
        return;
    }

    const cantidadTexto = cantidad ? cantidad : 'la del stock (STA19)';
    const confirmacion = await this.mostrarConfirmacion(
        'Confirmar Alta de Partida',
        `¿Dar de alta la partida ${nPartida} para el artículo ${codArticu} en el depósito ${codDepo} con cantidad ${cantidadTexto}? Esta acción inserta en STA10 y no se puede deshacer.`,
        'Sí, Dar de Alta',
        'Cancelar'
    );

    if (!confirmacion) {
        return;
    }

    try {
        btnEjecutar.innerHTML = '<span class="loading-spinner"></span> Ejecutando...';
        btnEjecutar.disabled = true;

        const formData = new FormData();
        formData.append('action', 'altaPartida');
        formData.append('codArticu', codArticu);
        formData.append('codDepo', codDepo);
        formData.append('nPartida', nPartida);
        formData.append('cantidad', cantidad);

        const response = await fetch('/ecommerce/Abastecimiento/Controller/importarRemitos.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log('Alta partida resultado:', data);

        if (data.success) {
            this.mostrarToast(data.message, 'success');
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalAltaPartida'));
            modal.hide();
        } else {
            this.mostrarToast('Error: ' + data.message, 'error');
        }

    } catch (error) {
        console.error('Error:', error);
        this.mostrarToast('Error de conexión: ' + error.message, 'error');
    } finally {
        btnEjecutar.innerHTML = originalText;
        btnEjecutar.disabled = false;
    }
}
}

// Inicializar cuando el DOM esté listo
$(document).ready(function() {
    console.log('DOM y jQuery listos, inicializando RemitoManager...');
    window.remitoManager = new RemitoManager();
});
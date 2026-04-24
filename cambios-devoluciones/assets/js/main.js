/**
 * Logística Inversa — JS principal
 * Gestiona búsqueda de pedidos, alta de devoluciones y listado.
 */

'use strict';

/* ============================================================
   CONSTANTES Y CONFIG
   ============================================================ */
const API_BASE = window.LOGISTICA_API_BASE || 'api/';

const ESTADOS_LABEL = {
    pendiente:    '<span class="badge badge-pendiente text-dark">Pendiente</span>',
    en_transito:  '<span class="badge badge-en_transito text-dark">En tránsito</span>',
    recibido:     '<span class="badge badge-recibido text-white">Recibido</span>',
    resuelto:     '<span class="badge badge-resuelto text-white">Resuelto</span>',
};

const TIPOS_LABEL = {
    cambio:      '<span class="chip-tipo chip-cambio">Cambio</span>',
    devolucion:  '<span class="chip-tipo chip-devolucion">Devolución</span>',
};

/* Estado local de la sesión de alta */
let pedidoCargado = null;
let localesData    = null; // Cache de tiendas/locales para Select2

/* ============================================================
   PASOS — PROGRESS INDICATOR
   ============================================================ */
function actualizarPasos(paso) {
    const $s1  = $('#step-1'),  $s2  = $('#step-2'),  $s3  = $('#step-3');
    const $l12 = $('#step-line-12'), $l23 = $('#step-line-23');
    if (paso >= 2) {
        $s1.removeClass('li-step-active').addClass('li-step-done');
        $s1.find('.li-step-num').html('<i class="fas fa-check"></i>');
        $l12.addClass('li-step-line-done');
        $s2.addClass('li-step-active');
        $s3.addClass('li-step-active');
        $l23.addClass('li-step-line-done');
    } else {
        $s1.addClass('li-step-active').removeClass('li-step-done');
        $s1.find('.li-step-num').text('1');
        $l12.removeClass('li-step-line-done');
        $s2.removeClass('li-step-active li-step-done');
        $s3.removeClass('li-step-active li-step-done');
        $l23.removeClass('li-step-line-done');
    }
}

/* ============================================================
   LOCALES — CARGA Y SELECT2
   ============================================================ */

/** Trae la lista de tiendas/locales del servidor (sólo una vez). */
function cargarLocales() {
    if (localesData !== null) return;
    $.getJSON(API_BASE + 'listar_locales.php', function (data) {
        localesData = Array.isArray(data) ? data : [];
    }).fail(function () {
        localesData = [];
        console.warn('Logística Inversa: no se pudo cargar la lista de locales.');
    });
}

/**
 * Inicializa Select2 en todos los <select class="select-stock-cambio"> del tbody.
 * @param {jQuery} $tbody
 */
function inicializarSelect2StockCambio($tbody) {
    if (typeof $.fn.select2 === 'undefined') return;
    $tbody.find('.select-stock-cambio').each(function () {
        $(this).select2({
            placeholder: 'Seleccionar local…',
            allowClear:  true,
            data:        localesData || [],
            width:       '100%',
            dropdownParent: $('body'),
        });
    });
}

/* ============================================================
   INICIALIZACIÓN
   ============================================================ */
$(function () {
    cargarLocales(); // Pre-cargar locales para Select2

    // Tab listado: cargar al mostrar
    $('#listado-tab').on('shown.bs.tab', function () {
        cargarListado();
    });

    // Botón buscar pedido
    $('#btn-buscar-pedido').on('click', buscarPedido);

    // Formulario guardar devolución
    $('#form-devolucion').on('submit', guardarDevolucion);

    // Filtros del listado
    $('#btn-filtrar').on('click', cargarListado);
    $('#btn-limpiar-filtros').on('click', function () {
        $('#filtro-estado, #filtro-tipo').val('');
        $('#filtro-desde, #filtro-hasta').val('');
        cargarListado();
    });

    // Input cantidad: solo números positivos
    $(document).on('input', '.cant-input', function () {
        let v = parseInt($(this).val(), 10);
        if (isNaN(v) || v < 1) $(this).val(1);
    });

    // Cálculo automático de diferencia de precio
    $(document).on('input', '#campo-precio-abonado, #campo-precio-art-cambio', function () {
        const abonado = parseFloat($('#campo-precio-abonado').val()) || 0;
        const cambio  = parseFloat($('#campo-precio-art-cambio').val()) || 0;
        const diff    = abonado - cambio;
        $('#campo-diferencia-precio').val(diff !== 0 ? diff.toFixed(2) : '');
    });

    // Enter para buscar pedido
    $('#pedido-id').on('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); buscarPedido(); }
    });

    // Sincronizar Tipo con la Acción de cada fila de productos
    $(document).on('change', '#campo-tipo', function () {
        const tipo = $(this).val();
        if (!tipo || !$('#tabla-productos tbody tr[data-codigo]').length) return;
        const accion = tipo === 'cambio' ? 'cambio' : 'devolucion';
        $('#tabla-productos .select-accion').val(accion);
        $('#li-sync-hint').stop(true, true).show().delay(3000).fadeOut(400);
    });

    // Búsqueda en tiempo real en el listado
    $(document).on('input', '#busqueda-listado', function () {
        const q = $(this).val().toLowerCase();
        $('#tabla-listado tbody tr').each(function () {
            $(this).toggle(q === '' || $(this).text().toLowerCase().includes(q));
        });
    });

    // Animar ícono chevron en secciones colapsables
    $(document).on('show.bs.collapse hide.bs.collapse',
        '#seccion-resolucion, #seccion-financiero', function (e) {
        const $icon = $('[href="#' + e.target.id + '"] .li-toggle-icon');
        $icon.toggleClass('li-toggle-open', e.type === 'show');
    });

    // Limpiar estado inválido al corregir un campo de la tabla de productos
    $(document).on('input change', '#tabla-productos .input-stock-origen, #tabla-productos .input-cod-cambio', function () {
        $(this).removeClass('is-invalid');
        $(this).closest('.li-product-card').find('.li-row-error').remove();
    });

    // Al salir del campo código de cambio, buscar descripción automáticamente
    $(document).on('blur', '#tabla-productos .input-cod-cambio', function () {
        const $input = $(this);
        const codigo = $input.val().trim().toUpperCase();
        const $card  = $input.closest('.li-product-card');
        const $desc  = $card.find('.input-desc-cambio');

        if (!codigo) {
            $desc.val('');
            return;
        }

        // Feedback visual mientras busca
        $input.prop('readonly', true);
        $desc.val('Buscando…').prop('readonly', true);

        $.getJSON(API_BASE + 'buscar_articulo.php', { codigo: codigo })
            .done(function (data) {
                $desc.val(data.descripcion || '');
            })
            .fail(function () {
                $desc.val('');
                $input.addClass('is-invalid');
                $card.find('.li-row-error').remove();
                $card.append(
                    '<div class="li-row-error text-danger small mt-2 px-1">' +
                    '<i class="fas fa-exclamation-circle me-1"></i>Artículo no encontrado: ' +
                    escHtml(codigo) + '</div>'
                );
            })
            .always(function () {
                $input.prop('readonly', false);
                $desc.prop('readonly', false);
            });
    });

    // Limpiar validación al seleccionar un local en el Select2
    $(document).on('select2:select select2:clear', '#tabla-productos .select-stock-cambio', function () {
        $(this).next('.select2-container').removeClass('is-invalid');
        $(this).closest('.li-product-card').find('.li-row-error').remove();
    });
});

/* ============================================================
   BÚSQUEDA DE PEDIDO
   ============================================================ */
function buscarPedido() {
    const pedidoId = $('#pedido-id').val().trim();
    if (!pedidoId) {
        mostrarAlerta('Ingrese un número de pedido.', 'warning');
        return;
    }

    $('#btn-buscar-pedido').prop('disabled', true).html(
        '<span class="spinner-border spinner-border-sm me-1"></span>Buscando...'
    );
    $('#pedido-encontrado').hide();
    $('#seccion-productos').addClass('disabled-section');

    $.ajax({
        url:    API_BASE + 'buscar_pedido.php',
        method: 'POST',
        data:   { pedido_id: pedidoId },
        dataType: 'json',
    })
    .done(function (resp) {
        if (!resp.success) {
            mostrarAlerta(resp.error || 'Pedido no encontrado.', 'danger');
            pedidoCargado = null;
            return;
        }
        pedidoCargado = resp;
        renderizarPedidoEncontrado(resp.cabecera, resp.detalle);
        renderizarTablaProductos(resp.detalle);
        $('#seccion-productos').removeClass('disabled-section');
        $('#seccion-paso2').removeClass('disabled-section');
        actualizarPasos(2);
        // Auto-scroll hacia el formulario
        setTimeout(function () {
            const el = document.getElementById('seccion-paso2');
            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 300);
    })
    .fail(function (xhr) {
        const msg = xhr.responseJSON?.error || 'Error al conectar con el servidor.';
        mostrarAlerta(msg, 'danger');
    })
    .always(function () {
        $('#btn-buscar-pedido').prop('disabled', false).html(
            '<i class="fas fa-search me-1"></i>Buscar'
        );
    });
}

/* ============================================================
   RENDER — DATOS DEL PEDIDO
   ============================================================ */
function renderizarPedidoEncontrado(cab, detalle) {
    const cliente    = cab.CLIENTE        || cab.RAZON_SOCI      || cab.cliente        || '—';
    const fecha      = cab.FECHA_PEDIDO   || cab.fecha_pedido    || '';
    const hora       = cab.HORA           || '';
    const nroPedido  = cab.NRO_PEDIDO     || '—';
    const nroOrden   = cab.NRO_ORDEN      || cab.ORDER_ID_TIENDA || '—';
    const factura    = cab.FACTURA        || '—';
    const canal      = cab.MARKETPLACE    || cab.ORIGEN          || cab.TIENDA || '—';
    const prepara    = cab.PREPARA        || cab.WAREHOUSE        || '—';
    const envio      = cab.METODO_ENVIO   || '—';
    const sucursal   = cab.SUCURSAL_ENTREGA || cab.WAREHOUSE     || '—';
    const direccion  = cab.DIRECCION_ENTREGA || cab.DEPARTAMENTO || '—';

    // Pre-completar campos ocultos del formulario
    $('#campo-cliente').val(cliente);
    $('#campo-fecha-pedido').val(formatearFechaInput(fecha));

    // ---- Card de info general ----
    const infoHtml = `
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="fw-bold text-primary mb-3">
                    <i class="fas fa-check-circle me-2"></i>Pedido encontrado
                </h6>
                <div class="row g-3">
                    <div class="col-sm-6 col-md-4">
                        <div class="info-label">Fecha y Hora</div>
                        <div class="info-value">${escHtml(formatearFecha(fecha))} ${escHtml(hora)}</div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="info-label">Marketplace / Canal</div>
                        <div class="info-value">${escHtml(canal)}</div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="info-label">Nro. Pedido</div>
                        <div class="info-value fw-bold">${escHtml(nroPedido)}</div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="info-label">Nro. Orden</div>
                        <div class="info-value">${escHtml(nroOrden)}</div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="info-label">Nro. Factura</div>
                        <div class="info-value">${escHtml(factura)}</div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="info-label">Cliente</div>
                        <div class="info-value">${escHtml(cliente)}</div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="info-label">Dirección de Entrega</div>
                        <div class="info-value">${escHtml(direccion)}</div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="info-label">Prepara</div>
                        <div class="info-value">${escHtml(prepara)}</div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="info-label">Método de Envío</div>
                        <div class="info-value">${escHtml(envio)}</div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="info-label">Sucursal Entrega</div>
                        <div class="info-value">${escHtml(sucursal)}</div>
                    </div>
                </div>
            </div>
        </div>`;

    // ---- Tabla de productos (solo lectura, para referencia) ----
    let filas = '';
    let total = 0;

    const detallesFiltrados = (detalle || []).filter(function (item) {
        const cod = item.COD_ARTICU || item.COD_ARTICULO || '';
        return cod.toUpperCase().indexOf('OHGIFT') === -1;
    });

    if (detallesFiltrados.length === 0) {
        filas = '<tr><td colspan="5" class="text-center text-muted py-3">Sin artículos en el pedido.</td></tr>';
    } else {
        detallesFiltrados.forEach(function (item) {
            const codigo   = item.COD_ARTICU   || item.COD_ARTICULO  || '';
            const descRaw  = item.DESCRIPCIO   || item.DESCRIPCION   || item.DESC_ARTICULO || codigo;
            const isSale   = descRaw.endsWith('-- SALE! --');
            const desc     = isSale ? descRaw.slice(0, -11).trim() : descRaw;
            const cantidad = parseInt(item.CANT_PEDID || item.CANTIDAD || 0, 10);
            const precio   = parseFloat(item.IMPORTE  || item.PRECIO  || 0);
            const subtotal = cantidad * precio;
            const esFaltante = parseInt(item.FALTANTE || 0, 10) === 1;
            total += subtotal;

            const badgeEstado = esFaltante
                ? `<span class="badge bg-danger"><i class="fas fa-exclamation-triangle me-1"></i>Faltante</span>`
                : `<span class="badge bg-success"><i class="fas fa-check me-1"></i>Normal</span>`;

            const saleBadge = isSale ? `<span class="badge bg-danger ms-1">SALE</span>` : '';

            const imgCod = codigo.substring(0, 13);
            filas += `
                <tr class="${esFaltante ? 'table-warning' : ''}">
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="li-thumb-wrap">
                                <img src="/Imagenes/${escHtml(imgCod)}.jpg"
                                     class="li-thumb"
                                     alt="${escHtml(codigo)}"
                                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                                <span class="li-thumb-empty" style="display:none"><i class="fas fa-image"></i></span>
                            </div>
                            <div>
                                <div class="fw-bold text-primary">${escHtml(desc)}${saleBadge}</div>
                                <div class="text-muted"><small>Código: ${escHtml(codigo)}</small></div>
                            </div>
                        </div>
                    </td>
                    <td class="text-end">$ ${formatearMoneda(precio)}</td>
                    <td class="text-center">${cantidad}</td>
                    <td class="text-end">$ ${formatearMoneda(subtotal)}</td>
                    <td class="text-center">${badgeEstado}</td>
                </tr>`;
        });
    }

    const totalFila = total > 0
        ? `<tr><td colspan="3" class="text-end fw-bold">Total</td>
               <td class="text-end fw-bold">$ ${formatearMoneda(total)}</td>
               <td></td></tr>`
        : '';

    const tablaHtml = `
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Detalle del Pedido</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Producto</th>
                                <th class="text-end" style="width:110px">Precio</th>
                                <th class="text-center" style="width:70px">Cant.</th>
                                <th class="text-end" style="width:110px">Total</th>
                                <th class="text-center" style="width:110px">Estado</th>
                            </tr>
                        </thead>
                        <tbody>${filas}${totalFila}</tbody>
                    </table>
                </div>
            </div>
        </div>`;

    $('#pedido-encontrado').html(infoHtml + tablaHtml).slideDown(200);
}

function renderizarTablaProductos(detalle) {
    const $container = $('#tabla-productos');
    $container.empty();

    const detallesFiltrados = (detalle || []).filter(function (item) {
        const cod = item.COD_ARTICU || item.COD_ARTICULO || '';
        return cod.toUpperCase().indexOf('OHGIFT') === -1;
    });

    if (detallesFiltrados.length === 0) {
        $container.html('<div class="text-center text-muted py-4"><i class="fas fa-search me-2"></i>Sin productos en el pedido.</div>');
        return;
    }

    detallesFiltrados.forEach(function (item, idx) {
        const codigo         = item.COD_ARTICU  || item.COD_ARTICULO || '';
        const descRaw        = item.DESCRIPCIO  || item.DESCRIPCION  || item.DESC_ARTICULO || codigo;
        const desc           = descRaw.endsWith('-- SALE! --') ? descRaw.slice(0, -11).trim() : descRaw;
        const cantOrig       = parseInt(item.CANT_PEDID || item.CANTIDAD || 1, 10);
        const imgCod         = codigo.substring(0, 13);
        const stockOrigenPre = (pedidoCargado && pedidoCargado.cabecera)
            ? (pedidoCargado.cabecera.WAREHOUSE || pedidoCargado.cabecera.PREPARA || '')
            : '';

        $container.append(`
            <div class="li-product-card"
                 data-idx="${idx}"
                 data-codigo="${escHtml(codigo)}"
                 data-nombre="${escHtml(desc)}">

                <!-- Cabecera del artículo -->
                <div class="d-flex align-items-center gap-3 mb-3 pb-2 border-bottom">
                    <div class="li-thumb-wrap flex-shrink-0">
                        <img src="/Imagenes/${escHtml(imgCod)}.jpg"
                             class="li-thumb"
                             alt="${escHtml(codigo)}"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <span class="li-thumb-empty" style="display:none"><i class="fas fa-image"></i></span>
                    </div>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="fw-bold">${escHtml(desc)}</div>
                        <small class="text-muted">Código: ${escHtml(codigo)}</small>
                    </div>
                    <div class="flex-shrink-0 text-center ms-auto">
                        <div class="small text-muted">Cant. original</div>
                        <span class="badge bg-secondary fs-6 px-3">${cantOrig}</span>
                    </div>
                </div>

                <!-- Fila A: datos de gestión -->
                <div class="row g-2 mb-2">
                    <div class="col-6 col-md-2">
                        <label class="li-field-label">Cant. a gestionar</label>
                        <input type="number" class="form-control form-control-sm cant-input"
                               value="1" min="1" max="${cantOrig}" data-max="${cantOrig}">
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="li-field-label">Estado del artículo</label>
                        <select class="form-select form-select-sm select-estado-prod">
                            <option value="nuevo">Nuevo</option>
                            <option value="usado">Usado</option>
                            <option value="fallado">Fallado</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-4">
                        <label class="li-field-label">Stock de origen <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm input-stock-origen"
                               placeholder="Central / Tienda"
                               value="${escHtml(stockOrigenPre)}">
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="li-field-label">Acción</label>
                        <select class="form-select form-select-sm select-accion">
                            <option value="devolucion">Devolución</option>
                            <option value="cambio">Cambio</option>
                        </select>
                    </div>
                </div>

                <!-- Fila B: datos del artículo de cambio -->
                <div class="row g-2 li-fila-cambio">
                    <div class="col-6 col-md-3">
                        <label class="li-field-label">Cód. artículo de cambio <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm input-cod-cambio"
                               placeholder="Código">
                    </div>
                    <div class="col-6 col-md-5">
                        <label class="li-field-label">Descripción del artículo de cambio</label>
                        <input type="text" class="form-control form-control-sm input-desc-cambio"
                               placeholder="Descripción"
                               value="${escHtml(desc)}">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="li-field-label">Local de origen del cambio <span class="text-danger">*</span></label>
                        <select class="select-stock-cambio" style="width:100%">
                            <option value=""></option>
                        </select>
                    </div>
                </div>

            </div>
        `);
    });

    inicializarSelect2StockCambio($container);
}


/* ============================================================
   GUARDAR DEVOLUCIÓN
   ============================================================ */
function guardarDevolucion(e) {
    e.preventDefault();

    if (!pedidoCargado) {
        mostrarAlerta('Primero busque y confirme un pedido.', 'warning');
        return;
    }

    const pedidoId   = $('#pedido-id').val().trim();
    const tipo       = $('#campo-tipo').val();
    const motivo     = $('#campo-motivo').val().trim();
    const usuario    = $('#campo-usuario').val().trim();
    const cliente    = $('#campo-cliente').val().trim();
    const fechaPed   = $('#campo-fecha-pedido').val().trim();

    if (!tipo) {
        mostrarAlerta('Seleccione el tipo de operación.', 'warning');
        return;
    }

    // Limpiar errores anteriores de la tabla
    $('#tabla-productos .is-invalid').removeClass('is-invalid');
    $('#tabla-productos .select2-container.is-invalid').removeClass('is-invalid');
    $('#tabla-productos .li-row-error').remove();

    // Construir items desde la tabla
    const items = [];
    let primeraFilaConError = null;

    $('#tabla-productos .li-product-card').each(function () {
        const $row      = $(this);
        const codigo    = $row.data('codigo');
        const nombre    = $row.data('nombre');
        const cantidad  = parseInt($row.find('.cant-input').val(), 10) || 1;
        const accion    = $row.find('.select-accion').val();
        const max       = parseInt($row.find('.cant-input').data('max'), 10);
        const $stockOri    = $row.find('.input-stock-origen');
        const $codCambio   = $row.find('.input-cod-cambio');
        const $stockCambio = $row.find('.select-stock-cambio');

        if (cantidad > max) {
            mostrarAlerta(`La cantidad (${cantidad}) supera la original (${max}) para "${nombre}".`, 'warning');
            items.length = 0;
            return false;
        }

        // --- Validaciones de campos requeridos ---
        const errores = [];

        if (!$stockOri.val().trim()) {
            $stockOri.addClass('is-invalid');
            errores.push('Stock de origen');
        }

        if (accion === 'cambio') {
            if (!$codCambio.val().trim()) {
                $codCambio.addClass('is-invalid');
                errores.push('Cód. cambio');
            }
            if (!$stockCambio.val()) {
                $stockCambio.next('.select2-container').addClass('is-invalid');
                errores.push('Local de origen del cambio');
            }
        }

        if (errores.length) {
            $row.append(
                `<div class="li-row-error text-danger small mt-2 px-1">` +
                `<i class="fas fa-exclamation-circle me-1"></i>Completar: ${errores.join(', ')}` +
                `</div>`
            );
            if (!primeraFilaConError) primeraFilaConError = $row[0];
            items.length = 0;
            return false; // romper each
        }

        items.push({
            producto_id:        codigo,
            producto_nombre:    nombre,
            cantidad,
            accion,
            estado_producto:    $row.find('.select-estado-prod').val(),
            stock_origen:       $stockOri.val().trim(),
            codigo_cambio:      $codCambio.val().trim(),
            descripcion_cambio: $row.find('.input-desc-cambio').val().trim(),
            stock_cambio:       ($stockCambio.val() || '').trim(),
        });
    });

    if (primeraFilaConError) {
        primeraFilaConError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        mostrarAlerta('Completá los campos requeridos en la tabla de productos (marcados en rojo).', 'warning');
    }

    if (items.length === 0) return;

    const payload = {
        pedido_id:          pedidoId,
        cliente,
        fecha_pedido:       fechaPed,
        tipo,
        motivo,
        usuario,
        observaciones:      $('#campo-observaciones').val().trim(),
        nro_rto:            $('#campo-nro-rto').val().trim(),
        nro_nc_fact:        $('#campo-nro-nc-fact').val().trim(),
        precio_abonado:     $('#campo-precio-abonado').val()     !== '' ? parseFloat($('#campo-precio-abonado').val())     : null,
        precio_art_cambio:  $('#campo-precio-art-cambio').val()  !== '' ? parseFloat($('#campo-precio-art-cambio').val())  : null,
        diferencia_precio:  $('#campo-diferencia-precio').val()  !== '' ? parseFloat($('#campo-diferencia-precio').val())  : null,
        link_pago_mp:       $('#campo-link-pago-mp').val().trim(),
        nro_operacion_mp:   $('#campo-nro-operacion-mp').val().trim(),
        items,
    };

    const $btn = $('#btn-guardar');
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Guardando...');

    $.ajax({
        url:         API_BASE + 'crear_devolucion.php',
        method:      'POST',
        contentType: 'application/json',
        data:        JSON.stringify(payload),
        dataType:    'json',
    })
    .done(function (resp) {
        if (!resp.success) {
            mostrarAlerta(resp.error || 'No se pudo guardar.', 'danger');
            return;
        }
        Swal.fire({
            icon:  'success',
            title: '¡Guardado!',
            text:  `Devolución #${resp.devolucion_id} registrada correctamente.`,
            confirmButtonText: 'Ver listado',
        }).then(function () {
            resetFormulario();
            bootstrap.Tab.getOrCreateInstance(document.querySelector('#listado-tab')).show();
        });
    })
    .fail(function (xhr) {
        const msg = xhr.responseJSON?.error || 'Error al conectar con el servidor.';
        mostrarAlerta(msg, 'danger');
    })
    .always(function () {
        $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Guardar registro');
    });
}

/* ============================================================
   LISTADO DE DEVOLUCIONES
   ============================================================ */
function cargarListado() {
    const params = {
        estado: $('#filtro-estado').val() || '',
        tipo:   $('#filtro-tipo').val()   || '',
        desde:  $('#filtro-desde').val()  || '',
        hasta:  $('#filtro-hasta').val()  || '',
    };

    // Limpiar parámetros vacíos
    Object.keys(params).forEach(k => { if (!params[k]) delete params[k]; });

    $('#tabla-listado tbody').html(
        '<tr><td colspan="7" class="text-center py-3">' +
        '<span class="spinner-border spinner-border-sm me-2"></span>Cargando...</td></tr>'
    );

    $.ajax({
        url:      API_BASE + 'listar_devoluciones.php',
        method:   'GET',
        data:     params,
        dataType: 'json',
    })
    .done(function (resp) {
        if (!resp.success) {
            mostrarAlertaListado(resp.error || 'Error al cargar.', 'danger');
            return;
        }
        renderizarListado(resp.data);
        $('#total-registros').text(resp.total + ' registro(s)');
    })
    .fail(function () {
        mostrarAlertaListado('Error al conectar con el servidor.', 'danger');
    });
}

function renderizarListado(data) {
    const $tbody = $('#tabla-listado tbody');
    $tbody.empty();

    if (!data || data.length === 0) {
        $tbody.append('<tr><td colspan="7" class="text-center text-muted py-3">No hay registros.</td></tr>');
        return;
    }

    data.forEach(function (row) {
        $tbody.append(`
            <tr>
                <td><span class="fw-bold text-primary">${escHtml(row.nro_seguimiento || '—')}</span></td>
                <td><strong>${escHtml(row.pedido_id)}</strong></td>
                <td>${escHtml(row.cliente || '—')}</td>
                <td>${TIPOS_LABEL[row.tipo] || escHtml(row.tipo)}</td>
                <td>${ESTADOS_LABEL[row.estado] || escHtml(row.estado)}</td>
                <td>${formatearFecha(row.fecha_creacion)}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-primary me-1 btn-ver-detalle"
                            data-id="${row.id}" title="Ver detalle">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary btn-cambiar-estado"
                            data-id="${row.id}" data-estado="${escHtml(row.estado)}" title="Cambiar estado">
                        <i class="fas fa-exchange-alt"></i>
                    </button>
                </td>
            </tr>
        `);
    });

    // Eventos en los botones del listado (delegados)
    $tbody.find('.btn-ver-detalle').on('click', function () {
        verDetalle($(this).data('id'));
    });
    $tbody.find('.btn-cambiar-estado').on('click', function () {
        cambiarEstado($(this).data('id'), $(this).data('estado'));
    });
}

/* ============================================================
   VER DETALLE
   ============================================================ */
function verDetalle(id) {
    $('#modal-detalle-body').html(
        '<div class="text-center py-3"><span class="spinner-border"></span></div>'
    );
    const modal = new bootstrap.Modal(document.getElementById('modal-detalle'));
    modal.show();

    $.ajax({
        url:      API_BASE + 'listar_devoluciones.php',
        method:   'GET',
        data:     { id },
        dataType: 'json',
    })
    .done(function (resp) {
        if (!resp.success) {
            $('#modal-detalle-body').html('<div class="alert alert-danger">' + escHtml(resp.error) + '</div>');
            return;
        }
        renderizarModalDetalle(resp.data);
    })
    .fail(function () {
        $('#modal-detalle-body').html('<div class="alert alert-danger">Error al cargar el detalle.</div>');
    });
}

function renderizarModalDetalle(data) {
    const cab     = data.cabecera;
    const detalle = data.detalle;

    // --- Detalle de productos ---
    let filasDetalle = '';
    if (detalle && detalle.length) {
        detalle.forEach(function (d) {
            const estadoProd = { nuevo: 'Nuevo', usado: 'Usado', fallado: 'Fallado' }[d.estado_producto] || (d.estado_producto || '—');
            filasDetalle += `<tr>
                <td>${escHtml(d.producto_id || '—')}</td>
                <td>${escHtml(d.producto_nombre || '—')}</td>
                <td class="text-center">${escHtml(String(d.cantidad))}</td>
                <td>${TIPOS_LABEL[d.accion] || escHtml(d.accion)}</td>
                <td>${escHtml(estadoProd)}</td>
                <td>${escHtml(d.stock_origen || '—')}</td>
                <td>${escHtml(d.codigo_cambio || '—')}</td>
                <td>${escHtml(d.descripcion_cambio || '—')}</td>
                <td>${escHtml(d.stock_cambio || '—')}</td>
            </tr>`;
        });
    } else {
        filasDetalle = '<tr><td colspan="9" class="text-center text-muted">Sin detalle.</td></tr>';
    }

    // --- Precios ---
    const precioAbonado   = cab.precio_abonado    != null ? '$ ' + formatearMoneda(cab.precio_abonado)    : '—';
    const precioArt       = cab.precio_art_cambio != null ? '$ ' + formatearMoneda(cab.precio_art_cambio) : '—';
    const diferencia      = cab.diferencia_precio != null ? '$ ' + formatearMoneda(cab.diferencia_precio) : '—';

    // --- Sección financiera (sólo si hay algún dato) ---
    const hayFinanciero = cab.precio_abonado != null || cab.precio_art_cambio != null
        || cab.link_pago_mp || cab.nro_operacion_mp;
    const secFinanciero = hayFinanciero ? `
        <div class="col-12 mt-2"><div class="border-top pt-2">
            <small class="text-muted fw-semibold text-uppercase">Datos financieros</small>
        </div></div>
        <div class="col-sm-4">
            <div class="info-label">Precio Abonado</div>
            <div class="info-value">${precioAbonado}</div>
        </div>
        <div class="col-sm-4">
            <div class="info-label">Precio Art. Cambio</div>
            <div class="info-value">${precioArt}</div>
        </div>
        <div class="col-sm-4">
            <div class="info-label">Diferencia</div>
            <div class="info-value">${diferencia}</div>
        </div>
        ${cab.link_pago_mp ? `<div class="col-sm-8">
            <div class="info-label">Link Pago MP</div>
            <div class="info-value"><a href="${escHtml(cab.link_pago_mp)}" target="_blank" rel="noopener">${escHtml(cab.link_pago_mp)}</a></div>
        </div>` : ''}
        ${cab.nro_operacion_mp ? `<div class="col-sm-4">
            <div class="info-label">Nro. Operación MP</div>
            <div class="info-value">${escHtml(cab.nro_operacion_mp)}</div>
        </div>` : ''}
    ` : '';

    $('#modal-detalle-body').html(`
        <div class="row g-3 mb-3">
            <div class="col-sm-6">
                <div class="info-label">N° Seguimiento</div>
                <div class="info-value fw-bold text-primary fs-5">${escHtml(cab.nro_seguimiento || '—')}</div>
            </div>
            <div class="col-sm-6">
                <div class="info-label">Pedido (Vtex)</div>
                <div class="info-value fw-bold">${escHtml(cab.pedido_id)}</div>
            </div>
            <div class="col-sm-6">
                <div class="info-label">Cliente</div>
                <div class="info-value">${escHtml(cab.cliente || '—')}</div>
            </div>
            <div class="col-sm-6">
                <div class="info-label">Factura</div>
                <div class="info-value">${escHtml(cab.factura || '—')}</div>
            </div>
            <div class="col-sm-4">
                <div class="info-label">Tipo</div>
                <div>${TIPOS_LABEL[cab.tipo] || escHtml(cab.tipo)}</div>
            </div>
            <div class="col-sm-4">
                <div class="info-label">Estado</div>
                <div>${ESTADOS_LABEL[cab.estado] || escHtml(cab.estado)}</div>
            </div>
            <div class="col-sm-4">
                <div class="info-label">Motivo</div>
                <div class="info-value">${escHtml(cab.motivo || '—')}</div>
            </div>
            <div class="col-sm-6">
                <div class="info-label">Fecha creación</div>
                <div class="info-value">${formatearFecha(cab.fecha_creacion)}</div>
            </div>
            <div class="col-sm-6">
                <div class="info-label">Fecha resolución</div>
                <div class="info-value">${formatearFecha(cab.fecha_resolucion)}</div>
            </div>
            <div class="col-sm-4">
                <div class="info-label">Usuario</div>
                <div class="info-value">${escHtml(cab.usuario || '—')}</div>
            </div>

            ${cab.observaciones ? `<div class="col-12">
                <div class="info-label">Observaciones</div>
                <div class="info-value">${escHtml(cab.observaciones)}</div>
            </div>` : ''}

            <!-- Resolución -->
            <div class="col-12 mt-1"><div class="border-top pt-2">
                <small class="text-muted fw-semibold text-uppercase">Resolución</small>
            </div></div>
            <div class="col-sm-4">
                <div class="info-label">Nro. Remito</div>
                <div class="info-value">${escHtml(cab.nro_rto || '—')}</div>
            </div>
            <div class="col-sm-4">
                <div class="info-label">NC / Factura Nro.</div>
                <div class="info-value">${escHtml(cab.nro_nc_fact || '—')}</div>
            </div>
            <div class="col-sm-4">
                <div class="info-label">Nro. Ped. Tango</div>
                <div class="info-value">${escHtml(cab.nro_ped_tango || '—')}</div>
            </div>

            ${secFinanciero}
        </div>

        <h6 class="mt-3 mb-2">Productos</h6>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Código</th><th>Producto</th>
                        <th class="text-center">Cant.</th><th>Acción</th>
                        <th>Estado</th><th>Stock origen</th>
                        <th>Cód. cambio</th><th>Desc. cambio</th><th>Stock cambio</th>
                    </tr>
                </thead>
                <tbody>${filasDetalle}</tbody>
            </table>
        </div>
    `);
}

/* ============================================================
   CAMBIAR ESTADO
   ============================================================ */
function cambiarEstado(id, estadoActual) {
    const opciones = {
        pendiente:   'Pendiente',
        en_transito: 'En tránsito',
        recibido:    'Recibido',
        resuelto:    'Resuelto',
    };

    // Construir select HTML para SweetAlert
    let selectHtml = '<select id="swal-estado" class="form-select">';
    Object.entries(opciones).forEach(([val, label]) => {
        const selected = val === estadoActual ? 'selected' : '';
        selectHtml += `<option value="${val}" ${selected}>${label}</option>`;
    });
    selectHtml += '</select>';

    Swal.fire({
        title:            'Cambiar estado',
        html:             `<label class="mb-2">Seleccione el nuevo estado:</label>${selectHtml}`,
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        cancelButtonText:  'Cancelar',
        preConfirm: function () {
            return document.getElementById('swal-estado').value;
        },
    }).then(function (result) {
        if (!result.isConfirmed || result.value === estadoActual) return;

        $.ajax({
            url:         API_BASE + 'actualizar_estado.php',
            method:      'POST',
            contentType: 'application/json',
            data:        JSON.stringify({ id, estado: result.value }),
            dataType:    'json',
        })
        .done(function (resp) {
            if (!resp.success) {
                mostrarAlerta(resp.error || 'Error al actualizar.', 'danger');
                return;
            }
            Swal.fire({ icon: 'success', title: '¡Actualizado!', text: resp.message, timer: 1500, showConfirmButton: false });
            cargarListado();
        })
        .fail(function () {
            mostrarAlerta('Error al conectar con el servidor.', 'danger');
        });
    });
}

/* ============================================================
   UTILIDADES
   ============================================================ */
function resetFormulario() {
    pedidoCargado = null;
    $('#pedido-id').val('');
    $('#pedido-encontrado').hide();
    // Destruir instancias Select2 antes de vaciar el contenedor
    $('#tabla-productos .select-stock-cambio').each(function () {
        if ($(this).data('select2')) { $(this).select2('destroy'); }
    });
    $('#tabla-productos').empty();
    $('#seccion-productos').addClass('disabled-section');
    $('#seccion-paso2').addClass('disabled-section');
    $('#li-sync-hint').hide();
    // Cerrar secciones opcionales si están abiertas
    ['seccion-resolucion', 'seccion-financiero'].forEach(function (id) {
        const el = document.getElementById(id);
        if (el && el.classList.contains('show')) {
            bootstrap.Collapse.getOrCreateInstance(el).hide();
        }
    });
    $('#form-devolucion')[0].reset();
    actualizarPasos(1);
    const pedidoInput = document.getElementById('pedido-id');
    if (pedidoInput) { pedidoInput.focus(); pedidoInput.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
}

function mostrarAlerta(mensaje, tipo) {
    const $zona = $('#alerta-alta');
    $zona.html(`
        <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
            ${escHtml(mensaje)}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `).show();
}

function mostrarAlertaListado(mensaje, tipo) {
    $('#tabla-listado tbody').html(
        `<tr><td colspan="7"><div class="alert alert-${tipo} mb-0">${escHtml(mensaje)}</div></td></tr>`
    );
}

function formatearMoneda(valor) {
    return parseFloat(valor || 0).toLocaleString('es-AR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

function formatearFecha(valor) {    if (!valor || valor === 'null') return '—';
    try {
        const d = new Date(valor);
        if (isNaN(d)) return valor;
        return d.toLocaleDateString('es-AR', { day: '2-digit', month: '2-digit', year: 'numeric' })
             + ' ' + d.toLocaleTimeString('es-AR', { hour: '2-digit', minute: '2-digit' });
    } catch { return valor; }
}

function formatearFechaInput(valor) {
    if (!valor || valor === 'null') return '';
    try {
        const d = new Date(valor);
        if (isNaN(d)) return '';
        return d.toISOString().substring(0, 10);
    } catch { return ''; }
}

/** Escapa HTML para prevenir XSS al insertar en el DOM como string */
function escHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

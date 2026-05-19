<?php

require_once 'Class/Conexion.php';
require_once 'Class/Pedido.php';
require_once 'Controlador/envio_remitos_once.php';

$pedidos = new Pedido();

// Solo sincronizar remitos en la primera carga
remitos_buscar_once();

$hoy         = date('Y-m-d');
$desde       = isset($_GET['desde'])        ? $_GET['desde']        : $hoy;
$hasta       = isset($_GET['hasta'])        ? $_GET['hasta']        : $hoy;
$tienda      = isset($_GET['tienda'])       ? $_GET['tienda']       : '';
$warehouse   = isset($_GET['warehouse'])    ? $_GET['warehouse']    : '';
$estado      = isset($_GET['estado'])       ? $_GET['estado']       : '';
$orden       = isset($_GET['orden'])        ? $_GET['orden']        : '';
$metodoEnvio = isset($_GET['metodo_envio']) ? $_GET['metodo_envio'] : '';
$busqueda    = isset($_GET['factura'])      ? $_GET['factura']      : '';

$todosLosWarehouse    = $pedidos->traerWarehouse();
$todosLosMetodosEnvio = $pedidos->traerMetodosEnvio();

// Con el nuevo sistema AJAX, BUSCAR_ACTIVO solo controla si hay params en la URL
// (para que la búsqueda de texto y los botones aparezcan en carga directa via URL)
// El submit del form siempre dispara la carga AJAX desde el JS.
$buscarActivo = isset($_GET['desde']);

?>
<!doctype HTML>
<html lang="es">
<head>
    <title>XL Extralarge - Inicio</title>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="assets/icono.ico" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/ecommerce/assets/css/css.php'; ?>
    <link rel="stylesheet" href="assets/css/helpIndex.css">
    <link rel="stylesheet" href="assets/css/nc_pendientes.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css"
          integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
            integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
</head>

<body>
<div class="container-fluid">

    <div class="alert alert-primary" role="alert" id="menu">

        <!-- ── Cabecera ── -->
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem;margin-bottom:.6rem;">
            <h3 class="mt-0 mb-0"><i class="bi bi-bag-check" style="color:var(--accent)"></i> Estado Pedidos Ecommerce</h3>
            <div class="counter-group">
                <label>Órdenes</label>
                <input type="text" class="form-control form-control-sm" id="cantidad" readonly disabled placeholder="—">
                <label class="ml-2">Artículos</label>
                <input type="text" class="form-control form-control-sm" id="cantidadArticulos" readonly disabled placeholder="—">
            </div>
        </div>

        <!-- ── Formulario de filtros ── -->
        <div class="row" id="renderr" style="margin-left:10px">
            <div class="mt-2">
                <form class="form-inline" id="formFiltros" method="GET" action="">

                    <div style="display:flex;flex-direction:column;">
                        <label style="align-self:flex-start;">Desde:</label>
                        <input type="date" class="form-control form-control-sm" name="desde" value="<?= htmlspecialchars($desde) ?>">
                    </div>
                    <div style="display:flex;flex-direction:column;margin-left:0.5rem">
                        <label style="align-self:flex-start;">Hasta:</label>
                        <input type="date" class="form-control form-control-sm" name="hasta" value="<?= htmlspecialchars($hasta) ?>">
                    </div>
                    <div style="display:flex;flex-direction:column;margin-left:0.5rem">
                        <label style="align-self:flex-start;">Tienda:</label>
                        <select class="form-control form-control-sm" name="tienda">
                            <option value="" <?= $tienda === '' ? 'selected' : '' ?>>Todas</option>
                            <option value="FRAVEGA"  <?= $tienda === 'FRAVEGA'  ? 'selected' : '' ?>>FRAVEGA</option>
                            <option value="ICBC"     <?= $tienda === 'ICBC'     ? 'selected' : '' ?>>ICBC</option>
                            <option value="VTEX"     <?= $tienda === 'VTEX'     ? 'selected' : '' ?>>VTEX</option>
                            <option value="ML"       <?= $tienda === 'ML'       ? 'selected' : '' ?>>MERCADO LIBRE</option>
                        </select>
                    </div>
                    <div style="display:flex;flex-direction:column;margin-left:0.5rem">
                        <label style="align-self:flex-start;">Estado:</label>
                        <select class="form-control form-control-sm" name="estado">
                            <option value="" <?= $estado === '' ? 'selected' : '' ?>>Todos</option>
                            <?php foreach (['CANCELADO','PREPARADO','SIN_CONTROLAR','FALTANTE','FACTURADO','DESPACHADO','ENTREGADO','SIN_DESPACHAR'] as $opt): ?>
                                <option value="<?= $opt ?>" <?= $estado === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="display:flex;flex-direction:column;margin-left:0.5rem">
                        <label style="align-self:flex-start;">Origen:</label>
                        <select class="form-control form-control-sm" name="warehouse">
                            <option value="">Todos</option>
                            <?php foreach ($todosLosWarehouse as $wh): ?>
                                <option value="<?= htmlspecialchars($wh[0]->WAREHOUSE) ?>"
                                    <?= $warehouse === $wh[0]->WAREHOUSE ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($wh[0]->WAREHOUSE) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="display:flex;flex-direction:column;margin-left:0.5rem">
                        <label style="align-self:flex-start;">Orden:</label>
                        <input class="form-control form-control-sm" type="text"
                               placeholder="Número de orden.." name="orden" value="<?= htmlspecialchars($orden) ?>">
                    </div>
                    <div style="display:flex;flex-direction:column;margin-left:0.5rem">
                        <label style="align-self:flex-start;">Método Envío:</label>
                        <select class="form-control form-control-sm" name="metodo_envio">
                            <option value="">Todos</option>
                            <?php foreach ($todosLosMetodosEnvio as $me): ?>
                                <option value="<?= htmlspecialchars($me[0]->METODO_ENVIO) ?>"
                                    <?= $metodoEnvio === $me[0]->METODO_ENVIO ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($me[0]->METODO_ENVIO) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="ml-2 d-flex align-items-end gap-1">
                        <button type="submit" id="btnBuscar" class="btn btn-primary btn-buscar mt-4">
                            Buscar <i class="bi bi-search"></i>
                        </button>
                        <button type="button" id="btnDescargarDirecto" class="btn btn-descargar-directo mt-4"
                                onclick="descargarDirecto()"
                                title="Descarga el CSV con los filtros seleccionados sin cargar la tabla">
                            <i class="bi bi-file-earmark-arrow-down"></i> Descargar CSV
                        </button>
                    </div>

                    <?php if ($buscarActivo): ?>
                    <label class="ml-2 mt-4">Búsqueda rápida:</label>
                    <input type="text" class="form-control form-control-sm ml-1 mt-4"
                           onkeyup="busquedaRapida()" id="textBox" name="factura"
                           value="<?= htmlspecialchars($busqueda) ?>"
                           placeholder="Sobre cualquier campo..">
                    <?php else: ?>
                    <label class="ml-2 mt-4">Búsqueda rápida:</label>
                    <input type="text" class="form-control form-control-sm ml-1 mt-4"
                           onkeyup="busquedaRapida()" id="textBox" name="factura"
                           value="" placeholder="Sobre cualquier campo..">
                    <?php endif; ?>

                </form>
            </div><!-- /mt-2 -->
        </div><!-- /renderr -->

        <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/ecommerce/assets/js/js.php'; ?>

        <!-- ── Botones de acción ── -->
        <div class="action-bar">
            <button onclick="filterPendientes()"   id="buttonPendientes"><i class="bi bi-clock"></i> Pendientes</button>
            <button onclick="filterCancelados()"   id="buttonCancelados"><i class="bi bi-x-circle"></i> Sin NC</button>
            <button onclick="filterIncompletos()"  id="buttonIncompletos"><i class="bi bi-exclamation-triangle"></i> Incompletos</button>
            <button onclick="iniciarExportacion()" id="buttonExportar"><i class="bi bi-file-earmark-spreadsheet"></i> Exportar</button>
            <button onclick="$('#modalAyuda').modal('show')" class="btn btn-outline-secondary btn-sm" style="height:34px;border-radius:6px;font-size:.8rem;">
                <i class="fas fa-question-circle"></i> Ayuda
            </button>
        </div>

        <!-- ── Barra de progreso ── -->
        <div id="progressBar">
            <div class="progress">
                <div id="progressBarInner" class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                     role="progressbar" style="width:0%"></div>
            </div>
            <small id="progressText"></small>
        </div>

        <!-- ── Spinner de carga de tabla ── -->
        <div id="loadingTable">
            <div class="spinner-border text-primary" role="status"></div>
            <p>Cargando registros… <span id="loadedCount">0</span> cargados</p>
        </div>

        <!-- ── Tabla principal ── -->
        <div style="width:100%;">
            <table class="table table-hover" id="id_tabla">
                <thead id="tablaPedidosH">
                    <tr>
                        <th style="width:2%"   class="headerTitle">TIENDA</th>
                        <th style="width:9%"   class="headerTitle">NRO<BR>ORDEN</th>
                        <th style="width:6%"   class="headerTitle">FECHA<BR>PEDIDO</th>
                        <th style="width:6%"   class="headerTitle">HORA<BR>PEDIDO</th>
                        <th style="width:5%"   class="headerTitle">PEDIDO</th>
                        <th style="width:12%"  class="headerTitle">NOMBRE</th>
                        <th style="width:8%"   class="headerTitle">COD<BR>ARTICULO</th>
                        <th style="width:8%"   class="headerTitle">DESC<BR>ARTICULO</th>
                        <th style="width:4%;text-align:left;padding-left:0px" class="headerTitle">CANT</th>
                        <th style="width:7%"   class="headerTitle">IMPORTE</th>
                        <th style="width:5.5%" class="headerTitle">NRO<BR>FACT</th>
                        <th style="width:5%"   class="headerTitle">DEPOSITO</th>
                        <th style="width:5%"   class="headerTitle">METODO<BR>ENVIO</th>
                        <th style="width:5%"   class="headerTitle">TIENDA</th>
                        <th style="width:1%;color:white;" class="headerTitle noExl">
                            <i class="bi-cart-check-fill" data-toggle="tooltip" title="Preparación" style="color:#FFF;font-size:18px;padding-top:0.4rem;"></i>
                        </th>
                        <th style="width:1%;color:white;" class="headerTitle noExl">
                            <i class="bi bi-file-earmark-text-fill" data-toggle="tooltip" title="Facturación" style="color:#FFF;font-size:18px;padding-top:0.4rem;"></i>
                        </th>
                        <th style="width:1%;color:white;" class="headerTitle noExl">
                            <i class="bi bi-clipboard2-check-fill" data-toggle="tooltip" title="Control" style="color:#FFF;font-size:18px;padding-top:0.4rem;"></i>
                        </th>
                        <th style="width:1%;color:white;" class="headerTitle noExl">
                            <i class="fas fa-truck" data-toggle="tooltip" title="Despacho" style="color:#FFF;font-size:18px;padding-top:0.4rem;"></i>
                        </th>
                        <th style="width:1%;color:white;" class="headerTitle noExl">
                            <i class="fas fa-store" data-toggle="tooltip" title="Recibido" style="color:#FFF;font-size:18px;padding-top:0.4rem;"></i>
                        </th>
                        <th style="width:1%;color:white;" class="headerTitle noExl">
                            <i class="bi bi-box-seam-fill" data-toggle="tooltip" title="Entrega" style="color:#FFF;font-size:18px;padding-top:0.4rem;"></i>
                        </th>
                        <th style="width:1%;color:white;" class="headerTitle noExl">
                            <i class="bi bi-cart-dash-fill" data-toggle="tooltip" title="Incompleto" style="color:#FFF;font-size:18px;padding-top:0.4rem;"></i>
                        </th>
                    </tr>
                </thead>
                <tbody id="table">
                    <!-- Las filas se insertan dinámicamente via AJAX -->
                </tbody>
            </table>

            <!-- Mensaje cuando no hay resultados -->
            <div id="sinResultados" style="display:none;text-align:center;padding:2rem;color:#888;">
                <i class="bi bi-search" style="font-size:2rem;"></i>
                <p class="mt-2">No se encontraron registros para los filtros aplicados.</p>
            </div>
        </div>

    </div><!-- /alert -->
</div><!-- /container-fluid -->


<!-- ══════════════════════════════════════════════════════════════════════════ -->
<!-- Modal de exportación                                                       -->
<!-- ══════════════════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalExportando" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-file-earmark-excel-fill"></i> Generando exportación…
                </h5>
            </div>
            <div class="modal-body text-center" style="padding:1.5rem 2rem;">
                <div class="spinner-border mb-3" style="width:2.5rem;height:2.5rem;color:var(--success);" role="status"></div>
                <div class="progress mb-2">
                    <div id="exportProgressBar" class="progress-bar bg-success"
                         role="progressbar" style="width:100%"></div>
                </div>
                <p id="exportProgressText" class="mb-0">Generando el archivo… La descarga aparecerá en la barra del navegador.</p>
            </div>
            <div class="modal-footer" style="border-top:1px solid var(--border);padding:.6rem 1rem;">
                <button type="button" id="btnCancelarExport" class="btn btn-outline-secondary btn-sm" style="border-radius:6px;" onclick="cancelarExportacion()">
                    Cerrar (8)
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Overlay de descarga -->
<div id="exportOverlay" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;
     z-index:99999;background:rgba(255,255,255,0.88);flex-direction:column;
     align-items:center;justify-content:center;">
    <div class="spinner-border text-success" style="width:3.5rem;height:3.5rem;" role="status"></div>
    <p style="margin-top:1.2rem;font-size:1.2rem;font-weight:600;color:#333;">
        <i class="bi bi-file-earmark-excel-fill" style="color:#28a745;"></i> Preparando descarga…
    </p>
</div>

<script src="assets/bootstrap/popper.min.js"></script>
<script src="assets/bootstrap/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert@1.1.3/dist/sweetalert.min.js"></script>

<?php require_once 'modals/ayuda.php'; ?>
<?php require_once 'modals/nc_pendientes.php'; ?>

<!-- ══════════════════════════════════════════════════════════════════════════ -->
<!-- JavaScript principal                                                       -->
<!-- ══════════════════════════════════════════════════════════════════════════ -->
<script>
// ── Estado global ─────────────────────────────────────────────────────────────
let   cargando             = false;
let   cancelarCarga        = false;
let   pollingInterval      = null;

// ── Filtros iniciales desde PHP (para pre-cargar si la URL ya tiene parámetros) ─
const filtrosIniciales = {
    desde:        <?= json_encode($desde) ?>,
    hasta:        <?= json_encode($hasta) ?>,
    tienda:       <?= json_encode($tienda) ?>,
    warehouse:    <?= json_encode($warehouse) ?>,
    estado:       <?= json_encode($estado) ?>,
    orden:        <?= json_encode($orden) ?>,
    metodo_envio: <?= json_encode($metodoEnvio) ?>,
    factura:      <?= json_encode($busqueda) ?>,
};

// Filtros que se usan en las llamadas AJAX (se actualizan al hacer submit)
let filtros = Object.assign({}, filtrosIniciales);

// ── Flag: hay búsqueda activa (URL ya tiene params) ───────────────────────────
const BUSCAR_ACTIVO_INICIAL = <?= $buscarActivo ? 'true' : 'false' ?>;

// ══════════════════════════════════════════════════════════════════════════════
// CARGA PROGRESIVA VIA AJAX
// ══════════════════════════════════════════════════════════════════════════════

/**
 * Carga todos los pedidos en chunks de 200, agregando filas al DOM
 * de forma progresiva. El usuario ve los datos aparecer sin paginar.
 */
async function cargarPedidosProgresivo() {
    cargando      = true;
    cancelarCarga = false;

    const tbody     = document.getElementById('table');
    const loading   = document.getElementById('loadingTable');
    const progBar   = document.getElementById('progressBar');
    const progInner = document.getElementById('progressBarInner');
    const progText  = document.getElementById('progressText');
    const sinRes    = document.getElementById('sinResultados');
    const loadedCnt = document.getElementById('loadedCount');

    // Reset
    tbody.innerHTML = '';
    sinRes.style.display  = 'none';
    loading.style.display = 'block';
    progBar.style.display = 'block';
    progInner.style.width = '2%';
    progText.textContent  = 'Consultando la base de datos…';

    let pagina      = 1;
    let totalLoaded = 0;

    // Loop: cada request trae hasta 500 filas para no superar el timeout del proxy
    while (true) {
        if (cancelarCarga) break;

        const params = new URLSearchParams({ ...filtros, pagina });
        let data;

        try {
            const resp = await fetch('getPedidos.php?' + params.toString());
            if (!resp.ok) throw new Error('HTTP ' + resp.status + ' - ' + resp.statusText);
            const texto = await resp.text();
            try {
                data = JSON.parse(texto);
            } catch (parseErr) {
                throw new Error('Respuesta inválida del servidor: ' + texto.substring(0, 300));
            }
        } catch (err) {
            loading.style.display = 'none';
            progBar.style.display = 'none';
            swal('Error al cargar pedidos', err.message, 'error');
            cargando = false;
            return;
        }

        if (data.error) {
            loading.style.display = 'none';
            progBar.style.display = 'none';
            swal('Error del servidor', data.error, 'error');
            cargando = false;
            return;
        }

        if (data.html) {
            tbody.insertAdjacentHTML('beforeend', data.html);
        }

        totalLoaded += data.count || 0;
        loadedCnt.textContent = totalLoaded.toLocaleString();

        // Actualizar barra: 100% al terminar, animada mientras hay más páginas
        if (data.hayMas) {
            const pct = Math.min(90, pagina * 15);
            progInner.style.width = pct + '%';
            progText.textContent  = 'Cargando… ' + totalLoaded.toLocaleString() + ' registros';
        } else {
            progInner.style.width = '100%';
            progText.textContent  = '✓ ' + totalLoaded.toLocaleString() + ' registros cargados.';
        }

        if (!data.hayMas) break;
        pagina++;
    }

    loading.style.display = 'none';

    if (totalLoaded === 0) {
        sinRes.style.display = 'block';
        progBar.style.display = 'none';
    } else {
        setTimeout(() => { progBar.style.display = 'none'; }, 3000);
    }

    $('[data-toggle="tooltip"]').tooltip();
    contar();

    // Aplicar búsqueda rápida si el usuario tenía texto en el campo
    const textBox = document.getElementById('textBox');
    if (textBox && textBox.value.trim() !== '') {
        busquedaRapida();
    }

    cargando = false;
}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

// ── Interceptar submit del formulario ────────────────────────────────────────
document.getElementById('formFiltros').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const params   = new URLSearchParams(formData).toString();

    // Actualizar URL sin recargar
    window.history.pushState({}, '', '?' + params);

    // Actualizar objeto filtros
    filtros = {
        desde:        formData.get('desde')        || '',
        hasta:        formData.get('hasta')        || '',
        tienda:       formData.get('tienda')       || '',
        warehouse:    formData.get('warehouse')    || '',
        estado:       formData.get('estado')       || '',
        orden:        formData.get('orden')        || '',
        metodo_envio: formData.get('metodo_envio') || '',
        factura:      formData.get('factura')      || '',
    };

    cargarPedidosProgresivo();
});

// ── Arrancar carga si la URL ya tiene parámetros (ej: recarga o link compartido) ─
$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();

    if (BUSCAR_ACTIVO_INICIAL) {
        // Hay params en la URL → cargar tabla automáticamente
        filtros = Object.assign({}, filtrosIniciales);
        cargarPedidosProgresivo();
    }
});

// ══════════════════════════════════════════════════════════════════════════════
// CONTADORES
// ══════════════════════════════════════════════════════════════════════════════
const contar = () => {
    const trFiltrados = $('#id_tabla tbody tr:visible');
    const pedidosUnicos = new Set();
    let totalArticulos  = 0;

    trFiltrados.each(function () {
        const numeroPedido = $(this).find('td').eq(4).text().trim();
        const codArticulo  = $(this).find('td').eq(6).text().trim();
        const cantidad     = parseFloat($(this).find('td').eq(8).text().trim()) || 0;
        pedidosUnicos.add(numeroPedido);
        if (codArticulo !== '***COSTO ENVIO') totalArticulos += cantidad;
    });

    document.getElementById('cantidad').value          = pedidosUnicos.size.toLocaleString();
    document.getElementById('cantidadArticulos').value = totalArticulos.toLocaleString();
};

// ══════════════════════════════════════════════════════════════════════════════
// BÚSQUEDA RÁPIDA (sobre DOM ya cargado)
// ══════════════════════════════════════════════════════════════════════════════
function busquedaRapida() {
    const input  = document.getElementById('textBox');
    if (!input) return;
    const filter = input.value.toUpperCase();
    const tbody  = document.getElementById('table');
    const rows   = tbody.getElementsByTagName('tr');

    for (let i = 0; i < rows.length; i++) {
        const cells   = rows[i].getElementsByTagName('td');
        let   visible = false;
        for (let j = 0; j < cells.length; j++) {
            if (cells[j] && cells[j].innerHTML.toUpperCase().indexOf(filter) > -1) {
                visible = true;
                break;
            }
        }
        rows[i].style.display = visible ? '' : 'none';
    }
    contar();
}

function pulsar(e) {
    return e.keyCode !== 13;
}

// ══════════════════════════════════════════════════════════════════════════════
// FILTROS RÁPIDOS (Pendientes / Sin NC / Incompletos)
// ══════════════════════════════════════════════════════════════════════════════
function filterPendientes() {
    const rows = document.getElementById('table').getElementsByTagName('tr');
    for (let r of rows) {
        const canceladoCell = r.querySelector('td#cancelado');
        const hayPendiente  = canceladoCell && canceladoCell.querySelector('.pendiente');
        r.style.display = hayPendiente ? '' : 'none';
    }
    contar();
}

function filterCancelados() {
    const rows = document.getElementById('table').getElementsByTagName('tr');
    for (let r of rows) {
        const canceladoCell = r.querySelector('td#cancelado');
        const hayCancelado  = canceladoCell && canceladoCell.querySelector('.cancelado');
        r.style.display = hayCancelado ? '' : 'none';
    }
    contar();
}

function filterIncompletos() {
    const rows = document.getElementById('table').getElementsByTagName('tr');
    for (let r of rows) {
        const incompletoCell = r.querySelector('td#incompleto');
        const hayIncompleto  = incompletoCell && incompletoCell.querySelector('.incompleto');
        r.style.display = hayIncompleto ? '' : 'none';
    }
    contar();
}

// ══════════════════════════════════════════════════════════════════════════════
// EXPORTACIÓN  (descarga sincrónica en streaming — sin proceso background)
// ══════════════════════════════════════════════════════════════════════════════

function _exportarConParams(params) {
    const overlay = document.getElementById('exportOverlay');
    overlay.style.display = 'flex';
    window.location.href = 'exportarPedidos.php?' + new URLSearchParams(params).toString();
    // El navegador no navega cuando el servidor responde Content-Disposition: attachment.
    // Ocultamos el overlay luego de unos segundos (el browser ya está manejando la descarga).
    setTimeout(() => { overlay.style.display = 'none'; }, 8000);
}

/** Exporta con los filtros activos de la última búsqueda. */
function iniciarExportacion() {
    _exportarConParams(filtros);
}

/** Exporta leyendo el formulario directamente, sin cargar la tabla. */
function descargarDirecto() {
    const fd = new FormData(document.getElementById('formFiltros'));
    _exportarConParams({
        desde:        fd.get('desde')        || '',
        hasta:        fd.get('hasta')        || '',
        tienda:       fd.get('tienda')       || '',
        warehouse:    fd.get('warehouse')    || '',
        estado:       fd.get('estado')       || '',
        orden:        fd.get('orden')        || '',
        metodo_envio: fd.get('metodo_envio') || '',
        factura:      fd.get('factura')      || '',
    });
}
</script>

</body>
</html>
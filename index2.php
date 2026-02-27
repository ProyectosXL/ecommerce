<?php
require_once 'Class/Conexion.php';
require_once 'Class/Pedido.php';
require_once 'Controlador/envio_remitos_once.php';
require_once 'Controlador/nuevo_ml.php';
require_once 'Controlador/nc_pend.php';
$pedidos = new Pedido();

// Solo sincronizar remitos y ML en la primera página para no sumar tiempo al paginar
if (!isset($_GET['pagina']) || intval($_GET['pagina']) <= 1) {
    remitos_buscar_once();
    new_ml();
}

if (!isset($_GET['desde'])) {
    nc_pendientes();
}

$hoy = date("Y-m-d");
$tienda = (!isset($_GET['tienda'])) ? '%' : $_GET['tienda'] . '%';
$warehouse = (!isset($_GET['warehouse'])) ? '%' : $_GET['warehouse'] . '%';
$desde = (!isset($_GET['desde'])) ? $hoy : $_GET['desde'];
$hasta = (!isset($_GET['hasta'])) ? $hoy : $_GET['hasta'];
$estado = (isset($_GET['estado'])) ? $_GET['estado'] : null;
$orden = (!isset($_GET['orden'])) ? '%' : $_GET['orden'] . '%';
$pagina = (isset($_GET['pagina']) && intval($_GET['pagina']) > 0) ? intval($_GET['pagina']) : 1;
$metodoEnvio = (isset($_GET['metodo_envio']) && trim($_GET['metodo_envio']) !== '') ? trim($_GET['metodo_envio']) : '';
$todosLosWarehouse = $pedidos->traerWarehouse();
$todosLosMetodosEnvio = $pedidos->traerMetodosEnvio();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XL Extralarge - Estado Pedidos</title>
    <link rel="shortcut icon" href="assets/icono.ico" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/index2.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid px-2">
        <!-- Header -->
        <div class="header-section">
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 me-4">
                        <i class="bi bi-handbag me-2"></i>Estado Pedidos Ecommerce
                    </h4>
                </div>

                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <div class="stats-group">
                        <label class="form-label mb-0 me-1">Órdenes:</label>
                        <input type="text" class="form-control form-control-sm stats-input" id="cantidad" readonly>
                    </div>
                    <div class="stats-group">
                        <label class="form-label mb-0 me-1">Artículos:</label>
                        <input type="text" class="form-control form-control-sm stats-input" id="cantidadArticulos"
                            readonly>
                    </div>
                </div>
            </div>

            <!-- Filters Form -->
            <form class="filters-form" method="GET" action="">
                <div class="row g-2 align-items-end">
                    <div class="col-md-2 col-sm-6">
                        <label class="form-label">Desde:</label>
                        <input type="date" class="form-control form-control-sm" name="desde" value="<?= $desde ?>">
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <label class="form-label">Hasta:</label>
                        <input type="date" class="form-control form-control-sm" name="hasta" value="<?= $hasta ?>">
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <label class="form-label">Tienda:</label>
                        <select class="form-select form-select-sm" name="tienda">
                            <option selected></option>
                            <option value="ICBC">ICBC</option>
                            <option value="VTEX">VTEX</option>
                            <option value="ML">MERCADO LIBRE</option>
                        </select>
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <label class="form-label">Estado:</label>
                        <select class="form-select form-select-sm" name="estado">
                            <option selected></option>
                            <option value="CANCELADO" <?= (isset($_GET['estado']) && $_GET['estado'] == 'CANCELADO') ? 'selected' : '' ?>>CANCELADO</option>
                            <option value="PREPARADO" <?= (isset($_GET['estado']) && $_GET['estado'] == 'PREPARADO') ? 'selected' : '' ?>>PREPARADO</option>
                            <option value="SIN_CONTROLAR" <?= (isset($_GET['estado']) && $_GET['estado'] == 'SIN_CONTROLAR') ? 'selected' : '' ?>>SIN CONTROLAR</option>
                            <option value="FALTANTE" <?= (isset($_GET['estado']) && $_GET['estado'] == 'FALTANTE') ? 'selected' : '' ?>>FALTANTE</option>
                            <option value="FACTURADO" <?= (isset($_GET['estado']) && $_GET['estado'] == 'FACTURADO') ? 'selected' : '' ?>>FACTURADO</option>
                            <option value="DESPACHADO" <?= (isset($_GET['estado']) && $_GET['estado'] == 'DESPACHADO') ? 'selected' : '' ?>>DESPACHADO</option>
                            <option value="ENTREGADO" <?= (isset($_GET['estado']) && $_GET['estado'] == 'ENTREGADO') ? 'selected' : '' ?>>ENTREGADO</option>
                            <option value="SIN_DESPACHAR" <?= (isset($_GET['estado']) && $_GET['estado'] == 'SIN_DESPACHAR') ? 'selected' : '' ?>>SIN DESPACHAR</option>
                        </select>
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <label class="form-label">Origen:</label>
                        <select class="form-select form-select-sm" name="warehouse">
                            <option selected></option>
                            <?php foreach ($todosLosWarehouse as $warehouse => $key): ?>
                                <option value="<?= $key[0]->WAREHOUSE ?>"><?= $key[0]->WAREHOUSE ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <label class="form-label">Orden:</label>
                        <input class="form-control form-control-sm" type="text" placeholder="Número de orden.."
                            name="orden">
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <label class="form-label">Método Envío:</label>
                        <select class="form-select form-select-sm" name="metodo_envio">
                            <option value="">Todos</option>
                            <?php foreach ($todosLosMetodosEnvio as $me): ?>
                                <option value="<?= $me[0]->METODO_ENVIO ?>" <?= $metodoEnvio === $me[0]->METODO_ENVIO ? 'selected' : '' ?>>
                                    <?= $me[0]->METODO_ENVIO ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12 d-flex flex-wrap gap-2 align-items-center">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-search me-1"></i>Buscar
                        </button>

                        <div id="boxLoading" class="spinner-border spinner-border-sm d-none" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>

                        <?php if (isset($_GET['desde'])): ?>
                            <div class="search-group">
                                <label class="form-label mb-0 me-1">Búsqueda:</label>
                                <input type="text" class="form-control form-control-sm search-input"
                                    onkeyup="busquedaRapida()" id="textBox" placeholder="Sobre cualquier campo.." autofocus>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </form>

            <?php if (isset($_GET['desde'])): ?>
                <!-- Action Buttons -->
                <div class="action-buttons mt-3">
                    <button onclick="filterPendientes()" class="btn btn-outline-warning btn-sm">
                        <i class="bi bi-clock me-1"></i>Pendientes
                    </button>
                    <button onclick="filterCancelados()" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-x-circle me-1"></i>Sin NC
                    </button>
                    <button onclick="filterIncompletos()" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-exclamation-triangle me-1"></i>Incompletos
                    </button>
                    <button onclick="exportar()" class="btn btn-success btn-sm">
                        <i class="bi bi-download me-1"></i>Exportar
                    </button>
                    <button onclick="showHelp()" class="btn btn-info btn-sm">
                        <i class="fas fa-question-circle me-1"></i>Ayuda
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <?php if (isset($_GET['desde'])):
            $busqueda2 = (isset($_GET['factura']) && trim($_GET['factura']) !== '') ? trim($_GET['factura']) : '';

            // Solo la búsqueda de texto libre pide 1000 registros
            // El filtro de método de envío ahora es Global vía Stored Procedure
            $porPagina2 = $busqueda2 ? 1000 : 100;
            $paginaUsada2 = $busqueda2 ? 1 : $pagina;

            $arrayPedidos = $pedidos->traerPedidos($desde, $hasta, $tienda, $warehouse, $estado, $orden, $paginaUsada2, $porPagina2, $metodoEnvio);
            // Filtrar pedidos que no tengan unidades
            $arrayPedidos = array_filter($arrayPedidos, function ($value) {
                return isset($value[0]->CANTIDAD_A_FACTURAR) && $value[0]->CANTIDAD_A_FACTURAR > 0;
            });
            $totalResultados = count($arrayPedidos);
            $pedido_anterior = '';
            ?>

            <!-- Table Container -->
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-sm" id="id_tabla">
                        <thead class="table-header">
                            <tr>
                                <th class="col-tienda">TIENDA</th>
                                <th class="col-orden">NRO<br>ORDEN</th>
                                <th class="col-fecha">FECHA<br>PEDIDO</th>
                                <th class="col-hora">HORA<br>PEDIDO</th>
                                <th class="col-pedido">PEDIDO</th>
                                <th class="col-nombre">NOMBRE</th>
                                <th class="col-cod">COD<br>ARTICULO</th>
                                <th class="col-desc">DESC<br>ARTICULO</th>
                                <th class="col-cant">CANT</th>
                                <th class="col-importe">IMPORTE</th>
                                <th class="col-fact">NRO<br>FACT</th>
                                <th class="col-deposito">DEPOSITO</th>
                                <th class="col-envio">MÉTODO<br>ENVÍO</th>
                                <th class="col-sucursal">TIENDA</th>
                                <th class="col-icon noExl"><i class="bi-cart-check-fill" title="Preparación"></i></th>
                                <th class="col-icon noExl"><i class="bi bi-file-earmark-text-fill" title="Facturación"></i>
                                </th>
                                <th class="col-icon noExl"><i class="bi bi-clipboard2-check-fill" title="Control"></i></th>
                                <th class="col-icon noExl"><i class="fas fa-truck" title="Despacho"></i></th>
                                <th class="col-icon noExl"><i class="fas fa-store" title="Recibido"></i></th>
                                <th class="col-icon noExl"><i class="bi bi-box-seam-fill" title="Entrega"></i></th>
                                <th class="col-icon noExl"><i class="bi bi-cart-dash-fill" title="Estado"></i></th>
                            </tr>
                        </thead>
                        <tbody id="table">
                            <?php foreach ($arrayPedidos as $key => $value):
                                $nuevo_grupo = ($pedido_anterior != $value[0]->NRO_ORDEN_ECOMMERCE);
                                $pedido_anterior = $value[0]->NRO_ORDEN_ECOMMERCE;

                                $row_class = '';
                                if ($value[0]->NRO_COMP == '' && $value[0]->CANCELADO == 0) {
                                    $row_class .= ' row-pending';
                                }
                                if ($nuevo_grupo) {
                                    $row_class .= ' row-group-start';
                                }
                                ?>
                                <tr class="data-row <?= $row_class ?>">
                                    <td><?= $value[0]->ORIGEN ?></td>
                                    <td>
                                        <?php if ($value[0]->ORIGEN == 'VTEX'): ?>
                                            <a href="https://xlshop.myvtex.com/admin/orders/<?= $value[0]->NRO_ORDEN_ECOMMERCE; ?>"
                                                target="_blank" class="order-link">
                                                <?= $value[0]->NRO_ORDEN_ECOMMERCE ?>
                                            </a>
                                        <?php else: ?>
                                            <?= $value[0]->NRO_ORDEN_ECOMMERCE ?>
                                        <?php endif; ?>
                                        <?php if ($nuevo_grupo): ?>
                                            <div class="group-indicator"></div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $value[0]->FECHA_PEDIDO->format("Y-m-d") ?></td>
                                    <td><?= $value[0]->HORA ?></td>
                                    <td><?= $value[0]->NRO_PEDIDO ?></td>
                                    <td class="customer-name" title="<?= $value[0]->LUGAR_ENTREGA ?>">
                                        <?= $value[0]->RAZON_SOCIAL ?>
                                    </td>
                                    <td><?= $value[0]->COD_ARTICULO ?></td>
                                    <td class="product-desc"><?= $value[0]->DESCRIPCION ?></td>
                                    <td class="text-center"><?= $value[0]->CANTIDAD_A_FACTURAR ?></td>
                                    <td class="text-end">$<?= number_format($value[0]->IMPORTE_PAGO, 0, '', '.') ?></td>
                                    <td class="text-center"><?= $value[0]->NRO_COMP ?></td>
                                    <td class="text-center"><?= $value[0]->WAREHOUSE ?></td>
                                    <td class="text-center"><?= $value[0]->METODO_ENVIO ?></td>
                                    <td class="text-center"><?= $value[0]->DESC_SUCURSAL ?></td>

                                    <!-- Status Icons -->
                                    <td class="text-center noExl" id="incompleto">
                                        <?php if ($value[0]->PREPARADO == 1): ?>
                                            <i class="bi bi-cart-check-fill status-prepared"
                                                title="Preparado <?= $value[0]->FECHA_PREPARADO->format('Y-m-d H:i') ?>"></i>
                                        <?php else: ?>
                                            <i class="status-empty"></i>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center noExl" id="cancelado">
                                        <?php if ($value[0]->CANCELADO == 1 && $value[0]->FACTURADO == 1 && !isset($value[0]->NCR)): ?>
                                            <i class="bi bi-clipboard-x-fill status-cancelled-no-nc cancelado"
                                                title="Pedido cancelado sin NC"></i>
                                        <?php elseif (isset($value[0]->NCR)): ?>
                                            <i class="bi bi-clipboard-check-fill status-cancelled-with-nc"
                                                title="Pedido cancelado NCR <?= $value[0]->NCR ?>"></i>
                                        <?php elseif ($value[0]->FACTURADO == 1): ?>
                                            <i class="bi bi-file-earmark-text-fill status-invoiced"
                                                title="Facturado <?= $value[0]->FECHA_FACTURADO->format('Y-m-d H:i') ?>"></i>
                                        <?php else: ?>
                                            <i class="status-empty status-pending pendiente"></i>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center noExl">
                                        <?php if ($value[0]->CONTROLADO == 1): ?>
                                            <i class="bi bi-clipboard2-check-fill status-controlled"
                                                title="Controlado <?= $value[0]->FECHA_CONTROLADO->format('Y-m-d') ?>"></i>
                                        <?php else: ?>
                                            <i class="status-empty"></i>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center noExl">
                                        <?php if ($value[0]->DESPACHADO == 1): ?>
                                            <i class="fas fa-truck status-dispatched"
                                                title="Despachado <?= $value[0]->FECHA_DESPACHO->format('Y-m-d') ?>"></i>
                                        <?php else: ?>
                                            <i class="status-empty"></i>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center noExl">
                                        <?php if ($value[0]->RECIBIDO_TIENDA == 1): ?>
                                            <i class="fas fa-store status-received"
                                                title="Recibido Tienda <?= $value[0]->FECHA_RECIBIDO_TIENDA->format('Y-m-d') ?>"></i>
                                        <?php else: ?>
                                            <i class="status-empty"></i>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center noExl">
                                        <?php if ($value[0]->ENTREGADO == 1): ?>
                                            <i class="bi bi-box-seam-fill status-delivered"
                                                title="Entregado <?= $value[0]->FECHA_ENTREGADO->format('Y-m-d') ?>"></i>
                                        <?php else: ?>
                                            <i class="status-empty"></i>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center noExl" id="incompleto">
                                        <?php if ($value[0]->CANCELADO == 1): ?>
                                            <i class="bi bi-cart-x-fill status-cancelled" title="Cancelado"></i>
                                        <?php elseif (isset($value[0]->FALTANTE) && $value[0]->FALTANTE == 1): ?>
                                            <i class="bi bi-cart-dash-fill status-incomplete incompleto"
                                                title="Pedido incompleto"></i>
                                        <?php else: ?>
                                            <i class="status-empty"></i>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php
            // Controles de paginación
            $queryParams = $_GET;
            unset($queryParams['pagina']);
            $urlBase = '?' . http_build_query($queryParams);

            // Solo mostrar paginación si NO hay búsqueda de texto activa
            if (!$busqueda2):
                ?>
                <div
                    style="display:flex; align-items:center; justify-content:center; gap:12px; padding:16px 0; margin-bottom:20px;">
                    <?php if ($pagina > 1): ?>
                        <a href="<?= $urlBase ?>&pagina=<?= $pagina - 1 ?>" onclick="mostrarSpinner()"
                            class="btn btn-secondary btn-sm">
                            &laquo; Anterior
                        </a>
                    <?php endif; ?>
                    <span style="font-weight:bold;">Página <?= $pagina ?> &nbsp;|&nbsp; <?= $totalResultados ?> registros en
                        esta página</span>
                    <?php if ($totalResultados >= 100): ?>
                        <a href="<?= $urlBase ?>&pagina=<?= $pagina + 1 ?>" onclick="mostrarSpinner()"
                            class="btn btn-primary btn-sm">
                            Siguiente &raquo;
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div style="text-align:center; padding: 10px 0; margin-bottom:20px; color:#555;">
                    <small><i class="bi bi-search"></i> Búsqueda de "<strong><?= htmlspecialchars($busqueda2) ?></strong>"
                        &mdash; <?= $totalResultados ?> resultado(s) encontrado(s)</small>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>

    <!-- jQuery (required for table2excel and AJAX) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Table2Excel for export functionality -->
    <script src="https://cdn.jsdelivr.net/npm/table2excel@1.0.4/dist/table2excel.min.js"></script>
    <!-- SweetAlert for notifications -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/index2.js"></script>
    <script>
        const mostrarSpinner = () => {
            document.getElementById("boxLoading").classList.remove("d-none");
        }

        const contar2 = () => {
            let trFiltrados = $('#id_tabla tbody tr:visible');
            let pedidosUnicos = new Set();
            let totalArticulos = 0;

            trFiltrados.each(function () {
                let numeroPedido = $(this).find('td').eq(4).text().trim();
                let codArticulo = $(this).find('td').eq(6).text().trim();
                let cantidad = parseFloat($(this).find('td').eq(8).text().trim()) || 0;

                pedidosUnicos.add(numeroPedido);

                if (codArticulo !== '***COSTO ENVIO') {
                    totalArticulos += cantidad;
                }
            });

            document.getElementById('cantidad').value = pedidosUnicos.size.toLocaleString();
            document.getElementById('cantidadArticulos').value = totalArticulos.toLocaleString();
        }

        $(document).ready(function () {
            contar2();
        });
    </script>

    <?php require_once 'modals/ayuda.php'; ?>
</body>

</html>
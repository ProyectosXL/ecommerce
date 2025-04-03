
<?php

require_once 'Class/Conexion.php';
require_once 'Class/Pedido.php';
$pedidos = new Pedido();

$sucursales = $pedidos->traerWarehouse();

$sucursal = '2';
$articulos = $pedidos->buscarStockArticulo($sucursal);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguimiento de Pedidos E-commerce</title>
    <link rel="shortcut icon" href="assets/icono.ico" />
    <!-- Primero jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Luego Select2 CSS y JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- El resto de tus dependencias -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style/consultaPedido.css">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>


  
</head>
<body>
    <div class="container py-4">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-shopping-cart me-2"></i>
                    Seguimiento de Pedidos E-commerce
                </h4>
            </div>
            <div class="card-body">
                <!-- Formulario de búsqueda -->
                <form method="POST" class="search-container mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-3 mb-3 mb-md-0">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-calendar"></i>
                                </span>
                                <input type="date" name="desde" class="form-control" 
                                    value="<?php echo isset($_POST['desde']) ? $_POST['desde'] : date('Y-m-d', strtotime('-30 days')); ?>"
                                    max="<?php echo date('Y-m-d'); ?>"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-calendar"></i>
                                </span>
                                <input type="date" name="hasta" class="form-control" 
                                    value="<?php echo isset($_POST['hasta']) ? $_POST['hasta'] : date('Y-m-d'); ?>"
                                    max="<?php echo date('Y-m-d'); ?>"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" name="numero" class="form-control" 
                                    placeholder="Ingrese número de Orden, Pedido o Factura" 
                                    value="<?php echo isset($_POST['numero']) ? htmlspecialchars($_POST['numero']) : ''; ?>"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>Buscar
                            </button>
                        </div>
                        <div id="spinner" class="spinner-wrapper spinner-hidden">
                            <div class="spinner-dots">
                                <div></div>
                                <div></div>
                                <div></div>
                                <div></div>
                            </div>
                        </div>
                    </div>
                </form>

                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['numero'])) {
                    $numero = trim($_POST['numero']);
                    $desde = isset($_POST['desde']) ? $_POST['desde'] : date('Y-m-d', strtotime('-30 days'));
                    $hasta = isset($_POST['hasta']) ? $_POST['hasta'] : date('Y-m-d');
                    
                    $resultado = $pedidos->buscarPedido($desde, $hasta, $numero);
                    
                    if ($resultado && !empty($resultado)) {
                        foreach($resultado as $row) {
                            $pedido = $row[0]; // Accedemos al objeto dentro del array
                            $detalleReclamo = $pedidos->listarReclamoDetalle($pedido->NRO_PEDIDO);

                ?>
                            <!-- Información del Pedido -->
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="info-label">Fecha y Hora</div>
                                            <div class="info-value" id= "fechaHora">
                                                <?php 
                                                echo $pedido->FECHA_PEDIDO instanceof DateTime ? 
                                                    $pedido->FECHA_PEDIDO->format('d/m/Y') : date('d/m/Y', strtotime($pedido->FECHA_PEDIDO));
                                                echo ' ' . $pedido->HORA;
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Marketplace</div>
                                            <div class="info-value"><?php echo $pedido->MARKETPLACE; ?></div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-label">Nro. Pedido</div>
                                            <div class="info-value d-flex align-items-center" id="nroPedido">
                                                <?php echo $pedido->NRO_PEDIDO; ?>
                                                <?php if ($pedido->CANCELADO == 1): 
                                                    $tooltipText = "Pedido Cancelado";
                                                    if (isset($pedido->NCR) && !empty($pedido->NCR)) {
                                                        $tooltipText .= " - NCR " . $pedido->NCR;
                                                    }
                                                ?>
                                                    <i class="bi bi-x-circle-fill ms-2 text-danger icon-state" 
                                                    data-bs-toggle="tooltip" 
                                                    title="<?php echo $tooltipText; ?>">
                                                    </i>
                                                <?php endif; ?>
                                                <?php if ($pedido->INCOMPLETO == 1): ?>
                                                    <i class="fas fa-exclamation-triangle ms-2 text-warning icon-state" data-bs-toggle="tooltip" title="Pedido Incompleto"></i>
                                                    <!-- <button class="btn btn-sm btn-outline-warning ms-2" data-bs-toggle="modal" data-bs-target="#historialModal">
                                                        <i class="fas fa-history"></i> Ver Historial
                                                    </button> -->
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-label">Nro. Orden</div>
                                            <div class="info-value" id="nroOrden"><?php echo $pedido->NRO_ORDEN; ?></div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-label">Nro. Factura</div>
                                            <div class="info-value"><?php echo $pedido->FACTURA; ?></div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-label">Cliente</div>
                                            <div class="info-value" id="cliente"><?php echo $pedido->CLIENTE; ?></div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-label">Dirección de Entrega</div>
                                            <div class="info-value"><?php echo $pedido->DIRECCION_ENTREGA; ?></div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-label">Lugar de Entrega</div>
                                            <div class="info-value"><?php echo $pedido->LUGAR_ENTREGA; ?></div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-label">Prepara</div>
                                            <div class="info-value" id="prepara"><?php echo $pedido->PREPARA; ?></div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-label">Método de Envío</div>
                                            <div class="info-value"><?php echo $pedido->METODO_ENVIO; ?></div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-label">Sucursal Entrega</div>
                                            <div class="info-value"><?php echo $pedido->SUCURSAL_ENTREGA; ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Timeline de Estados -->
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-4">Estado del Pedido</h5>
                                    <div class="timeline">
                                        <div class="row timeline-row">
                                            <div class="col timeline-step">
                                                <div class="timeline-icon <?php echo $pedido->SINCRONIZADO ? 'active' : ''; ?>">
                                                    <i class="fas fa-sync-alt icon"></i>
                                                </div>
                                                <div>Sincronizado</div>
                                                <div class="timeline-date">
                                                    <?php 
                                                    echo $pedido->FECHA_SINCRONIZADO ? 
                                                        ($pedido->FECHA_SINCRONIZADO instanceof DateTime ? 
                                                            $pedido->FECHA_SINCRONIZADO->format('d/m/Y H:i') : 
                                                            date('d/m/Y H:i', strtotime($pedido->FECHA_SINCRONIZADO))) : 
                                                        'Pendiente'; 
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col timeline-step">
                                                <div class="timeline-icon <?php echo $pedido->FACTURADO ? 'active' : ''; ?>">
                                                    <i class="fas fa-file-invoice icon"></i>
                                                </div>
                                                <div>Facturado</div>
                                                <div class="timeline-date">
                                                    <?php 
                                                    echo $pedido->FECHA_FACTURADO ? 
                                                        ($pedido->FECHA_FACTURADO instanceof DateTime ? 
                                                            $pedido->FECHA_FACTURADO->format('d/m/Y H:i') : 
                                                            date('d/m/Y H:i', strtotime($pedido->FECHA_FACTURADO))) : 
                                                        'Pendiente'; 
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col timeline-step">
                                                <div class="timeline-icon <?php echo $pedido->CONTROLADO ? 'active' : ''; ?>">
                                                    <i class="fas fa-clipboard-check icon"></i>
                                                </div>
                                                <div>Controlado</div>
                                                <div class="timeline-date">
                                                    <?php 
                                                    echo $pedido->FECHA_CONTROLADO ? 
                                                        ($pedido->FECHA_CONTROLADO instanceof DateTime ? 
                                                            $pedido->FECHA_CONTROLADO->format('d/m/Y H:i') : 
                                                            date('d/m/Y H:i', strtotime($pedido->FECHA_CONTROLADO))) : 
                                                        'Pendiente'; 
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col timeline-step">
                                                <div class="timeline-icon <?php echo $pedido->DESPACHADO ? 'active' : ''; ?>">
                                                    <i class="fas fa-truck icon"></i>
                                                </div>
                                                <div>Despachado</div>
                                                <div class="timeline-date">
                                                    <?php 
                                                    echo $pedido->FECHA_DESPACHADO ? 
                                                        ($pedido->FECHA_DESPACHADO instanceof DateTime ? 
                                                            $pedido->FECHA_DESPACHADO->format('d/m/Y H:i') : 
                                                            date('d/m/Y H:i', strtotime($pedido->FECHA_DESPACHADO))) : 
                                                        'Pendiente'; 
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col timeline-step">
                                                <div class="timeline-icon <?php echo $pedido->ENTREGADO ? 'active' : ''; ?>">
                                                    <i class="fas fa-check icon"></i>
                                                </div>
                                                <div>Entregado</div>
                                                <div class="timeline-date">
                                                    <?php 
                                                    echo $pedido->FECHA_ENTREGADO ? 
                                                        ($pedido->FECHA_ENTREGADO instanceof DateTime ? 
                                                            $pedido->FECHA_ENTREGADO->format('d/m/Y H:i') : 
                                                            date('d/m/Y H:i', strtotime($pedido->FECHA_ENTREGADO))) : 
                                                        'Pendiente'; 
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Después del card del timeline, agregar: -->
                            <div class="card mt-4">
                                <div class="card-body">
                                    <h5 class="card-title mb-4">Detalle del Pedido</h5>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 35%">Producto</th>
                                                    <th style="width: 20%" class="text-end">Precio</th>
                                                    <th style="width: 10%" class="text-end">Cant.</th>
                                                    <th style="width: 20%" class="text-end">Total</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $total = 0;
                                                $detalles = $pedidos->buscarDetallePedido($desde, $hasta, $numero);
                                                if ($detalles) {
                                                    foreach($detalles as $detalle) {
                                                        $item = $detalle[0];
                                                        $subtotal = $item->CANT_PEDID * $item->IMPORTE;
                                                        $total += $subtotal;

                                                        // Lógica para las imágenes
                                                        $imageName = substr($item->COD_ARTICU, 0, 13);
                                                        $imageUrl = file_exists("../Imagenes/".$imageName.".jpg") ? 
                                                                "../Imagenes/".$imageName.".jpg" : "";
                                                        
                                                        // Verificar si es SALE
                                                        $isSale = (substr($item->DESCRIPCIO, -11) == '-- SALE! --');
                                                        $description = $isSale ? substr($item->DESCRIPCIO, 0, -11) : $item->DESCRIPCIO;
                                                ?>
                                                <tr class="<?php echo $item->FALTANTE == 1 ? 'faltante-row' : ''; ?>">
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="<?php echo $imageUrl ? $imageUrl : '/api/placeholder/50/50'; ?>" 
                                                                alt="<?php echo $imageUrl ? $item->COD_ARTICU : 'Sin imagen'; ?>"
                                                                class="product-image me-3"
                                                                style="width: 50px; height: 50px; object-fit: contain;">
                                                            <div class="flex-grow-1">
                                                                <div class="fw-bold text-primary d-flex align-items-center">
                                                                    <?php echo $description; ?>
                                                                    <?php if ($isSale): ?>
                                                                        <span class="badge bg-danger ms-2">SALE</span>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div class="text-muted">
                                                                    <small>Código: <?php echo $item->COD_ARTICU; ?></small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-end">$ <?php echo number_format($item->IMPORTE, 2, ',', '.'); ?></td>
                                                    <td class="text-end"><?php echo $item->CANT_PEDID; ?></td>
                                                    <td class="text-end">$ <?php echo number_format($subtotal, 2, ',', '.'); ?></td>
                                                    <?php if ($item->FALTANTE == 1): ?>
                                                    <td>
                                                        <button type="button" class="btn btn-outline-danger btn ms-2" 
                                                            onclick="abrirHistorial('<?php echo htmlspecialchars($item->COD_ARTICU); ?>', 
                                                                                    '<?php echo htmlspecialchars($description); ?>', 
                                                                                    '<?php echo $item->IMPORTE; ?>', 
                                                                                    '<?php echo $item->CANT_PEDID; ?>')">
                                                            <i class="fas fa-history"></i> Ver Historial
                                                        </button>
                                                    </td>
                                                    <?php endif; ?>
                                                </tr>
                                                <?php
                                                    }
                                                ?>
                                                <tr>
                                                    <td colspan="3" class="text-end fw-bold">Total</td>
                                                    <td class="text-end fw-bold">$ <?php echo number_format($total, 2, ',', '.'); ?></td>
                                                </tr>
                                                <?php
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Modales para las imágenes -->
                            <?php
                            if ($detalles) {
                                foreach($detalles as $detalle) {
                                    $item = $detalle[0];
                                    $imageName = substr($item->COD_ARTICU, 0, 13);
                                    $imageUrl = file_exists("../Imagenes/".$imageName.".jpg") ? 
                                            "../Imagenes/".$imageName.".jpg" : "";
                            ?>
                            <div class="modal fade" id="imageModal<?php echo $imageName; ?>" tabindex="-1" 
                                aria-labelledby="imageModalLabel<?php echo $imageName; ?>" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="imageModalLabel<?php echo $imageName; ?>">
                                                <?php echo $item->DESCRIPCIO; ?>
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <img src="<?php echo $imageUrl; ?>" 
                                                alt="<?php echo $imageName; ?>.jpg - imagen no encontrada" 
                                                class="img-fluid">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                                }
                            }
                            ?>

                            <!-- Modal Historial -->
                            <div class="modal fade" id="historialModal" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-clipboard me-2"></i>
                                                <h5 class="modal-title mb-0">Historial de Reclamo</h5>
                                            </div>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <!-- Estado del Reclamo -->
                                        <div class="status-bar p-3 border-bottom">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2">Estado del Reclamo:</span>
                                                    <span class="badge estado-actual" id="estado"></span>
                                                </div>
                                                <button class="btn btn-outline-success btn-sm" id="btnResolucion">
                                                    <i class="fas fa-check me-1"></i>Marcar como Resuelto
                                                </button>
                                            </div>
                                            
                                            <!-- Sección de Resolución (inicialmente oculta) -->
                                            <div id="seccionResolucion" class="mt-3" style="display: none;">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-label">Resolución</label>
                                                        <select class="form-select" id="tipoResolucion">
                                                            <option value="">Seleccione...</option>
                                                            <option value="cambio">Cambio</option>
                                                            <option value="cancelado">Cancelado</option>
                                                            <option value="completado">Completado</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4" id="seccionSucursal" style="display: none;">
                                                        <label class="form-label">Sucursal</label>
                                                        <select class="form-select" id="selectSucursal"></select>
                                                    </div>
                                                </div class="row">
                                                    <div class="col-12" id="seccionArticulo" style="display: none;">
                                                        <label class="form-label">Artículo</label>
                                                        <select id="selectArticulo" class="form-select"></select>
                                                    </div>
                                                <div>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-body">
                                            <div class="article-details mb-4 border-bottom pb-3">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <h6 class="mb-2">Artículo</h6>
                                                        <p id="modalArticulo" class="mb-1"></p>
                                                        <small id="modalCodigo" class="text-muted"></small>
                                                    </div>
                                                    <div class="col-3">
                                                        <h6 class="mb-2">Precio</h6>
                                                        <p id="modalPrecio"></p>
                                                    </div>
                                                    <div class="col-3">
                                                        <h6 class="mb-2">Cantidad</h6>
                                                        <p id="modalCantidad"></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php 
                                            if(count($detalleReclamo) != 0){
                                                foreach ($detalleReclamo as  $comentario) {
                                
                                                    echo '<div class="seccion-historial border-start border-4 border-primary ps-3 mt-4 seccion-guardada">
                                                            <div class="row g-3 mb-3">
                                                                <div class="col-md-6">
                                                                    <label class="form-label"><i class="fas fa-comments me-2"></i>Tipo de Contacto</label>
                                                                    <select class="form-select tipo-contacto">
                                                                        <option value="mail">'.$comentario[0]->TIPO_CONTACTO.'</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label"><i class="fas fa-user me-2"></i>Agente</label>
                                                                    <select class="form-select agente">
                                                                        <option value="at">'.$comentario[0]->AGENTE.'</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-12">
                                                                    <label class="form-label"><i class="fas fa-comment me-2"></i>Comentario</label>
                                                                    <textarea class="form-control comentario" rows="4">'.$comentario[0]->COMENTARIOS.'</textarea>
                                                                </div>
                                                            </div>
                                                        </div>';
                                                }
                                            }
                                            ?>
                                            <div id="seccionesHistorial">
                                                <div class="seccion-historial">
                                                    <div class="row g-3 mb-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label">
                                                                <i class="fas fa-comments me-2"></i>Tipo de Contacto
                                                            </label>
                                                            <select class="form-select tipo-contacto">
                                                                <option value="mail">Mail</option>
                                                                <option value="whatsapp">WhatsApp</option>
                                                                <option value="facebook">Facebook</option>
                                                                <option value="instagram">Instagram</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">
                                                                <i class="fas fa-user me-2"></i>Agente
                                                            </label>
                                                            <select class="form-select agente">
                                                                <option value="at">Agustina Taboada</option>
                                                                <option value="fc">Florencia Consoli</option>
                                                                <option value="jd">Julieta Dalmeida</option>
                                                                <option value="ls">Leonel Segovia</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label">
                                                                <i class="fas fa-comment me-2"></i>Comentario
                                                            </label>
                                                            <textarea class="form-control comentario" rows="4" placeholder="Ingrese su comentario aquí..."></textarea>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <small class="text-muted">
                                                            <i class="far fa-clock me-1"></i>Creado: <span class="fecha-creacion"></span>
                                                        </small>
                                                        <button type="button" class="btn btn-primary btn-guardar-seccion" onclick="guardarComentario(this)">
                                                            <i class="fas fa-save me-1"></i>Guardar Sección
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <div id="botonesNormales" class="d-flex justify-content-end gap-2">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                <button type="button" class="btn btn-primary" id="agregarSeccion">
                                                    <i class="fas fa-plus"></i> Agregar seguimiento
                                                </button>
                                            </div>
                                            <div id="botonFinalizar" style="display: none;margin-top:20px">
                                                <button type="button" class="btn btn-success" style="margin-top:10px" id="finalizarReclamo">
                                                    <i class="fas fa-check-circle me-1"></i>Finalizar Reclamo
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                <?php
                        }
                    } else {
                        echo '<div class="alert alert-warning">No se encontraron resultados para la búsqueda.</div>';
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="js/consultaPedido.js"></script>         

</body>
</html>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XL - Pedido Despachado</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Inter:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style/notificacionDespachoTienda.css">
</head>
<body>
    <div class="container email-container">
        <!-- Email Header -->
        <div class="email-header">
            <a href="http://xlshop.com.br">
                <img src="https://xlshop.vtexassets.com/assets/vtex/assets-builder/xlshop.xlshop-theme/0.0.0/imgs/logo-xl___e442f6171974569fecd8f17af66d79e4.png" alt="XL Logo">
            </a>
        </div>
        
        <!-- Email Title -->
        <div class="email-title">
            Hola, <span id="clientName">Cliente</span>!
        </div>
        
        <!-- Email Subtitle -->
        <div class="email-subtitle">
            Su pedido <span class="order-number" id="orderNumber">#1515301341830-01</span> se encuentra en camino a nuestra sucursal
        </div>
        
        <!-- Email Banner with Status Icons -->
        <div class="email-banner">
            <div class="status-steps">
                <!-- Los iconos se generarán con JavaScript -->
            </div>
        </div>
        
        <!-- Email Content -->
        <div class="email-content">
            <div>
                <span style="max-width: 550px; display: inline-block;">
                    Su pedido ha sido despachado hacia nuestra sucursal! Corresponde a la compra realizada el día
                    <span id="orderDate">01/03/2025</span>
                </span>
            </div>
        </div>

        <!-- Alert Section -->
        <div class="px-4 alert-section">
            <hr class="divider">
            <h3 class="alert-title">ATENCIÓN:</h3>
            <span style="color: #373535; font-size: 14px;">
                <div id="deliveryInstructions">
                    <ul>
                        <li>Tu pedido fue despachado, y esta en camino al local de retiro. Estara disponible para retirar despues de 24hs habiles (72hs para NOA). No acercarse antes.</li>
                        <!-- <li>Tu pedido fue despachado, y esta en camino al local de retiro. Recibiras un correo cuando esta listo para retirar. No acercarse antes</li> -->
                        <li>Tené en cuenta que: Deberá presentarse la persona que realizó la compra con DNI y la tarjeta con la que realizó la compra e informarle al asesor el n° de pedido</li>
                    </ul>
                </div>
            </span>
        </div>
        
        <!-- Product Details -->
        <div class="px-4">
            <h2 class="section-title">Detalles del pedido</h2>
            
            <div id="productList">
                <!-- Estructura mejorada para productos -->
                <div class="product-item">
                    <div class="product-image">
                        <img src="http://app.xl.com.ar:8080/Imagenes/XV5WDC14C1701.jpg" alt="ERAS CARTERA BANDOLERA">
                    </div>
                    <div class="product-details">
                        <!-- Nombre y Número de Orden (arriba) -->
                         <div class="order-id-tag">
                            <span>Order: <span class="order-id-value">#1515301341830-01</span></span>
                         </div>
                        <div class="product-name">
                            ERAS CARTERA BANDOLERA
                        </div>
                        
                        <!-- Contenedor central para cantidad y color (centro) -->
                        <div class="product-info-container">
                            <!-- Cantidad y Precio -->
                            <div class="product-meta">
                                <div class="product-quantity">Cantidad 1</div>
                                <div class="product-price">$45.435</div>
                            </div>
                            
                            <!-- Color -->
                            <div class="product-color">
                                Color: <span>Negro</span>
                            </div>
                        </div>
                        
                        <!-- Espacio en blanco para mantener alineación (no visible) -->
                        <div style="height: 1px;"></div>
                    </div>
                </div>
                
                <div class="product-item">
                    <div class="product-image">
                        <img src="http://app.xl.com.ar:8080/Imagenes/XV5WDC07C0901.jpg" alt="CORALINE TOTE GRANDE">
                    </div>
                    <div class="product-details">
                        <!-- Nombre y Número de Orden (arriba) -->
                         <div class="order-id-tag">
                            <span>Order: <span class="order-id-value">#1515301341830-01</span></span>
                         </div>
                        <div class="product-name">
                            CORALINE TOTE GRANDE
                        </div>
                        
                        <!-- Contenedor central para cantidad y color (centro) -->
                        <div class="product-info-container">
                            <!-- Cantidad y Precio -->
                            <div class="product-meta">
                                <div class="product-quantity">Cantidad 1</div>
                                <div class="product-price">$15.499</div>
                            </div>
                            
                            <!-- Color -->
                            <div class="product-color">
                                Color: <span>Beige</span>
                            </div>
                        </div>
                        
                        <!-- Espacio en blanco para mantener alineación (no visible) -->
                        <div style="height: 1px;"></div>
                    </div>
                </div>
            </div>
            
            <p class="order-info">Pedido Realizado el <span id="orderDateInfo">01/03/2025</span></p>
        </div>
        
        <!-- Payment Section -->
        <div class="px-4">
            <div class="payment-section">
                <h4 class="section-title">Formas de pago</h4>
                
                <div id="paymentDetails">
                    <div class="payment-row">
                        <span>Tarjeta de crédito</span>
                        <span>**** **** **** 4321</span>
                    </div>
                    <div class="payment-row">
                        <span>Cuotas</span>
                        <span id="installmentsInfo">3x sin interés</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Shipping Section -->
        <div class="px-4">
            <div class="shipping-section">
                <h4 class="section-title">Entrega</h4>
                
                <div id="shippingDetails">
                    <div class="shipping-row">
                        <strong><span>Tipo de entrega</span></strong>
                        <span id="deliveryType">Retiro en tienda</span>
                    </div>
                    <div class="shipping-row">
                        <strong><span>Dirección de entrega</span></strong>
                        <span id="deliveryAddress">XL Villa Ballester - Independencia, 4741 - Villa Ballester - Buenos Aires - CEP 1653</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <div class="social-links">
                <a href="https://www.facebook.com/xlextralarge/">
                    <img src="https://xlshop.vteximg.com.br/arquivos/facebook-ico.svg" alt="Logo Facebook">
                </a>
                <a href="https://www.instagram.com/xlextralarge/">
                    <img src="https://xlshop.vteximg.com.br/arquivos/instagram-ico.svg" alt="Logo Instagram">
                </a>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap & JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="js/htmlDespachoTienda.js"></script>
</body>
</html>
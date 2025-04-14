
// Datos de ejemplo del pedido
const orderData = {
    orderId: '1515301341830-01',
    clientName: 'Mercedes Ramella',
    orderDate: '05/03/2025',
    deliveryType: 'pickup-in-point',
    products: [
        {
            name: 'ERAS CARTERA BANDOLERA',
            imageUrl: 'http://192.168.0.143:8080/Imagenes/XV5WDC14C1701.jpg',
            quantity: 1,
            price: 12999,
            color: 'Negro'
        },
        {
            name: 'CORALINE TOTE GRANDE',
            imageUrl: 'http://192.168.0.143:8080/Imagenes/XV5WDC07C0901.jpg',
            quantity: 1,
            price: 15499,
            color: 'Beige'
        }
    ],
    payment: {
        type: 'Tarjeta de crédito',
        lastDigits: '4321',
        installments: 3
    },
    shipping: {
        storeLocation: 'XL Villa Ballester',
        address: 'XL Villa Ballester - Independencia, 4741 - Villa Ballester - Buenos Aires - CEP 1653'
    },
    status: {
        received: true,
        processed: true,
        dispatched: true,
        ready: true,
        delivered: false
    }
};

// Función para formatear moneda
function formatCurrency(amount) {
    return new Intl.NumberFormat('es-AR').format(amount);
}

// Actualizar el DOM con los datos del pedido
document.addEventListener('DOMContentLoaded', function() {
    // Actualizar información básica del pedido
    document.getElementById('clientName').textContent = orderData.clientName;
    document.getElementById('orderNumber').textContent = '#' + orderData.orderId;
    document.getElementById('orderDate').textContent = orderData.orderDate;
    document.getElementById('orderDateInfo').textContent = orderData.orderDate;
    
    // Actualizar lista de productos
    // Nota: Ya los hemos incluido directamente en el HTML, pero este código funciona
    // si necesitas generarlos dinámicamente en lugar de codificarlos de forma estática
    
    const productListContainer = document.getElementById('productList');
    // Comentamos esta línea para no sobrescribir los productos ya incluidos en el HTML
    // productListContainer.innerHTML = ''; 
    
    /* Este código está comentado porque ya incluimos los productos en el HTML
    orderData.products.forEach(product => {
        const productElement = document.createElement('div');
        productElement.className = 'product-item';
        productElement.innerHTML = `
            <div class="product-image">
                <img src="${product.imageUrl}" alt="${product.name}">
            </div>
            <div class="product-details">
                <div class="product-name">${product.name}</div>
                <p class="order-id-tag">Order: <span class="order-id-value">#${orderData.orderId}</span></p>
                <div class="product-meta">
                    Cantidad ${product.quantity}
                    <div class="product-price">$${formatCurrency(product.price)}</div>
                </div>
                <div>
                    <p class="product-color">
                        Color: <span>${product.color}</span>
                    </p>
                </div>
            </div>
        `;
        productListContainer.appendChild(productElement);
    });
    */
    
    // Actualizar detalles de pago
    const installmentsText = orderData.payment.installments === 1 ? 'de contado' : `${orderData.payment.installments}x sin interés`;
    document.getElementById('installmentsInfo').textContent = installmentsText;
    
    // Actualizar últimos dígitos de la tarjeta de crédito si está disponible
    const paymentDetailsDiv = document.getElementById('paymentDetails').querySelector('.payment-row:first-child span:last-child');
    paymentDetailsDiv.textContent = `**** **** **** ${orderData.payment.lastDigits}`;
    
    // Actualizar datos de envío
    document.getElementById('deliveryType').textContent = 'Retiro en tienda';
    document.getElementById('deliveryAddress').textContent = orderData.shipping.address;
    
    // Actualizar el rastreador de estado del pedido con iconos de Font Awesome
    updateOrderStatusTracker();
});

// Función para actualizar el rastreador de estado del pedido con iconos 
// y textos en dos líneas
function updateOrderStatusTracker() {
    const statusStepsContainer = document.querySelector('.status-steps');
    if (!statusStepsContainer) return;
    
    // Iconos de Font Awesome para cada estado
    const icons = {
        pedidoRealizado: 'fa-solid fa-check',
        pagoConfirmado: 'fa fa-credit-card',
        pedidoFacturado: 'bi bi-box-seam',
        pedidoEnviado: 'bi bi-truck',
        pedidoEntregado: 'fa-solid fa-user'
    };
    
    // Etiquetas para cada estado (con saltos de línea para visualización en dos líneas)
    // Insertamos un <br> para forzar el salto de línea
    const labels = {
        pedidoRealizado: 'Pedido<br>Realizado',
        pagoConfirmado: 'Pago<br>Confirmado',
        pedidoFacturado: 'Pedido<br>Facturado',
        pedidoEnviado: 'Pedido<br>Despachado',
        pedidoEntregado: 'Pedido<br>Listo para Retirar'
    };
    
    // Configurar los pasos de estado
    const steps = [
        { id: 'received', labelKey: 'pedidoRealizado', icon: icons.pedidoRealizado },
        { id: 'processed', labelKey: 'pagoConfirmado', icon: icons.pagoConfirmado },
        { id: 'dispatched', labelKey: 'pedidoFacturado', icon: icons.pedidoFacturado },
        { id: 'ready', labelKey: 'pedidoEnviado', icon: icons.pedidoEnviado },
        { id: 'delivered', labelKey: 'pedidoEntregado', icon: icons.pedidoEntregado }
    ];
    
    // Construir el HTML para los iconos de estado
    let stepsHTML = '';
    steps.forEach(step => {
        const isCompleted = orderData.status[step.id] ? 'completed' : '';
        stepsHTML += `
            <div class="status-step">
                <div class="step-icon-circle ${isCompleted}">
                    <i class="${step.icon}"></i>
                </div>
                <div class="step-label">${labels[step.labelKey]}</div>
            </div>
        `;
    });
    
    // Actualizar el contenedor con los nuevos iconos
    statusStepsContainer.innerHTML = stepsHTML;
}
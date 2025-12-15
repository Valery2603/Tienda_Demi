<?php
require 'includes/funciones.php'; 

// Obtener el total del URL 
$total_pagado = filter_input(INPUT_GET, 'total', FILTER_VALIDATE_FLOAT);

// Establecer un ID de pedido simulado 
$id_pedido_simulado = rand(100000, 999999);

// Configurar el título y cargar el header
$titulo = "Pedido Confirmado | Demi";
incluirTemplates('header');
?>

<div class="container my-5 adamina-regular">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            
            <div class="card shadow-lg p-5">
                
                <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="#4CAF50" class="bi bi-check-circle-fill mx-auto mb-4" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.429 10.32l-.707-.707a.75.75 0 0 0-1.06 1.06l1.25 1.25a.75.75 0 0 0 1.06 0l5.5-5.5a.75.75 0 0 0-.022-1.08z"/>
                </svg>

                <h1 class="card-title adamina-regular text-success mb-3">¡Tu Pedido ha sido Confirmado!</h1>
                
                <p class="lead">
                    Gracias por tu compra en Demi. Tu pedido ha sido procesado exitosamente y está siendo preparado para el envío.
                </p>

                <div class="alert alert-light border p-3 mt-4 text-start">
                    <p class="mb-1"><strong>Número de Pedido:</strong> #<?php echo $id_pedido_simulado; ?></p>
                    <?php if ($total_pagado !== false): ?>
                        <p class="mb-1"><strong>Monto Pagado:</strong> <span class="fw-bold text-dark fs-5">S/.<?php echo number_format($total_pagado, 2); ?></span></p>
                    <?php endif; ?>
                    <p class="mb-0 text-muted small">Recibirás un email de confirmación con los detalles de seguimiento.</p>
                </div>

                <div class="mt-4">
                    <a href="index.php" class="btn boton-enviar">Volver a la Tienda</a>
                </div>
            </div>

        </div>
    </div>
</div>

<?php 
incluirTemplates('footer'); 
?>
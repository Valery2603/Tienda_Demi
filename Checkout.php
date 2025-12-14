<?php
require 'includes/funciones.php';
require 'includes/config/database.php';
$db = conectarDB();

// VERIFICAR EL CARRITO
if (empty($_SESSION['carrito'])) {
    // Redirigir si el carrito está vacío
    header('Location: Carrito.php?vacio=1');
    exit();
}

// CONSULTAR DETALLES DEL CARRITO
$productos_en_carrito = [];
$total_general = 0;
$ids = array_keys($_SESSION['carrito']);
$ids_string = implode(',', $ids);

// Consulta para obtener detalles (incluyendo nombre_categoria para la imagen)
$sql = "SELECT p.id_producto, p.nombre, p.precio, p.imagen, c.nombre_categoria 
        FROM productos p
        INNER JOIN categorias c ON p.id_categoria = c.id_categoria
        WHERE p.id_producto IN ($ids_string)";
$resultado = mysqli_query($db, $sql);

while ($fila = mysqli_fetch_assoc($resultado)) {
    $id = $fila['id_producto'];
    $cantidad = $_SESSION['carrito'][$id]['cantidad'];
    $subtotal = $fila['precio'] * $cantidad;
    
    $productos_en_carrito[] = array_merge($fila, ['cantidad' => $cantidad, 'subtotal' => $subtotal]);
    $total_general += $subtotal;
}


// MANEJO DEL FORMULARIO (Lógica de Procesamiento)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalizar_compra'])) {
    
    // Recolección y Sanitización de Datos
    $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $direccion = filter_input(INPUT_POST, 'direccion', FILTER_SANITIZE_STRING);

    // Validación (Añadir lógica de validación aquí)
    if (empty($nombre) || empty($email) || empty($direccion)) {
        $error = "Por favor, complete todos los campos de envío requeridos.";
    } else {
        // Proceso de la Orden (Simulación)
        // Guardar datos del pedido y detalles del carrito en la DB
        // Procesar pago (usando una API externa)
        unset($_SESSION['carrito']);
        
        // Simulación de éxito
        header('Location: PedidoConfirmado.php?total=' . $total_general);
        exit();
    }
}
incluirTemplates('header');
incluirTemplates('nav');
?>

<div class="container my-5 adamina-regular">
    <h1 class="text-center mb-5">Finalizar Compra</h1>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-5 order-md-2 mb-4">
            <h4 class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">Tu Pedido</span>
                <span class="badge bg-secondary rounded-pill"><?php echo count($productos_en_carrito); ?></span>
            </h4>
            <ul class="list-group mb-3">
                <?php foreach ($productos_en_carrito as $producto): ?>
                    <li class="list-group-item d-flex justify-content-between lh-sm">
                        <div>
                            <h6 class="my-0"><?php echo $producto['nombre']; ?></h6>
                            <small class="text-muted">Cantidad: <?php echo $producto['cantidad']; ?></small>
                        </div>
                        <span class="text-muted">S/.<?php echo number_format($producto['subtotal'], 2); ?></span>
                    </li>
                <?php endforeach; ?>
                
                <li class="list-group-item d-flex justify-content-between">
                    <span>Total General</span>
                    <strong>S/.<?php echo number_format($total_general, 2); ?></strong>
                </li>
            </ul>
        </div>

        <div class="col-md-7 order-md-1">
            <form action="checkout.php" method="POST" class="needs-validation">
                
                <h4>Datos de Envío</h4>
                <hr class="mb-4">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nombre">Nombre Completo</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="dni">Identificación</label>
                        <input type="text" class="form-control" id="identificacion" name="identificacion" placeholder="DNI/C.E" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="telefono">Teléfono</label>
                        <input type="phone" class="form-control" id="telefono" name="telefono" placeholder="" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="departamento">Departamento</label>
                        <input type="text" class="form-control" id="telefono" name="telefono" placeholder="" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="provincia">Provincia</label>
                        <input type="text" class="form-control" id="provincia" name="provincia" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="distrito">Distrito</label>
                        <input type="text" class="form-control" id="distrito" name="distrito" placeholder="" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="direccion">Dirección de Envío</label>
                    <input type="text" class="form-control" id="direccion" name="direccion" placeholder="" required>
                </div>

                <div class="mb-3">
                    <label for="referencia">Referencia</label>
                    <input type="text" class="form-control" id="referencia" name="referencia" placeholder="" required>
                </div>

                <h4 class='mt-2'>Información de Pago</h4>
                <hr class="mb-4">
                <div class="my-3">
                    <div class="form-check">
                        <input id="tarjeta" name="metodo_pago" type="radio" class="form-check-input" checked required>
                        <label class="form-check-label" for="tarjeta">Tarjeta de Crédito / Débito</label>
                    </div>
                    </div>

                <div class="row gy-3">
                    <div class="col-md-6">
                        <label for="cc-nombre">Nombre del Titular</label>
                        <input type="text" class="form-control" id="cc-nombre" name="cc-nombre" required>
                    </div>
                    <div class="col-md-6">
                        <label for="cc-numero">Número de tarjeta</label>
                        <input type="text" class="form-control" id="cc-numero" name="cc-numero" placeholder="XXXX XXXX XXXX XXXX" required>
                    </div>
                    <div class="col-md-3">
                        <label for="cc-expiracion">Expiración</label>
                        <input type="text" class="form-control" id="cc-expiracion" name="cc-expiracion" placeholder="MM/AA" required>
                    </div>
                    <div class="col-md-3">
                        <label for="cc-cvv">CVV</label>
                        <input type="text" class="form-control" id="cc-cvv" name="cc-cvv" required>
                    </div>
                </div>

                <hr class="my-4">
                <button class="w-100 btn boton-enviar btn-lg" type="submit" name="finalizar_compra">
                    Pagar S/.<?php echo number_format($total_general, 2); ?>
                </button>
            </form>
        </div>
    </div>
</div>

<?php 
incluirTemplates('footer'); 
?>
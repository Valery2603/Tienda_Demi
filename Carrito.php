<?php
require 'includes/funciones.php';
require_once 'includes/config/database.php'; 

$db=conectarDB();
// Inicializar el carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}
$productos_por_pagina = 5; 
$pagina_actual = $_GET['page'] ?? 1;
$pagina_actual = filter_var($pagina_actual, FILTER_VALIDATE_INT);
// Asegura que la página sea un número válido y mayor a 1
if (!$pagina_actual || $pagina_actual < 1) {
    $pagina_actual = 1;
}

// ----------------------------------------------------
// Lógica para ELIMINAR un producto
// ----------------------------------------------------
if (isset($_GET['eliminar_id'])) {
    $id_a_eliminar = $_GET['eliminar_id'];
    
    if (isset($_SESSION['carrito'][$id_a_eliminar])) {
        unset($_SESSION['carrito'][$id_a_eliminar]);
        // Redirigir para limpiar el parámetro GET de la URL
        header('Location: Carrito.php?eliminado=1'); 
        exit();
    }
}

// ----------------------------------------------------
// Lógica para ACTUALIZAR la cantidad (usando POST)
// ----------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_cantidad'])) {
    $id_producto = $_POST['producto_id'];
    $nueva_cantidad = (int)$_POST['cantidad'];
    
    if (isset($_SESSION['carrito'][$id_producto]) && $nueva_cantidad > 0) {
        // Asumiendo que has consultado el stock antes de llegar aquí:
        $_SESSION['carrito'][$id_producto]['cantidad'] = $nueva_cantidad;
    } elseif ($nueva_cantidad <= 0) {
        // Eliminar si la cantidad es cero o menos
        unset($_SESSION['carrito'][$id_producto]);
    }
    // Redirigir al carrito
    header('Location: Carrito.php?actualizado=1');
    exit();
}

// ----------------------------------------------------
// Lógica para CONSULTAR los detalles completos
// ----------------------------------------------------
$productos_en_carrito = [];
$total_general = 0;
$productos_base = [] ;

if (!empty($_SESSION['carrito'])) {
    // 1. Obtener los IDs de los productos en la sesión
    $ids = array_keys($_SESSION['carrito']);
    $ids_string = implode(',', $ids);

    // Consulta segura
    $sql = "SELECT p.id_producto, p.nombre, p.precio, p.imagen, c.nombre_categoria 
            FROM productos p
            INNER JOIN categorias c ON p.id_categoria = c.id_categoria
            WHERE p.id_producto IN ($ids_string)
            ORDER BY p.id_producto DESC"; // Ordenar para paginación consistente
    
    $resultado = mysqli_query($db, $sql);
    
    // Rellenar la lista base y calcular el total general
    while ($fila = mysqli_fetch_assoc($resultado)) {
         $id = $fila['id_producto'];
         $cantidad = $_SESSION['carrito'][$id]['cantidad'];
         $subtotal = $fila['precio'] * $cantidad;
       
         $productos_base[] = array_merge($fila, ['cantidad' => $cantidad, 'subtotal' => $subtotal]);
         $total_general += $subtotal;
    }
}

// --- LÓGICA DE PAGINACIÓN APLICADA AL ARRAY ---
// Obtener el número total de ítems en el carrito
$total_productos_carrito = count($productos_base);

// Calcular el total de páginas
$total_paginas = ceil($total_productos_carrito / $productos_por_pagina);

// Ajustar la página actual si es inválida
if ($pagina_actual > $total_paginas && $total_paginas > 0) {
    $pagina_actual = $total_paginas;
} elseif ($total_paginas === 0) {
    $pagina_actual = 1;
}

// Calcular el OFFSET final (desde dónde empezar)
$offset = ($pagina_actual - 1) * $productos_por_pagina;

// Aplicar la paginación al array
$productos_en_carrito = array_slice($productos_base, $offset, $productos_por_pagina);

incluirTemplates('header');
incluirTemplates('nav');

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Carrito de Compras</title>
</head>
<body class='adamina-regular'>
    <div class="container m-5 mx-auto contenedor-carrito adamina-regular">
        <h1 class='text-center mb-5'>Carrito de Compras</h1>
        <?php 
            $eliminado = $_GET['eliminado'] ?? null;
            $actualizado = $_GET['actualizado'] ?? null;

            if ($eliminado == 1) {
                echo "<div class='text-center col-6 mx-auto py-2 text-success fw-bold fs-5' id='success'>Producto eliminado del carrito.</div>";
            }
            if ($actualizado == 1) {
                echo "<div class='text-center col-6 mx-auto py-2 text-success fw-bold fs-5' id='success'>Cantidad actualizada correctamente.</div>";
            }
        ?>
        <?php if (empty($productos_en_carrito)): ?>
            <div class="alert volver">
                <p>Tu carrito está vacío. ¡Añade algunos productos!</p>
                <a href="index.php" class='fs-5'>Volver a la Tienda</a>
            </div>
        <?php else: ?>
            
            <table class="tabla-carrito">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Bucle para mostrar CADA producto en el carrito
                    foreach ($productos_en_carrito as $producto): 
                    ?>
                        <tr>
                            <td>
                                <img src="imagenes/<?php echo $producto['nombre_categoria'];?>/<?php echo $producto['imagen'];?>" class="imagen-producto-tabla" alt="<?php echo $producto['nombre']; ?>">
                                <?php echo $producto['nombre']; ?>
                            </td>
                            <td>S/.<?php echo number_format($producto['precio'], 2); ?></td>
                            <td>
                                <form action="carrito.php" method="POST">
                                    <input type="hidden" name="producto_id" value="<?php echo $producto['id_producto']; ?>">
                                    <input type="number" 
                                           name="cantidad" 
                                           value="<?php echo $producto['cantidad']; ?>" 
                                           min="1" 
                                           style="width: 60px; text-align: center;">
                                    <input type="submit" name="actualizar_cantidad" value="Actualizar" style="display: none;">
                                </form>
                                </td>
                            <td>S/.<?php echo number_format($producto['subtotal'], 2); ?></td>
                            <td>
                                <a href="carrito.php?eliminar_id=<?php echo $producto['id_producto']; ?>" class='enlace-borde'
                                   onclick="return confirm('¿Estás seguro de que quieres eliminar este producto?');">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
                                        <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            

            <div class="resumen-total">
                <h4 class="mb-5">Total a Pagar: S/.<?php echo number_format($total_general, 2); ?></h4>
                <?php if ($total_paginas > 1){ ?>
            <nav aria-label="Paginacion de productos" class="mt-1">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php echo ($pagina_actual <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $pagina_actual - 1; ?>">Anterior</a>
                    </li>
            <?php for ($i = 1; $i <= $total_paginas; $i++){ ?>
                    <li class="page-item <?php echo ($i == $pagina_actual) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
            <?php } ?>
                    <li class="page-item <?php echo ($pagina_actual >= $total_paginas) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $pagina_actual + 1; ?>">Siguiente</a>
                    </li>
                </ul>
            </nav>
            <?php } ?>
                <a href="Checkout.php" class="col-6 mx-auto boton-enviar p-2 text-center fs-4">Continuar Compra</a>
            </div>
            <?php endif; ?>

    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const quantityInputs = document.querySelectorAll('.tabla-carrito input[name="cantidad"]');

            quantityInputs.forEach(input => {
                // Cada vez que el valor del campo cambie
                input.addEventListener('change', function() {
                    // Envía el formulario al que pertenece el input
                    this.form.submit();
                });
            });

            // Ocultar el mensaje de éxito después de unos segundos
            const successMessage = document.getElementById('success');
            if (successMessage) {
                setTimeout(() => {
                    successMessage.style.display = 'none';
                }, 3000); // 3 segundos
            }
        });
    </script>
<?php 
    incluirTemplates('footer'); 
?>
</body>
</html>
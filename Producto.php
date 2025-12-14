<?php
    require 'includes/funciones.php';
    require_once 'includes/config/database.php';
    $id = $_GET['id'];
    $id = filter_var($id, FILTER_VALIDATE_INT);
    if(!$id) {
        header('Location: Tienda.php');
        exit; 
    }
    $db=conectarDB();
    // Inicializar el carrito si no existe
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }
    // ----------------------------------------------------
    // Lógica para AÑADIR producto al carrito
    // ----------------------------------------------------
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_carrito'])) {
        // 1. Obtener y validar datos
        $id_producto = filter_input(INPUT_POST, 'id_producto', FILTER_VALIDATE_INT);
        $cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_INT);

        if ($id_producto && $cantidad && $cantidad > 0) {
            // 2. Almacenar en la sesión
            // Usamos el ID del producto como clave del array
            if (isset($_SESSION['carrito'][$id_producto])) {
                // Si ya existe, incrementa la cantidad
                $_SESSION['carrito'][$id_producto]['cantidad'] += $cantidad;
            } else {
                // Si no existe, crea el registro
                $_SESSION['carrito'][$id_producto] = [
                    'cantidad' => $cantidad
                ];
            }
            // 3. Redirigir para evitar reenvío del formulario 
            
            header('Location: Producto.php?id='.$id.'&agregado=1');
            exit();
        }
    }

    $consulta= "SELECT 
                p.id_producto,
                p.nombre, 
                p.descripcion, 
                p.imagen, 
                p.precio,
                p.stock,
                p.activo,
                c.nombre_categoria,
                c.id_categoria 
              FROM 
                productos p 
              INNER JOIN 
                categorias c ON p.id_categoria = c.id_categoria WHERE p.id_producto = ${id} and activo=1"; 
    $productos=mysqli_query($db,$consulta);
    $producto=mysqli_fetch_assoc($productos);
    $consulta_categoria="select * from categorias";
    $categorias=mysqli_query($db,$consulta_categoria);
    $categoria=mysqli_fetch_assoc($categorias);
    $slug_categoria = str_replace(' ', '-', $categoria['nombre_categoria']);
    if(mysqli_num_rows($productos) === 0) {
        header('Location: Tienda.php');
        exit; 
    }

    $imagenes = [];
    if (isset($producto['imagen'])) {
        $imagenes[] = $producto['imagen'];
    }
    // Consulta de imágenes secundarias 
    $consulta_secundarias = "SELECT ruta_imagen FROM imagenes_producto WHERE id_producto = ${id}";
    $resultado_secundarias = mysqli_query($db, $consulta_secundarias);
    // Añadir las imágenes secundarias al array
    while ($imagen_secundaria = mysqli_fetch_assoc($resultado_secundarias)) {
        $imagenes[] = $imagen_secundaria['ruta_imagen'];
    }
    
    incluirTemplates('header');
    incluirTemplates('nav');
?>

<body>
    <main class="adamina-regular contenedor seccion contenido-centrado">
        <h1 class='my-5 text-center'><?php echo $producto['nombre']; ?></h1>
        <div class='container volver fs-4 mx-auto'>
                <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                </svg>
                <?php echo 
                "<a href='Tienda.php?id=".$producto['id_categoria']."'>Volver</a>" ?>
        </div>  
        <div class='container row mx-auto'>
            <div class='col-12 col-md-5 mt-4 mb-3 mx-auto'>
    
                <div id="carruselProducto" class="carousel slide" data-bs-ride="carousel">
                    
                    <div class="carousel-indicators">
                        <?php foreach ($imagenes as $i => $ruta): ?>
                            <button 
                                type="button" 
                                data-bs-target="#carruselProducto" 
                                data-bs-slide-to="<?php echo $i; ?>" 
                                class="<?php echo ($i === 0) ? 'active' : ''; ?>" 
                                aria-label="Slide <?php echo $i + 1; ?>">
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <div class="carousel-inner">
                        <?php foreach ($imagenes as $i => $ruta): ?>
                            <div class="carousel-item <?php echo ($i === 0) ? 'active' : ''; ?>">
                                <img src="imagenes/<?php echo $producto['nombre_categoria'];?>/<?php echo $ruta; ?>" class="d-block w-100 img-fluid" alt="Imagen del producto <?php echo $i + 1; ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <button class="carousel-control-prev" type="button" data-bs-target="#carruselProducto" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carruselProducto" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Siguiente</span>
                    </button>
                    
                </div>
    
            </div>
            <div class='col-12 col-md-5 p-4 mt-3 mb-3 mx-auto'>
                <?php echo 
                "<a class='categoria' href='Tienda.php?id=".$producto['id_categoria']."'>".$producto['nombre_categoria']."</a>" ?>
                <h4 class='my-4'><?php echo $producto['descripcion']; ?></h4>
                <p class='fs-2 fw-bold my-4'>S/<?php echo $producto['precio']; ?></p>

                <form action='Producto.php?id=<?php echo $id ?>' method='POST'> 
                    <input type='hidden' name='id_producto' value='<?php echo $producto['id_producto'];?>'> 
                    <div class='col-6'>
                            <div class='cantidad-selector'>
                                <label for="cantidad_<?php echo $producto['id_producto']?>" class='pb-2'>Cantidad</label>
                                <div class='input-group mb-2'>
                                    <div class='col-3 border text-center'>
                                        <button type='button' class='btn btn-default btn-sm minus-btn' data-id=<?php echo $producto['id_producto'];?>>-</button>
                                    </div>
                                    <input type='number' name='cantidad' id='cantidad_<?php echo $producto['id_producto']?>'
                                        value='1' min='1' max=<?php echo $producto['stock'] ?> 
                                        class='form-control quantity-input text-center' readonly> 
                                    <div class='col-3 border text-center'>
                                        <button type='button' class='btn btn-default btn-sm plus-btn' data-id=<?php echo $producto['id_producto'];?>>+</button>
                                    </div>   
                                </div>
                            </div> 
                    </div>
                    <div class=''>
                        <button type='submit' name='agregar_carrito' class='text-center fs-5 boton-enviar p-2 agregar-carrito-btn col-12' 
                        data-id=<?php echo $producto['id_producto']?>>
                            Agregar a carrito
                        </button>
                    </div>  
                </form>
            </div>
    </main>
    <?php 
        incluirTemplates('footer'); 
    ?>
<script src='js/carrito.js'></script>
</body>
<div id="notificacion-carrito" style="position: fixed; bottom: 20px; right: 20px; background-color: #26222F; color: white; padding: 15px; border-radius: 5px; z-index: 1000; display: none;">
    Añadido al carrito ✅
</div>
</html>
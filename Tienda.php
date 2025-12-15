<?php
    require 'includes/funciones.php';
    require 'includes/config/database.php';
    $db=conectarDB();
    $id = $_GET['id'];
    $id = filter_var($id, FILTER_VALIDATE_INT); 
    if(!$id) {
        header('Location: index.php');
        exit; 
    }
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
            
            header('Location: Tienda.php?id='.$id.'&agregado=1');
            exit();
        }
    }

    $consulta_productos= "SELECT 
                p.id_producto,
                p.nombre, 
                p.descripcion, 
                p.imagen, 
                p.precio,
                p.stock,
                c.nombre_categoria,
                c.id_categoria,
                p.activo
              FROM 
                productos p 
              INNER JOIN 
                categorias c ON p.id_categoria = c.id_categoria where c.id_categoria=${id} and activo=1";
    $productos=mysqli_query($db,$consulta_productos);
    $consulta_categoria="select nombre_categoria from categorias where id_categoria=${id}";
    $categorias=mysqli_query($db,$consulta_categoria);
    $categoria=mysqli_fetch_assoc($categorias);
    $titulo=$categoria['nombre_categoria'];

    incluirTemplates('header'); 
    incluirTemplates('nav');   
?>

<body class='fondo adamina-regular'>
    <main class="seccion">
        <h1 class='adamina-regular text-center mt-5 mb-4'><?php echo $categoria['nombre_categoria']?></h1>
        <div class='container'> 
            <div class='row mx-auto caja-producto adamina-regular justify-content-center'>
                <?php while ($producto=mysqli_fetch_assoc($productos)) {?>
                <div class='col-12 col-sm-6 col-lg-3 p-2 mb-3 m-3 shadow'>
                    <?php  echo 
                    "<a class='fs-4' href='Producto.php?id=".$producto['id_producto']."'>
                        <img src='imagenes/".$producto['nombre_categoria']."/".$producto['imagen']."' class='img-fluid'>
                    </a>
                    <div class='text-center pt-3 volver'> 
                        <a class='fs-4' href='Producto.php?id=".$producto['id_producto']."'>".$producto['nombre']."</a>
                    </div>
                        
                    <div class='text-center pt-3'><p class='text-center fs-4 fw-bold'>S/.".$producto['precio']."</p></div>
                    
                    <form action='Tienda.php?id={$id}' method='POST'>
                        
                        <input type='hidden' name='id_producto' value='".$producto['id_producto']."'> 
                    
                        <div class='col-6'>
                            <div class='cantidad-selector'>
                                <label for='cantidad_".$producto['id_producto']."' class='pb-2'>Cantidad</label>
                                <div class='input-group mb-2 '>
                                    <div class='col-3 border text-center'>
                                        <button type='button' class='btn btn-default btn-sm minus-btn' data-id='".$producto['id_producto']."'>-</button>
                                    </div>
                                    <input type='number' name='cantidad' id='cantidad_".$producto['id_producto']."'
                                        value='1' min='1' max='".$producto['stock']."' 
                                        class='form-control quantity-input text-center' readonly> 
                                    <div class='col-3 border text-center'>
                                        <button type='button' class='btn btn-default btn-sm plus-btn' data-id='".$producto['id_producto']."'>+</button>
                                    </div>   
                                </div>
                            </div> 
                        </div>
                        
                        <div class='col-12'>
                            <button type='submit' name='agregar_carrito' class='text-center fs-5 boton-enviar p-2 agregar-carrito-btn col-12' 
                            data-id='".$producto['id_producto']."'>
                                Agregar a carrito
                            </button>
                        </div>
                    </form>"?>
                </div>
                <?php    
                }
                ?>
            </div>
            <div class='mb-4 volver fs-4'>
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                </svg>
                <a href="index.php" class='adamina-regular'>Volver</a>
            </div>
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
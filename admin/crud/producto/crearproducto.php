<?php
    require '../../../includes/funciones.php';
    require '../../../includes/config/database.php';
    $db=conectarDB();

    $consulta = "select * from categorias";
    $categorias = mysqli_query($db, $consulta);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre=$_POST['nombre-producto'];
    $descripcion = $_POST['descripcion'];
    $imagen = $_FILES['imagen'] ?? null;
    $id_categoria = mysqli_real_escape_string($db,$_POST['categoria']);
    $precio = mysqli_real_escape_string($db,$_POST['precio']); 
    $stock = mysqli_real_escape_string($db,$_POST['stock']);
    $errores = [];

    if (empty($nombre) || empty($descripcion) || empty($id_categoria) || empty($precio) || empty($stock)) {
        $errores[] = "Todos los campos son obligatorios. ";
    }
    if (!$imagen || $imagen['error'] !== UPLOAD_ERR_OK) {
        $errores[] = "La imagen es obligatoria o hubo un error al subirla.";
    }

    $query_verificar = "SELECT id_producto FROM productos WHERE nombre = ?";
    $stmt_verificar = $db->prepare($query_verificar);
    $stmt_verificar->bind_param("s", $nombre);
    $stmt_verificar->execute();
    $stmt_verificar->store_result();
    
    if ($stmt_verificar->num_rows > 0) {
        $errores[] = "El producto ya está registrado.";
    }
    $stmt_verificar->close();
    $nombre_imagen = '';
    
    if (empty($errores)) {
        $consulta_categoria="select nombre_categoria from categorias where id_categoria = $id_categoria";
        $resultado=mysqli_query($db,$consulta_categoria);
        $nombre_categoria=mysqli_fetch_assoc($resultado)['nombre_categoria']; 

        $carpeta_imagenes = '../../../imagenes/'.$nombre_categoria.'/';
        if (!is_dir($carpeta_imagenes)) {
            mkdir($carpeta_imagenes);
        }
        $nombre_imagen = md5(uniqid(rand(), true)) . ".jpg";
        move_uploaded_file($imagen['tmp_name'], $carpeta_imagenes . $nombre_imagen);

        $query_insertar = "INSERT INTO productos (nombre, descripcion, imagen, id_categoria, precio, stock) 
                           VALUES (?, ?, ?, ?,?,?)";                         
        $stmt_insertar = $db->prepare($query_insertar);
        $stmt_insertar->bind_param("sssidi", $nombre, $descripcion, $nombre_imagen, $id_categoria, $precio, $stock);
        
        if ($stmt_insertar->execute()) {
            $id_producto_nuevo = $db->insert_id;
            $stmt_insertar->close();
        
        // PROCESAR IMÁGENES SECUNDARIAS (CARRUSEL)
        if (isset($_FILES['imagenes_secundarias']) && is_array($_FILES['imagenes_secundarias']['name'])) {
            $archivos = $_FILES['imagenes_secundarias'];
            $num_imagenes = count($archivos['name']);
            
            for ($i = 0; $i < $num_imagenes; $i++) {
                if ($archivos['error'][$i] === UPLOAD_ERR_OK) {
                    $nombre_temporal = $archivos['tmp_name'][$i];
                    $extension = pathinfo($archivos['name'][$i], PATHINFO_EXTENSION);
                    $nombre_imagen_secundaria = md5(uniqid(rand(), true)) . "." . $extension;
                    
                    // Mover el archivo subido
                    if (move_uploaded_file($nombre_temporal, $carpeta_imagenes . $nombre_imagen_secundaria)) {
                    
                        $query_secundaria = "INSERT INTO imagenes_producto (id_producto, ruta_imagen) VALUES (?, ?)";
                        $stmt_secundaria = $db->prepare($query_secundaria);
                        $stmt_secundaria->bind_param("is", $id_producto_nuevo, $nombre_imagen_secundaria);
                        $stmt_secundaria->execute();
                        $stmt_secundaria->close();
                    }
                }
            }
        }
            header("Location: listaproducto.php?resultado=1"); 
            exit();
        
    } else {
        $errores[] = "Error al intentar registrar el producto principal: " . $stmt_insertar->error;
    }

}

    if (!empty($errores)) {

        session_start();
        $_SESSION['errores_registro'] = $errores;
        header("Location: crearproducto.php"); 
        echo $errores;
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel</title>
    <link rel="stylesheet" href="../../../css/bootstrap.min.css">
    <link rel="stylesheet" href="../../../css/estilos.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Adamina&display=swap" rel="stylesheet">
</head>
<body class='fondo adamina-regular'>
    <header id='contenedor' class="d-flex align-items-center justify-content-center">
        <img src="../../../imagenes/logo.png">
        <div class='iconos-derecha'>
            <div class="icono-us user-menu-container">
                <a href="" class="user-icon-button">
                    <svg xmlns="http://www.w3.org/2000/svg"width="33"height="33"viewBox="0 0 24 24"fill="none"stroke="#000000"stroke-width="1.5"stroke-linecap="round"stroke-linejoin="round">
                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                    </svg>
                </a>
                <div class="dropdown-content adamina-regular">
                    <a href="admin/cerrarsesion.php">Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </header>
    <section class="contacto adamina-regular">
    
        <h1 class="card-title text-center mt-5 mb-3">Crear Producto</h1>
        <div>
            <?php
                if (isset($_SESSION['errores_registro']) && !empty($_SESSION['errores_registro'])) {
            ?>
                <div class="row container mx-auto justify-content-center">
                    <div class="col-12 col-lg-8 alert alert-danger" role="alert">
                        <?php 
                            foreach ($_SESSION['errores_registro'] as $error) {
                                echo "{$error}";
                            }
                        ?>
                    </div>
                </div>
            <?php
                // LIMPIAR LA SESIÓN DE ERRORES
                unset($_SESSION['errores_registro']); 
            }
            ?>
        </div>
            <form action="crearproducto.php" method="post" name="frmcontacto" class="formulario" enctype="multipart/form-data">
                <fieldset class='form-crear-producto'>
                    <div class="">
                        <div class="row container mx-auto justify-content-center">
                            <div class="col-12 col-lg-8">
                                <div class='mb-3 volver'>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                                    </svg>
                                    <a href="listaproducto.php" class='fs-4'>Volver</a>
                                </div>
                                <div class="card p-4 shadow">
                                    <div class="mb-4">
                                        <label for="nombre-producto" class="form-label">Nombre</label>
                                        <input type="text" class="form-control mb-2" id="nombre-producto" name="nombre-producto" placeholder="Ingrese el nombre del producto">
                                    </div>

                                    <div class="mb-4">
                                        <label for="descripcion" class="form-label">Descripción</label>
                                        <input type="text" class="form-control mb-2" id="descripcion" name="descripcion" placeholder="Ingrese una descripción">
                                    </div>

                                    <div class="mb-4">
                                        <label for="imagen">Imagen Principal: </label>
                                        <input type="file" id="imagen" name="imagen" class='mx-2' accept="image/jpeg,image/png" >
                                    </div>

                                    <div class="mb-4">
                                        <label for="imagenes_secundarias">Imágenes Adicionales: </label>
                                        <input type="file" id="imagenes_secundarias" name="imagenes_secundarias[]" class='mx-2' accept="image/jpeg,image/png" multiple> 
                                    </div>

                                    <div class="mb-3">
                                        <label for="categoria" class="form-label">Categoría: </label>
                                        <select name="categoria" id="categoria" class='mx-2' required>
                                            <option value="">-- Seleccione una categoría --</option>
                                            <?php
                                                while($categoria=mysqli_fetch_assoc($categorias)){?>
                                                <option
                                                    value='<?php echo $categoria['id_categoria']."' >".$categoria['nombre_categoria']."</option>";
                                            }
                                            ?>  
                                        </select>
                                    </div>

                                    <div class="mb-4">
                                        <label for="precio" class="form-label">Precio</label>
                                        <input type="double" class="form-control mb-2" id="precio" name="precio" placeholder="Ingrese el precio">
                                    </div>

                                    <div class="mb-4">
                                        <label for="stock" class="form-label">Stock</label>
                                        <input type="number" class="form-control mb-2" id="stock" name="stock" placeholder="Ingrese el stock">
                                    </div>

                                    <div class="botones col d-flex gap-5 pt-2 justify-content-center">
                                        <button type="submit" class="col-md-5 btn btn-success btn-registro">Crear
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-in-right" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0z"/>
                                                <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                                            </svg>
                                        </button>

                                        <button type="reset" class="col-md-5 btn btn-danger btn-limpiar">Limpiar
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
                                                <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div> 
                                
                                </div>
                            </div>
                        </div>
                </fieldset>        
            </form>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
   
</body>
</html>
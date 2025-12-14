<?php
    require '../../../includes/funciones.php';
    require '../../../includes/config/database.php';
    $db=conectarDB();
    $id=$_GET['id'];
    $id=filter_var($id,FILTER_VALIDATE_INT);
    if(!$id){
        header('Location: listaproducto.php');
    }
    $consulta="select * from productos where id_producto=$id";
    $resultado=mysqli_query($db,$consulta);
    $producto=mysqli_fetch_assoc($resultado);

    $errores=[];
    $nombre=$producto['nombre'];
    $descripcion=$producto['descripcion'];
    $imagenf=$producto['imagen'];
    $id_categoria=$producto['id_categoria'];
    $precio=$producto['precio'];
    $stock=$producto['stock'];

    $consulta_categoria="select nombre_categoria from categorias where id_categoria = $id_categoria";
    $resultado=mysqli_query($db,$consulta_categoria);
    $nombre_categoria=mysqli_fetch_assoc($resultado)['nombre_categoria']; 

    if (isset($_GET['eliminar_img'])) {
        $id_imagen_a_eliminar = filter_var($_GET['eliminar_img'], FILTER_VALIDATE_INT);
        $id_producto_actual = $id; 

    if ($id_imagen_a_eliminar) {

        $consulta_archivo = "SELECT ruta_imagen FROM imagenes_producto WHERE id_imagen = $id_imagen_a_eliminar AND id_producto = $id_producto_actual";
        $resultado_archivo = mysqli_query($db, $consulta_archivo);
        $imagen_secundaria = mysqli_fetch_assoc($resultado_archivo);
        
        if ($imagen_secundaria) {
            $ruta_archivo = '../../../imagenes/'.$nombre_categoria.'/'. $imagen_secundaria['ruta_imagen'];
            // Elimina el archivo del servidor
            if (file_exists($ruta_archivo)) {
                unlink($ruta_archivo);
            }
            // Elimina el registro de la base de datos
            $query_eliminar = "DELETE FROM imagenes_producto WHERE id_imagen = $id_imagen_a_eliminar";
            mysqli_query($db, $query_eliminar);
        }
        header("Location: actualizarproducto.php?id=$id_producto_actual");
        exit;
    }
}

    $consulta_imagenes_secundarias = "SELECT id_imagen, ruta_imagen FROM imagenes_producto WHERE id_producto = $id";
    $resultado_imagenes_secundarias = mysqli_query($db, $consulta_imagenes_secundarias);

    $consulta_categorias = "select * from categorias";
    $categorias = mysqli_query($db, $consulta_categorias);

    if(isset($_POST['nombre'])){
        $nombre=$_POST['nombre'];
        $descripcion=$_POST['descripcion'];    
        $imagen = $_FILES['imagen'] ?? null;
        $id_categoria = mysqli_real_escape_string($db,$_POST['categoria']);
        $precio=$_POST['precio'];
        $stock=mysqli_real_escape_string($db,$_POST['stock']);

    if (empty($nombre) || empty($descripcion) || empty($id_categoria) || empty($precio) || empty($stock)) {
        $errores[] = "Todos los campos son obligatorios. ";
    }
    if (!$imagenf && (!$imagen || $imagen['error'] !== UPLOAD_ERR_OK)) {
    $errores[] = "La imagen es obligatoria o hubo un error al subirla.";
    }

    if(empty($errores)){
        $carpetaimagenes='../../../imagenes/'.$nombre_categoria.'/';
        if(!is_dir($carpetaimagenes)){
            mkdir($carpetaimagenes);
        } 
        $nombreimagen='';
        if($imagen['name']){
            unlink($carpetaimagenes.$imagenf);
            $nombreimagen=md5(uniqid(rand(),true)).".jpg";      
            move_uploaded_file($imagen['tmp_name'],$carpetaimagenes.$nombreimagen);
        }
        else{
            $nombreimagen=$imagenf;
        }       
        $consultaupdate="UPDATE productos SET nombre='$nombre',descripcion='$descripcion',imagen='$nombreimagen',id_categoria=$id_categoria,precio=$precio,stock=$stock WHERE id_producto=$id";    
        $resultadoup=mysqli_query($db,$consultaupdate);
        if (isset($_FILES['imagenes_secundarias']) && is_array($_FILES['imagenes_secundarias']['name'])) {
        $archivos = $_FILES['imagenes_secundarias'];
        $num_imagenes = count($archivos['name']);
        
        for ($i = 0; $i < $num_imagenes; $i++) {
            if ($archivos['error'][$i] === UPLOAD_ERR_OK) {
                $nombre_temporal = $archivos['tmp_name'][$i];
                $extension = pathinfo($archivos['name'][$i], PATHINFO_EXTENSION);
                $nombre_imagen_secundaria = md5(uniqid(rand(), true)) . "." . $extension;
                // Mover y guardar en DB
                if (move_uploaded_file($nombre_temporal, $carpetaimagenes . $nombre_imagen_secundaria)) {
                    $query_secundaria = "INSERT INTO imagenes_producto (id_producto, ruta_imagen) VALUES ('$id', '$nombre_imagen_secundaria')";
                    mysqli_query($db, $query_secundaria);
                }
            }
        }
    }
        if($resultadoup){
            header('Location: listaproducto.php?resultado=2');
        }           
    }  
    if (!empty($errores)) {
        session_start();
        $_SESSION['errores_registro'] = $errores;
        header("Location: actualizarproducto.php?id=$id"); 
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
    </header>
    <main class="seccion">
        <h1 class='mt-5 text-center'>Actualizar Producto</h1>
        <?php
            if (isset($_SESSION['errores_registro']) && !empty($_SESSION['errores_registro'])) {
            ?>
                <div class="container mt-4">
                    <div class="alert alert-danger" role="alert">
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
    <form class="formulario" method="post" enctype="multipart/form-data">
    <fieldset class='form-actualizar'>
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
                            <label for="nombre" class="form-label">Nombre de producto</label>
                            <input type="text" class="form-control mb-2" id="nombre" name="nombre" placeholder="" value="<?php echo $nombre; ?>">
                        </div>

                        <div class="mb-4">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <input type="text" class="form-control mb-2" id="descripcion" name="descripcion" placeholder="" value="<?php echo $descripcion; ?>">
                        </div>

                        <div class="mb-4">
                            <label for="imagen" class="form-label">Imagen Principal: </label>
                            <input type="file" id="imagen" name="imagen" class='mx-2' accept="image/jpeg,image/png" value="<?php echo $imagenf; ?>">
                        </div>
                        <img src="../../../imagenes/<?php echo $nombre_categoria;?>/<?php echo $imagenf;?>" class="imagen-producto-tabla mb-4">
                        <div class="mb-4">
                            <label for="imagenes_secundarias" class="form-label fw-bold">Agregar Imágenes al Carrusel</label>
                            <p class="text-muted">Selecciona una o más imágenes nuevas para añadir al carrusel.</p>
                            <input type="file" 
                                id="imagenes_secundarias" 
                                name="imagenes_secundarias[]" 
                                class='mx-2' 
                                accept="image/jpeg,image/png" 
                                multiple>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Imágenes Actuales del Carrusel</label>
                            <div class="row row-cols-2 row-cols-md-3 g-4">
                                <?php while($img_sec = mysqli_fetch_assoc($resultado_imagenes_secundarias)): ?>
                                    <div class="col">
                                        <div class="card h-100">
                                            <img src="../../../imagenes/<?php echo $nombre_categoria;?>/<?php echo $img_sec['ruta_imagen'];?>" class="card-img-top img-fluid p-2" style="height: 150px; object-fit: cover;" alt="Imagen secundaria">
                                            <div class="card-footer text-center">
                                                <a href="?id=<?php echo $id; ?>&eliminar_img=<?php echo $img_sec['id_imagen']; ?>" class="btn btn-sm btn-outline-danger">Eliminar</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="categoria" class="form-label">Categoria </label>
                            <select name="categoria" id="categoria" class='mx-2' required>
                                <option value="">-- Seleccione --</option>
                                <?php
                                    while($categoria=mysqli_fetch_assoc($categorias)){
                                        if($id_categoria==$categoria['id_categoria']){
                                            echo "<option value=".$categoria['id_categoria']." selected>".$categoria['nombre_categoria']."</option>";
                                        }
                                        else{
                                            echo "<option value=".$categoria['id_categoria'].">".$categoria['nombre_categoria']."</option>";
                                        }                
                                    }
                                    ?>    
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="precio" class="form-label">Precio</label>
                            <input type="double" class="form-control mb-2" id="precio" name="precio" placeholder="Ingrese el precio" value="<?php echo $precio; ?>">
                        </div>

                        <div class="mb-4">
                            <label for="stock" class="form-label">Stock</label>
                            <input type="number" class="form-control mb-2" id="stock" name="stock" placeholder="Ingrese el stock" value="<?php echo $stock; ?>">
                        </div>

                        <div class="botones col d-flex gap-5 pt-4 justify-content-center">
                            <button type="submit" class="col-md-5 btn btn-success" >Actualizar
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
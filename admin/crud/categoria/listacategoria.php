<?php
    require '../../../includes/funciones.php';
    require '../../../includes/config/database.php';
    $db=conectarDB();
    $consulta= 'select * from categorias';
    $categorias=mysqli_query($db,$consulta);
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
                <a href="" class="user-icon-button text-dark">
                    <svg xmlns="http://www.w3.org/2000/svg" width="33" height="33" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                        <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                    </svg>
                </a>
                    <div class="dropdown-content adamina-regular">
                        <a href="../../panel.php">Panel de Administración</a>
                        <a href="../../cerrarsesion.php">Cerrar Sesión</a>
                    </div>
            </div>
        </div>
    </header>
    <main class="seccion">
        <h1 class='text-center mt-5'>Administrar Categorías</h1>
        <?php 
            $resultado=$_GET['resultado']??null;
            if ($resultado==1) {
                echo "<div class='text-center col-6 mx-auto py-2 text-success fw-bold fs-5' id='success'>¡Categoría creada correctamente!</div>";
            }
            elseif ($resultado== 2) {
                echo "<div class='text-center col-6 mx-auto py-2 text-success fw-bold fs-5' id='success'>¡Categoría actualizada correctamente!</div>";
            }
            elseif ($resultado== 3) {
                echo "<div class='text-center col-6 mx-auto py-2 text-success fw-bold fs-5' id='success'>¡Categoría eliminada correctamente!</div>";
            }
        ?>
        <div class='col container'>
            <div class='col-3 my-3'>
                    <a href="crearcategoria.php" class="text-center boton-enviar p-2">Registrar Categoría</a> 
            </div>
            <div class="table-responsive">
                <table class="table table-striped tabla-categorias border">
                    <thead>
                        <tr class='fw-bold'>
                            <td>Id</td>
                            <td>Nombre</td>
                            <td>Descripción</td>
                            <td class='text-center'>Acciones</td>
                        </tr>
                    </thead>
                    <tbody class='tabla-body'>
                        <?php
                            while($categoria=mysqli_fetch_assoc($categorias)){
                                echo "<tr>
                                <td >".$categoria['id_categoria']."</td>
                                <td >".$categoria['nombre_categoria']."</td>
                                <td >".$categoria['descripcion']."</td>
                                <td class='d-flex flex-column my-auto'> 
                                    <a href=actualizarcategoria.php?id=".$categoria['id_categoria']." class='enlace-borde text-center'>Actualizar</a>                  
                                    <a href=borrarcategoria.php?id=".$categoria['id_categoria']." class='enlace-borde mt-1 text-center'>Eliminar</a>
                                </td>
                                </tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
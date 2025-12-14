<?php
require '../includes/funciones.php';
require '../includes/config/database.php';
$db=conectarDB();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Adamina&display=swap" rel="stylesheet">
</head>
<body class='fondo adamina-regular'>
    <header id='contenedor' class="d-flex align-items-center justify-content-center">
        <img src="../imagenes/logo.png">
        <div class='iconos-derecha'>
            <div class="icono-us user-menu-container">
                <a href="" class="user-icon-button text-dark">
                    <svg xmlns="http://www.w3.org/2000/svg" width="33" height="33" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                        <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                    </svg>
                </a>
                    <div class="dropdown-content adamina-regular">
                        <a href="cerrarsesion.php">Cerrar Sesión</a>
                    </div>
            </div>
        </div>
        
    </header>
    <h1 class='py-5 text-center'>Panel de Administración</h1>
    <div class='pt-2 px-5 volver'>
        <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
        </svg>
          <a href="../index.php" class='fs-4'>Volver a la página principal</a>
    </div>
        <div class='panel-admin row mx-auto pt-5 '>
            <?php if($_SESSION['rol'] === 'admin') {?>
            <div class='tablas col-12 col-md-3 p-3 m-2 mx-auto'>
                <div class='d-flex flex-column align-items-center justify-content-center'>
                    <img src="../imagenes/usuarios.png" class='img-fluid p-2'>
                    <a class='fs-3' href="crud/usuario/listausuario.php">Usuarios</a>
                </div>    
            </div>
        <?php } ?>
        <div class='tablas col-12 col-md-3 p-3 m-2 mx-auto'>
            <div class='d-flex flex-column align-items-center'>
                <img src='../imagenes/productos.png'class='img-fluid'>
                <a class='fs-3'href="crud/producto/listaproducto.php">Productos</a> 
            </div> 
        </div>
        <div class='tablas col-12 col-md-3 p-3 m-2 mx-auto'>
            <div class='d-flex flex-column align-items-center'>
                <img src='../imagenes/categorias.png'class='img-fluid'>
                <a class='fs-3'href="crud/categoria/listacategoria.php">Categorias</a>
            </div>
        </div>
    </div>
    
</body>
</html>
  

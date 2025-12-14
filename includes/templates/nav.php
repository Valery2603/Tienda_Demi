<?php 
    require_once 'includes/config/database.php';
    $db=conectarDB();
    $consulta_categoria="select * from categorias";
    $categorias=mysqli_query($db,$consulta_categoria);
?>
<nav class="mx-auto menu d-flex justify-content-center align-items-center fondo-blanco adamina-regular navbar-light ">
    <a href="index.php" class="">Inicio </a>
    <a href="Nosotros.php" class=""> Nosotros </a>
    <a class="nav-link dropdown-toggle " href="#" id="navbarScrollingDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="true">Tienda</a>
        <ul class="dropdown-menu" aria-labelledby="navbarScrollingDropdown">
        <?php while ($categoria=mysqli_fetch_assoc($categorias)) {?>
            <li>
                <?php $slug_categoria = str_replace(' ', '-', $categoria['nombre_categoria']);?>
                <a class="dropdown-item" href="Tienda.php?nombre=<?php echo $slug_categoria ?>&id=<?php echo $categoria['id_categoria']?>">
                    <?php echo $categoria['nombre_categoria']?>
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
        <?php } ?>
        </ul> 
    <a href="Contacto.php" class=""> Contacto </a>
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Blog</a>
        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="Tips.php">Tips</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="Tendencias.php">Tendencias</a></li>
        </ul> 
</nav>
<?php
    require 'includes/funciones.php';
    incluirTemplates('header');
    incluirTemplates('nav');
    $db=conectarDB();
    $consulta='SELECT p.id_producto, p.nombre, p.imagen, c.nombre_categoria 
            FROM productos p INNER JOIN categorias c ON p.id_categoria = c.id_categoria WHERE tendencia=1';
    $resultado=mysqli_query($db,$consulta);
?>
        <section class="panel d-flex align-items-center justify-content-center adamina-regular">
            <img src="imagenes/Panel.png">
            <div>
                <p class='text-center m-1 text-light'>¡COMPRA CON EL 15% DE DESCUENTO EN TODA LA TIENDA!</p>
             </div>
        </section>
    <main class="container adamina-regular pb-4">
        <h1 class='py-5 text-center'>Productos en tendencia</h1>
        <section class="row">
            <?php while ($producto=mysqli_fetch_assoc($resultado)) {
                echo "
                <article class='col-6 col-xl-4 px-3 pb-3'>
                    <img src='imagenes/".$producto['nombre_categoria']."/".$producto['imagen']."' class='img-fluid'>
                    <a href='Producto.php?id=".$producto['id_producto']."' class='boton-compra text-center' >COMPRA AQUÍ</a> 
                </article>";
                
                }
            ?>      
        </section>
    </main>
    <?php 
        incluirTemplates('footer'); 
    ?>
</body>
</html>
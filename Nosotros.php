<?php
    require 'includes/funciones.php';
    incluirTemplates('header');
    incluirTemplates('nav');
?>
    <main class="py-5 container adamina-regular">
        <h1 class='text-center pb-5'>Nosotros</h1>
        <section class="row">
            <div class="col-12 col-md-5 pb-4 mx-auto">
                <img src="imagenes/dueña.jpg" class="img-fluid"><br><br>
                <h3>Valentina Lauder</h3>
                <h5>Dueña de la marca</h5>
            </div>
            <div class="col-12 col-md-5 pb-2 mx-auto d-flex align-items-center justify-content-center texto-dueña">
                <p class='justificado'>"Mi objetivo es ofrecerte accesorios que no solo sean bonitos, sino que también te empoderen a expresar tu individualidad y a sentirte segura de ti misma en cada momento. Cada artículo ha sido elegido pensando en la mujer de hoy: fuerte, creativa y con un estilo único".</p><br>
            </div> 
        </section>
        <hr class="py-3">
        <section class="row mision-vision ">
            <div class='col-12 col-lg-5 pb-5 mx-auto'>
                <div class="mx-auto">
                    <h2 class='text-center'>Misión</h2><br>
                    <p class='pb-4 justificado'>Seleccionar y ofrecer accesorios de vestir para mujeres que combinan diseño, calidad y versatilidad, buscando facilitar la creación de looks auténticos y empoderadores para cada faceta de la vida de nuestras clientas.</p>
                </div>
                <div class="col-8 pb-2 mx-auto">
                    <img src="imagenes/mision.png" class='img-fluid'>
                </div>
            </div>
            <div class='col-12 col-lg-5 mx-auto'>
                <div class="mx-auto">
                    <h2 class='text-center'>Visión</h2><br>
                    <p class='pb-4 justificado'>Construir una comunidad de mujeres que aman la moda y los accesorios como una forma de expresión personal, convirtiéndonos en su destino preferido para encontrar piezas que reflejen su estilo único.</p>   
                </div>
                <div class="col-8 mx-auto">
                    <img src="imagenes/vision.png" class='img-fluid'>
                </div>
            </div>
        </section>    
    </main>
    <?php 
        incluirTemplates('footer'); 
    ?>
</body>
</html>
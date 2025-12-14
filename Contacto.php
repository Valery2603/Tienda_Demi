<?php
    require 'includes/funciones.php';
    incluirTemplates('header');
    incluirTemplates('nav');
?>
    <main class="py-5 container adamina-regular">
        <h1 class='text-center pb-5'>Contacto</h1>
        <section class="contacto row formulario">
            <div class='col-12 col-lg-6 mx-auto pb-4'>
                <form action="#" method="get" class="">
                    <h4>Si tienes alguna consulta, déjanos tu mensaje y en breve te contestaremos.</h4>
                    <fieldset>
                        <div class="contenedor-controles">
                            <div class="campo fw-semibold">
                                <label for="nombres">Nombres y Apellidos </label>
                                <input class="input-text" type="text" name="nombres" size="20" required>
                            </div>
                            <div class="campo fw-semibold">
                                <label for="celular">Celular </label>
                                <input class="input-text" type="phone" name="celular" size="20" required>
                            </div>
                            <div class="campo fw-semibold">
                                <label for="correo">Correo </label>
                                <input class="input-text" type="email" name="correo" size="20" required>
                            </div>
                            <div class="campo fw-semibold">
                                <label for="comentario">Comentario </label>
                                <textarea class="input-text" name="comentario" id="comm"></textarea>
                            </div>
                        </div>
                        <div>
                            <button type="submit" class="boton-enviar p-2">Enviar
                        </div>
                        </fieldset>
                </form>
            </div>
            <div class='col-12 col-lg-6 mx-auto contenedor-canales'>
                <div>
                    <h4 class='pb-5'>Aquí encontrarás nuestros canales de atención para poder ayudarte.</h4>
                    <div class="">
                        <svg xmlns="http://www.w3.org/2000/svg"width="32"height="32"viewBox="0 0 24 24"fill="none"stroke="#000000"stroke-width="1.5"stroke-linecap="round"stroke-linejoin="round">
                            <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                            <path d="M3 7l9 6l9 -6" />
                        </svg> tiendademi@gmail.com
                        
                    </div>
                    <div class="">
                        <svg xmlns="http://www.w3.org/2000/svg"width="32"height="32"viewBox="0 0 24 24"fill="none"stroke="#000000"stroke-width="1.5"stroke-linecap="round"stroke-linejoin="round">
                            <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" />
                        </svg>+51 947-456-785
                        
                    </div>
                    <div class="">
                        <svg xmlns="http://www.w3.org/2000/svg"width="32"height="32"viewBox="0 0 24 24"fill="none"stroke="#000000"stroke-width="1.5"stroke-linecap="round"stroke-linejoin="round">
                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                            <path d="M12 12l-3 2" />
                            <path d="M12 7v5" />
                        </svg> Lunes a Sabádo: 10:00 am - 20:00 pm
                    </div>
                </div>
            </div>
            <div class="pt-4">
                <iframe class="col-12 mx-auto" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3903.5805801665856!2d-77.0500635241872!3d-11.934249839817578!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9105d037267e3561%3A0x53e80e2a530323a4!2sAv.%20T%C3%BApac%20Amaru%203833%2C%20Comas%2015312!5e0!3m2!1ses-419!2spe!4v1754170295242!5m2!1ses-419!2spe" width="1250" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </section>
        
    </main>
    <?php 
        incluirTemplates('footer'); 
    ?>
</body>
</html>
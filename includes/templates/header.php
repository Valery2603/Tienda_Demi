<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
        // Genera un título de respaldo a partir del nombre del archivo 
        $titulo_respaldo = ucfirst(str_replace('.php', '', basename($_SERVER['PHP_SELF'] ?? 'Inicio')));

        // Usa el título de la variable, o el título de respaldo si la variable no fue definida
        $titulo_final = $titulo ?? $titulo_respaldo;

        // Caso especial para la página principal
        if (strtolower($titulo_final) === 'index' || strtolower($titulo_final) === 'inicio') {
            $titulo_final = 'Inicio | Demi';
        }
    ?>
    <title><?php echo $titulo_final; ?></title>
    
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Adamina&display=swap" rel="stylesheet">
</head>
<body class='fondo'>
    <header id="contenedor" class='d-flex align-items-center justify-content-center'>
        <img src="imagenes/logo.png">
    <div class="iconos-izquierda">
        <div class="icono-fb">
            <a href="https://www.facebook.com" target="_blank">
                <svg xmlns="http://www.w3.org/2000/svg"width="33"height="33"viewBox="0 0 24 24"fill="none"stroke="#000000"stroke-width="1.5"stroke-linecap="round"stroke-linejoin="round">
                    <path d="M7 10v4h3v7h4v-7h3l1 -4h-4v-2a1 1 0 0 1 1 -1h3v-4h-3a5 5 0 0 0 -5 5v2h-3" />
                </svg>
            </a>
        </div>
        <div class="icono-ig">    
            <a href="https://www.instagram.com" target="_blank">
                <svg xmlns="http://www.w3.org/2000/svg"width="33"height="33"viewBox="0 0 24 24"fill="none"stroke="#000000"stroke-width="1.5"stroke-linecap="round"stroke-linejoin="round">
                    <path d="M4 4m0 4a4 4 0 0 1 4 -4h8a4 4 0 0 1 4 4v8a4 4 0 0 1 -4 4h-8a4 4 0 0 1 -4 -4z"/>
                    <path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/>
                    <path d="M16.5 7.5l0 .01"/>
                </svg>
            </a>
        </div>
        <div class="icono-yt">
            <a href="https://www.youtube.com" target="_blank">
                <svg xmlns="http://www.w3.org/2000/svg"width="33"height="33"viewBox="0 0 24 24"fill="none"stroke="#000000"stroke-width="1.5"stroke-linecap="round"stroke-linejoin="round">
                    <path d="M2 8a4 4 0 0 1 4 -4h12a4 4 0 0 1 4 4v8a4 4 0 0 1 -4 4h-12a4 4 0 0 1 -4 -4v-8z" />
                    <path d="M10 9l5 3l-5 3z" />
                </svg>
            </a>
        </div>
    </div>
    <div class="iconos-derecha">
        <div class="icono-sh">
            <a href="#">    
                <svg xmlns="http://www.w3.org/2000/svg"width="33"height="33"viewBox="0 0 24 24"fill="none"stroke="#000000"stroke-width="1.5"stroke-linecap="round"stroke-linejoin="round">
                    <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                    <path d="M21 21l-6 -6" />
                </svg> 
            </a>
        </div>
        <div class="icono-cr">
            <a href="Carrito.php">
                <svg xmlns="http://www.w3.org/2000/svg"width="33"height="33"viewBox="0 0 24 24"fill="none"stroke="#000000"stroke-width="1.5"stroke-linecap="round"stroke-linejoin="round">
                    <path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                    <path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                    <path d="M17 17h-11v-14h-2" />
                    <path d="M6 5l14 1l-1 7h-13" />
                </svg>
            </a>
        </div>
        <?php
        if (isset($_SESSION['id'])) {
        ?>
            <div class="icono-us user-menu-container">
                <a href="" class="user-icon-button text-dark">
                    <svg xmlns="http://www.w3.org/2000/svg" width="33" height="33" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                        <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                    </svg>
                </a>
                    <div class="dropdown-content adamina-regular">
                        <a href="admin/panel.php">Panel de Administración</a>
                        <a href="admin/cerrarsesion.php">Cerrar Sesión</a>
                    </div>
            </div>
            
        <?php
            } else {
        ?>
                <a href="admin/login.php" class="user-icon-button text-dark">
                    <svg xmlns="http://www.w3.org/2000/svg" width="33" height="33" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                        <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                    </svg>
                </a>
        <?php } ?>
    </div>
</header>
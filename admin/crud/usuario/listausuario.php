<?php
    require '../../../includes/funciones.php';
    require '../../../includes/config/database.php';
    $db=conectarDB();
    $consulta= 'SELECT 
                u.id_usuario,
                u.email, 
                u.nombre, 
                u.password, 
                r.nombre_rol 
              FROM 
                usuarios u 
              INNER JOIN 
                roles r ON u.id_rol = r.id_rol';
    $usuarios=mysqli_query($db,$consulta);
    $usuarios_por_pagina = 5; 
    $pagina_actual = $_GET['page'] ?? 1;
    $pagina_actual = filter_var($pagina_actual, FILTER_VALIDATE_INT);
    // Asegura que la página sea un número válido y mayor a 1
    if (!$pagina_actual || $pagina_actual < 1) {
        $pagina_actual = 1;
    }
    // Calcula el OFFSET (desde dónde empezar a traer los registros)
    $offset = ($pagina_actual - 1) * $usuarios_por_pagina;

    $where_clause = '';
    $busqueda = $_GET['search'] ?? '';
    $busqueda_sql = mysqli_real_escape_string($db, $busqueda);

    if (!empty($busqueda)) {
        $where_clause = " WHERE (u.nombre LIKE '%{$busqueda_sql}%')";
    }
    // Obtiene el número total de productos (para calcular el total de páginas)
    $consulta_total = "SELECT COUNT(*) as total FROM usuarios u{$where_clause}";
    $resultado_total = mysqli_query($db, $consulta_total);
    $total_usuarios = mysqli_fetch_assoc($resultado_total)['total'];
    // Calcula el total de páginas
    $total_paginas = ceil($total_usuarios / $usuarios_por_pagina);
    $search_param = !empty($busqueda) ? '&search='.urlencode($busqueda) : '';

    // --- 3. Consulta para obtener los USUARIOS de la página actual ---
    $consulta_base = 'SELECT 
                        u.id_usuario,
                        u.email, 
                        u.nombre, 
                        u.password, 
                        r.nombre_rol 
                      FROM 
                        usuarios u 
                      INNER JOIN 
                        roles r ON u.id_rol = r.id_rol';

    // Construye la consulta final con paginación y orden
    $consulta_final = "{$consulta_base} {$where_clause} ORDER BY u.id_usuario DESC LIMIT {$usuarios_por_pagina} OFFSET {$offset}";
    $usuarios = mysqli_query($db, $consulta_final);
    
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
        <h1 class='text-center mt-5'>Administrar Usuarios</h1>
        <?php 
            $resultado=$_GET['resultado']??null;
            if ($resultado==1) {
                echo "<div class='text-center col-6 mx-auto py-2 text-success fw-bold fs-5' id='success'>¡Usuario creado correctamente!</div>";
            }
            elseif ($resultado== 2) {
                echo "<div class='text-center col-6 mx-auto py-2 text-success fw-bold fs-5' id='success'>¡Usuario actualizado correctamente!</div>";
            }
            elseif ($resultado== 3) {
                echo "<div class='text-center col-6 mx-auto py-2 text-success fw-bold fs-5' id='success'>¡Usuario eliminado correctamente!</div>";
            }
        ?>
        <div class='col container'>
            <div class="row justify-content-end align-items-center my-3">
                <div class="col-4">
                    <form action="" method="GET" class="input-group">
                        <input type="hidden" name="page" value="1"> 
                        <input 
                            type="text" 
                            name="search" 
                            class="form-control" 
                            placeholder="Buscar..." 
                            value="<?php echo htmlspecialchars($busqueda) ?>" 
                        >
                        <button type="submit" class="btn btn-dark">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                            </svg>
                        </button>
                        <?php if (!empty($busqueda)){ ?>
                            <button type="button" class="btn btn-clear btn-limpiar-busqueda text-white bg-danger" onclick="limpiarBusqueda()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-x-square" viewBox="0 0 16 16">
                                    <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                </svg> 
                            </button>
                        <?php } ?>
                    </form>
                </div>
            </div>
            <div class='col-3 my-3'>
                <a href="crearusuario.php" class="text-center boton-enviar p-2">Registrar Usuario</a> 
            </div>
            <div class="table-responsive">
                <table class="table table-striped border">
                    <thead>
                        <tr class='fw-bold'>
                            <td>Id</td>
                            <td>Nombre</td>
                            <td>Email</td>
                            <td>Contraseña</td>
                            <td>Rol</td>
                            <td class='text-center'>Acciones</td>
                        </tr>
                    </thead>
                    <tbody class='tabla-body'>
                        <?php
                            while($usuario=mysqli_fetch_assoc($usuarios)){
                                echo "<tr><td>".$usuario['id_usuario']."</td><td>".$usuario['nombre']."</td>
                                <td>".$usuario['email']."</td><td>".$usuario['password']."</td><td>".$usuario['nombre_rol']."</td>
                                <td class='d-flex flex-column'>                   
                                    <a href=borrarusuario.php?id=".$usuario['id_usuario']." class='enlace-borde my-1 text-center'>Eliminar</a>
                                    <a href=actualizarusuario.php?id=".$usuario['id_usuario']." class='enlace-borde my-1 text-center'>Actualizar</a>
                                    
                                </td>
                                </tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php if ($total_paginas > 1 || !empty($busqueda)){ ?>
            <nav aria-label="Paginacion de productos" class="mt-1">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php echo ($pagina_actual <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $pagina_actual - 1; ?><?php echo $search_param; ?>">Anterior</a>
                    </li>
            <?php for ($i = 1; $i <= $total_paginas; $i++){ ?>
                    <li class="page-item <?php echo ($i == $pagina_actual) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo $search_param; ?>"><?php echo $i; ?></a>
                    </li>
            <?php } ?>
                    <li class="page-item <?php echo ($pagina_actual >= $total_paginas) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $pagina_actual + 1; ?><?php echo $search_param; ?>">Siguiente</a>
                    </li>
                </ul>
            </nav>
            <?php } ?>
        </div>
    </main>
    <script>
        function limpiarBusqueda() {
            // Crea un nuevo objeto URL usando la dirección actual de la página.
            const url = new URL(window.location.href);
            // Elimina el parámetro 'search' de la URL.
            url.searchParams.delete('search');
            // Redirige a la nueva URL (sin el parámetro 'search').
            window.location.href = url.toString();
        }
    </script>
</body>
</html>
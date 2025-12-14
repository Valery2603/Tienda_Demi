<?php
    require '../../includes/funciones.php';
    require '../../includes/config/database.php';
    $db=conectarDB();

    $consulta_roles = "select * from roles";
    $roles = mysqli_query($db, $consulta_roles);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
    $apellido = filter_input(INPUT_POST, 'apellido', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $contrasena = $_POST['contrasena']; 
    $repetir_contrasena = $_POST['confirmar_contrasena'];
    $celular = filter_input(INPUT_POST, 'celular', FILTER_SANITIZE_NUMBER_INT);
    $direccion = $_POST['direccion']; 
    $fecha_registro = date('Y-m-d'); 
    $errores = [];

    if (empty($nombre) || empty($apellido) || empty($email) || empty($contrasena) || empty($repetir_contrasena)) {
        $errores[] = "Todos los campos son obligatorios.";
    }
    if ($contrasena !== $repetir_contrasena) {
        $errores[] = "Las contraseñas no coinciden.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El formato del correo electrónico no es válido.";
    }

    $query_verificar = "SELECT id_cliente FROM clientes WHERE email_cliente = ?";
    $stmt_verificar = $db->prepare($query_verificar);
    $stmt_verificar->bind_param("s", $email);
    $stmt_verificar->execute();
    $stmt_verificar->store_result();
    
    if ($stmt_verificar->num_rows > 0) {
        $errores[] = "El usuario ya está registrado.";
    }
    $stmt_verificar->close();
    
    if (empty($errores)) {
        $contrasena_hasheada = password_hash($contrasena, PASSWORD_DEFAULT);      
        $query_insertar = "INSERT INTO clientes (nombre, apellido, email_cliente, password_cliente, celular, direccion, fecha_registro) 
                           VALUES (?, ?, ?, ?,?,?,?)";                         
        $stmt_insertar = $db->prepare($query_insertar);
        $stmt_insertar->bind_param("ssssiss", $nombre,$apellido, $email, $contrasena_hasheada, $celular, $direccion, $fecha_registro);
        
        if ($stmt_insertar->execute()) {
            $stmt_insertar->close();
            header("Location: ../../index.php"); 
            exit();
        } else {
            $errores[] = "Error al intentar registrar el usuario: " . $stmt_insertar->error;
        }
        $stmt_insertar->close();
    }

    if (!empty($errores)) {
        session_start();
        $_SESSION['errores_registro'] = $errores;
        header("Location: crearcliente.php"); 
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
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/estilos.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Adamina&display=swap" rel="stylesheet">
</head>
<body class='fondo adamina-regular'>
    <header id='contenedor' class="d-flex align-items-center justify-content-center">
        <img src="../../imagenes/logo.png">
    </header>
    <section class="contacto adamina-regular">
        <h1 class="card-title text-center mt-5 mb-3">Crear Cuenta</h1>
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
                //LIMPIAR LA SESIÓN DE ERRORES
                unset($_SESSION['errores_registro']); 
            }
            ?>
            <form action="#" method="post" name="frmcontacto" class="formulario">
                <fieldset class='form-crear-usuario'>
                    <div class="pb-5">
                        <div class="row container mx-auto justify-content-center">
                            <div class="col-12 col-lg-8">
                
                                <div class="card p-4 shadow">
                                    <div class="mb-4 div-relativo">
                                        <label for="nombre" class="form-label">Nombre</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingrese su nombre">
                                    </div>

                                    <div class="mb-4 div-relativo">
                                        <label for="apellido" class="form-label">Apellido</label>
                                        <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Ingrese su apellido">
                                    </div>

                                    <div class="mb-4 pt-2 div-relativo">
                                        <label for="celular" class="form-label">Celular</label>
                                        <input type="text" class="form-control" id="celular" name="celular" placeholder="Ingrese su celular">
                                    </div>

                                    <div class="mb-4 pt-2 div-relativo">
                                        <label for="direccion" class="form-label">Dirección</label>
                                        <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Ingrese su dirección">
                                    </div>

                                    <div class="mb-4 pt-2 div-relativo">
                                        <label for="email" class="form-label">Correo electrónico</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Ingrese su correo electrónico">
                                    </div>

                                    <div class="mb-4 pt-2 div-relativo">
                                        <label for="password" class="form-label">Contraseña</label>
                                        <input type="password" class="form-control" id="contrasena" name="contrasena" placeholder="Ingrese su contraseña">
                                    </div>

                                    <div class="mb-4 pt-2 div-relativo">
                                        <label for="password" class="form-label">Confirmar Contraseña</label>
                                        <input type="password" class="form-control " id="confirmar_contrasena" name="confirmar_contrasena" placeholder="Ingrese nuevamente su contraseña">
                                    </div>

                                    <div class="botones col d-flex gap-5 pt-4 justify-content-center">
                                        <button type="submit" class="col-md-5 btn btn-success btn-registro" >Crear
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
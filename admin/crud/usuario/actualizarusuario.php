<?php
require '../../../includes/funciones.php';
$id=$_GET['id'];
$id=filter_var($id,FILTER_VALIDATE_INT);
if(!$id){
    header('Location: listausuario.php');
}
require '../../../includes/config/database.php';
$db=conectarDB();
$consulta="select * from usuarios where id_usuario=$id";
$resultado=mysqli_query($db,$consulta);
$usuario=mysqli_fetch_assoc($resultado);

$consulta_roles = "select * from roles";
$roles = mysqli_query($db, $consulta_roles);

$errores=[];
    $nombre=$usuario['nombre'];
    $email=$usuario['email'];
    $contrasena=$usuario['password'];
    $repetir_contrasena=$usuario['password'];
    $id_rol=$usuario['id_rol'];

if(isset($_POST['nombre'])){
    $nombre=mysqli_real_escape_string($db,$_POST['nombre']);
    $email=mysqli_real_escape_string($db,$_POST['email']);    
    $contrasena=mysqli_real_escape_string($db,$_POST['contrasena']);
    $repetir_contrasena = mysqli_real_escape_string($db,$_POST['contrasena']);
    $id_rol=mysqli_real_escape_string($db,$_POST['rol']);

    if (empty($nombre) || empty($email) || empty($contrasena) || empty($repetir_contrasena) || empty($id_rol)) {
        $errores[] = "Todos los campos son obligatorios.";
    }
    if ($contrasena !== $repetir_contrasena) {
        $errores[] = "Las contraseñas no coinciden.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El formato del correo electrónico no es válido.";
    }
    
    if(empty($errores)){
        $contrasena_hasheada = password_hash($contrasena, PASSWORD_DEFAULT);       
        $consultaupdate="UPDATE usuarios SET nombre='$nombre',email='$email',password='$contrasena_hasheada',id_rol=$id_rol WHERE id_usuario=$id";    
        $resultadoup=mysqli_query($db,$consultaupdate);
        if($resultadoup){
            header('Location: listausuario.php?resultado=2');
        }           
    }
    if (!empty($errores)) {
        session_start();
        $_SESSION['errores_registro'] = $errores;
        header("Location: actualizarusuario.php?id=$id"); 
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
        <h1 class='mt-5 mb-3 text-center'>Actualizar Usuario</h1>
        <?php
                if (isset($_SESSION['errores_registro']) && !empty($_SESSION['errores_registro'])) {
            ?>
            <div class="container mb-2">
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
                    <div class='mb-4 volver fs-4'>
                        <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                        </svg>
                        <a href="listausuario.php">Volver</a>
                    </div>
                    <div class="card p-4 shadow">
                        <div class="mb-4">
                            <label for="nombre" class="form-label">Nombre completo</label>
                            <input type="text" class="form-control mb-2" id="nombre" name="nombre" placeholder="Ingrese su nombre completo" value="<?php echo $nombre; ?>">
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control mb-2" id="email" name="email" placeholder="Ingrese su correo" value="<?php echo $email; ?>">
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control mb-2" id="contrasena" name="contrasena" placeholder="Ingrese su contraseña" value="<?php echo $contrasena; ?>">
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">Repetir Contraseña</label>
                            <input type="password" class="form-control mb-2" id="confirmar_contrasena" name="confirmar_contrasena" placeholder="Ingrese nuevamente" value="<?php echo $contrasena; ?>">
                        </div>

                        <div class="mb-4">
                            <label for="rol" class="form-label">Rol:</label>
                            <select name="rol" id="rol" class='mx-2' required>
                                <option value="">Seleccione un rol</option>
                                <?php
                                    while($rol=mysqli_fetch_assoc($roles)){
                                        if($id_rol==$rol['id_rol']){
                                            echo "<option value=".$rol['id_rol']." selected>".$rol['nombre_rol']."</option>";
                                        }
                                        else{
                                            echo "<option value=".$rol['id_rol'].">".$rol['nombre_rol']."</option>";
                                        }                
                                    }
                                    ?>    
                            </select>
                        </div>

                        <div class="botones col d-flex gap-5 pt-4 justify-content-center">
                            <button type="submit" class="col-md-5 btn btn-success">Actualizar
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-in-right" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0z"/>
                                    <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                                </svg>
                            </button>

                            <button type="reset" class="col-md-5 btn btn-danger" onclick="limpiarFormulario()">Limpiar
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
                                    <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/>
                                </svg>
                            </button>
                            <script>
                                function limpiarFormulario() {
                                    // Selecciona el formulario por su clase o ID y lo resetea
                                    const formulario = document.querySelector('.formulario');
                                    formulario.reset(); // Esto restaura a valores iniciales
                                    
                                    // Esto fuerza a que todos los inputs queden vacíos
                                    const inputs = formulario.querySelectorAll('input');
                                    inputs.forEach(input => input.value = '');
                                    
                                    const selects = formulario.querySelectorAll('select');
                                    selects.forEach(select => select.selectedIndex = 0);
                                }
                            </script>
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
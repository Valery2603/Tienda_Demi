<?php
  require '../includes/funciones.php';
  require '../includes/config/database.php';
  $db=conectarDB();
  $error_mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = trim($_POST['usuario'] ?? ''); 
    $contrasena_ingresada = $_POST['contrasena'] ?? '';

    if (empty($email) || empty($contrasena_ingresada)) {
        $error_mensaje = "Debe llenar los campos.";
    } 
    
    if (empty($error_mensaje)) {
    $consulta = "SELECT 
                u.id_usuario, 
                u.nombre, 
                u.password, 
                r.nombre_rol 
              FROM 
                usuarios u 
              INNER JOIN 
                roles r ON u.id_rol = r.id_rol 
              WHERE 
                u.email = ?";
    
    $stmt = $db->prepare($consulta);
    $stmt->bind_param("s", $email); 
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows == 1) {
        $usuario_db = $resultado->fetch_assoc();
      
        if (password_verify($contrasena_ingresada, $usuario_db['password'])) {
  
            $_SESSION['id']    = $usuario_db['id_usuario'];
            $_SESSION['nombre'] = $usuario_db['nombre'];
            $_SESSION['rol']   = $usuario_db['nombre_rol']; 
            
            header("Location: ../index.php"); 
            exit();
            
        } else {
            $error_mensaje = "Usuario o contraseña incorrectos.";
        }
    } else {
        $error_mensaje = "Usuario o contraseña incorrectos.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body class='fondo-login adamina-regular'>
  <section class='login row text-center'>
      <article class='col-12 col-md-9 col-lg-7 mx-auto'>
        <h1 class='fw-bold'>Iniciar Sesión</h1>
        <div class="login-page d-flex align-items-center justify-content-center">
          <div class="formulario-login p-5 text-center">
            <?php if (!empty($error_mensaje)) { ?>
                <p class="mensaje-error text-danger container fw-bold" style="background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; border-radius: 5px; margin-top: 15px;">
                    <?php echo $error_mensaje; ?>
                </p>
        <?php } ?>
            <form class="form" method="post" action="#">
              <label for="usuario" class='pb-2'>Usuario</label>
              <input type="text"  id="usuario" name="usuario" placeholder="Tu usuario"/>
              <label for="contraseña" class='pb-2'>Contraseña</label>
              <input type="password" id="contrasena" name="contrasena" placeholder="Tu contraseña" />
              <button type='submit' id='ingreso' name='ingreso'>Ingresar</button>
            </form>
          </div>
        </div>
      </article>
      <div class='pt-5 volver'>
          <a href="../index.php">Volver a la página principal</a>
      </div>
  </section>
</body>
</html>
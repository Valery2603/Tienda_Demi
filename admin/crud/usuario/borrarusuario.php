<?php
$id=$_GET['id'];
$id=filter_var($id,FILTER_VALIDATE_INT);
if(!$id){
    header('Location: listausuario.php');
}
require '../../../includes/config/database.php';
$db=conectarDB();
$consultadelete="delete from usuarios where id_usuario=$id";
$resultadodelete=mysqli_query($db,$consultadelete);

if ($resultadodelete) {
    header('Location: listausuario.php?resultado=3');
}

?>
  
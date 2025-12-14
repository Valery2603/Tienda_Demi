<?php
$id=$_GET['id'];
$id=filter_var($id,FILTER_VALIDATE_INT);
if(!$id){
    header('Location: listacategoria.php');
}
require '../../../includes/config/database.php';
$db=conectarDB();
$consultadelete="delete from categorias where id_categoria=$id";
$resultadodelete=mysqli_query($db,$consultadelete);

if ($resultadodelete) {
    header('Location: listacategoria.php?resultado=3');
}

?>
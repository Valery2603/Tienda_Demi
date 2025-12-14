<?php
$id=$_GET['id'];
$id=filter_var($id,FILTER_VALIDATE_INT);
if(!$id){
    header('Location: listaproducto.php');
}
require '../../../includes/config/database.php';
$db=conectarDB();

$consultaimg="select imagen from productos where id_producto=$id";
$resultadoimg=mysqli_query($db,$consultaimg);
$img=mysqli_fetch_assoc($resultadoimg);
echo $img['imagen'];

unlink('../../../imagenes/'.$img['imagen']);

$consultadelete="delete from productos where id_producto=$id";
$resultadodelete=mysqli_query($db,$consultadelete);

if ($resultadodelete) {
    header('Location: listaproducto.php?resultado=3');
}

?>
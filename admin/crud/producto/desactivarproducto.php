<?php
$id=$_GET['id'];
$id=filter_var($id,FILTER_VALIDATE_INT);
if(!$id){
    header('Location: listaproducto.php');
}
require '../../../includes/config/database.php';
$db=conectarDB();

$consultadesactivar="update productos set activo=0 where id_producto=$id";
$resultado=mysqli_query($db,$consultadesactivar);

if ($resultado) {
    header('Location: listaproducto.php?resultado=4');
}

?>
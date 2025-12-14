<?php 
    function conectarDB() {
        $db = mysqli_connect("localhost","root","","demi");
        return $db;
        if (!$db) {
            echo "Error en la conexión";
        }
    }

?>
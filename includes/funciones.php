<?php
    define('TEMPLATE_URL',__DIR__.'/templates/');

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    function incluirTemplates(string $nombre){
        include TEMPLATE_URL.$nombre.'.php'; 
    }
    
?>
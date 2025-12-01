<?php

    $host = 'localhost';
    $dbname = 'control_escolar';
    $usuario = 'root';
    $clave = 'root';  

    try {
        $conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $usuario, $clave); 
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);        
    } catch (PDOException $e) {       
        die("Error de conexión: " . $e->getMessage());
    }
?>
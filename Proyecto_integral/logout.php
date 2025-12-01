<?php
	session_start();      
	// Inicia la sesión para poder manipularla

	session_unset();      
	// Limpia todas las variables de la sesión

	session_destroy();    
	// Destruye completamente la sesión del usuario

	header("Location: ./"); 
	// Redirige al usuario a la página principal (login)

	exit;                 
	// Detiene la ejecución del script después de la redirección
?>
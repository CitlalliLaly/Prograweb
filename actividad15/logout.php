<?php
session_start();
$_SESSION['autenticado'] = false;
header("Location: index.php");
?>
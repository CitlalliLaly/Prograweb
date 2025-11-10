<?php
session_start();

if(!isset($_SESSION['autenticado'])){
    header("Location: index.php");
    exit();
}

$color = $_SESSION['color'];

if(!isset($_COOKIE['visitas'])){
    $contador = 1;
} else {
    $contador = $_COOKIE['visitas'] + 1;
}
setcookie('visitas', $contador, time() + 3600);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Cookies</title>
</head>
<body style="color: <?php echo $color; ?>;">
   <h2>Bienvenido <?php echo $_SESSION['usuario']; ?></h2>
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="sesiones.php">Contador de sesiones</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                <a class="nav-link" href="cookies.php" style="color: <?php echo $color; ?>;">Contador con Cookies</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="http://citlallilaly.atwebpages.com/Actividad05/index.html">Home</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                <a class="nav-link" href="logout.php" style="color: <?php echo $color; ?>;">Cerrar sesión</a>
                </li>
            </ul>
            </div>
        </div>
        </nav>

    <h2>Contador con cookies</h2>
         <p>Has visitado esta página <?php echo $contador; ?> veces.</p>
        <hr>
            <video width="720" height="540" controls>
                <source src="mi_video.mp4" type="video/mp4">
                Tu navegador no soporta el video.
            </video>


    <footer class="main-footer text-center mt-4">
        <p>Citlalli Gonzalez - Programación Web &copy; 2025</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

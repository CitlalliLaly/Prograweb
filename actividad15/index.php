<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Actividad 15|SESSION / COOKIES</title>
</head>
<body>
    <header class="main-header text-center shadow-sm">
        <h1>Session/Cookies</h1>
    </header>
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Actual</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="http://citlallilaly.atwebpages.com/Actividad05/index.html">Home</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
    <hr>
    <h2>Login</h2>
    <form action="validar.php" method="POST">

        <label for="user">
            Usuario:
            <br>
            <input type="text" name="usuario" id="user" required>
        </label>
        <label for="pass">
            Contraseña:
            <input type="password" name="clave" id="pass" required>
        </label>
        <div>
        <label for=""></label>
            <label for="">
                Tipo de usuario:
                <br>
                <select name="color" id="color" size="3" required>
                    <option value="Rojo">Rojo</option>
                    <option value="Verde">Verde</option>
                    <option value="Azul">Azul</option>
                </select>
            </label>
            
        </div>
        <label for="">
            <input type="checkbox" name="recordar" id="recordar">
            Recordar usuario
        </label>

        <input type="submit" value="Iniciar Sesion">
    </form>
    <?php
    if(isset($_GET['error'])){
        echo "<p style='color:red;'>".$_GET['error']."</p>";
    }
    ?>


<footer class="main-footer text-center mt-4">
        <p>Citlalli Gonzalez - Programación Web &copy; 2025</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

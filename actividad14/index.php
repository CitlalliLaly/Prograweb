<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario </title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>    
    <h1>FORMULARIO DE REGISTRO</h1>
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

    
    <form action="procesar.php" method="GET">

        <label for="num1">Numero 1: </label>
        <input type="number" name="num1" id="num1" required>
        <br>
        <br>
        <label for="num2">Numero 2: </label>
        <input type="number" name="num2" id="num2"required>
        <br>
        <br>
        <div>
            <label for=""><strong>Operacion</strong></label>
            <br>
            <label>Suma
                <input type="radio" name="operacion" value="suma">
            </label>
            <br>
            <label>Resta
                <input type="radio" name="operacion" value="resta">
            </label>
            <br>
            <label>Multiplicacion
                <input type="radio" name="operacion" value="mult">
            </label>
            <br>
            <label>Division
                <input type="radio" name="operacion" value="div">
            </label>
        </div>
        <br>
        <input type="submit" value="Operacion">
    </form>

    <footer class="main-footer text-center mt-4">
        <p>Citlalli Gonzalez - Programación Web &copy; 2025</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

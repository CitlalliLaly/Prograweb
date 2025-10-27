<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Sistema de calificaciones</title>
</head>
<body>
    <header class="main-header text-center shadow-sm">
        <h1>Sistema de Calificaciones</h1>
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

    <main class="container my-4">
        <section class="calificaciones-section p-4 bg-light rounded shadow-sm">
            <h2 class="mb-3">Resultados de Calificaciones</h2>
            <div class="calificaciones-output p-3 bg-white border rounded">
                <?php include 'calificaciones.php'; ?>
            </div>
        </section>
    <footer class="main-footer text-center mt-4">
        <p>Citlalli Gonzalez - Programación Web &copy; 2025</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

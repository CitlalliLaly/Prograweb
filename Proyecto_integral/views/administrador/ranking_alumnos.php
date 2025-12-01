<?php
session_start();
include '../../Includes/conexion.php';
include '../../Includes/header.php';

// Solo administradores
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: /Proyecto_integral/login.php');
    exit();
}

// Ranking por entregas puntuales una insignia de constancia
$sql = "SELECT u.id_usuario, u.username, COALESCE(COUNT(ua.id),0) AS entregas_puntuales
        FROM usuarios u
        LEFT JOIN user_achievements ua ON ua.id_usuario = u.id_usuario
        WHERE u.id_usuario IS NOT NULL
        GROUP BY u.id_usuario
        ORDER BY entregas_puntuales DESC, u.username ASC
        LIMIT 50";

$stmt = $conexion->prepare($sql);
$stmt->execute();
$ranking = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="contenedor-flex-40">
    <div class="contenedor-centrado-lg">
        <h2 class="titulo-principal-seccion">Ranking: Alumnos Constantes</h2>
        <p class="subtitulo-atenuado-pequeño margen-inferior-20">Top de alumnos según entregas puntuales acumuladas.</p>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Usuario</th>
                                <th>Entregas Puntuales</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($ranking as $r): ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo htmlspecialchars($r['username']); ?></td>
                                    <td><?php echo (int)$r['entregas_puntuales']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="margen-superior-30 texto-centrado-personalizado">
            <a href="/Proyecto_integral/views/administrador/index.php" class="btn btn-secondary">← Volver al Panel</a>
        </div>
    </div>
</div>

<?php include '../../Includes/footer.php'; ?>

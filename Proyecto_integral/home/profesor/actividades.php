<?php
session_start();
include '../../Includes/conexion.php';
include '../../Includes/header.php';

// Solo profesores
if (!isset($_SESSION['id_usuario']) || !in_array(strtolower($_SESSION['rol']), ['profesor','maestro'])) {
    header('Location: ../../index.php');
    exit();
}

// Obtener id_profesor de la sesión
$pstmt = $conexion->prepare("SELECT id_profesor FROM usuarios WHERE id_usuario = :uid LIMIT 1");
$pstmt->execute([':uid' => $_SESSION['id_usuario']]);
$prow = $pstmt->fetch(PDO::FETCH_ASSOC);
$id_prof = $prow ? $prow['id_profesor'] : null;

if (!$id_prof) {
    echo "<div class='container mt-4'><div class='alert alert-danger'>Perfil de profesor no encontrado.</div></div>";
    include '../../Includes/footer.php';
    exit();
}

// Header
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Actividades</h2>
        <div>
            <a href="crear_actividad.php" class="btn btn-primary">Crear actividad</a>
            <a href="ver_actividades.php" class="btn btn-outline-secondary">Ver todas</a>
        </div>
    </div>

    <p class="text-muted">Desde aquí puedes crear nuevas actividades o ver las ya publicadas en tus cursos.</p>

    <?php
    $stmt = $conexion->prepare("SELECT c.id_curso, c.nombre_curso, COUNT(a.ID_actividad) AS total_actividades
                                FROM cursos c
                                LEFT JOIN actividades a ON c.id_curso = a.ID_curso
                                WHERE c.id_profesor = :idp
                                GROUP BY c.id_curso, c.nombre_curso
                                ORDER BY c.nombre_curso");
    $stmt->execute([':idp' => $id_prof]);
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($res)) {
        echo '<div class="alert alert-info">No tienes cursos o actividades aún.</div>';
    } else {
        echo '<div class="list-group">';
        foreach ($res as $r) {
            // Muestra el nombre del curso y dos acciones: Ver actividades y Calificar
            echo '<div class="list-group-item d-flex justify-content-between align-items-center">'
                . '<div>' . htmlspecialchars($r['nombre_curso']) . '</div>'
                . '<div class="btn-group" role="group" aria-label="acciones-curso">'
                    . '<a href="ver_actividades.php?curso=' . htmlspecialchars($r['id_curso']) . '" class="btn btn-sm btn-outline-secondary">Ver</a>'
                    . '<a href="calificar.php?id_curso=' . htmlspecialchars($r['id_curso']) . '" class="btn btn-sm btn-outline-primary">Calificar</a>'
                . '</div>'
                . '<span class="badge bg-primary rounded-pill ms-3">' . intval($r['total_actividades']) . '</span>'
            . '</div>';
        }
        echo '</div>';
    }
    ?>

</div>

<?php include '../../Includes/footer.php'; ?>

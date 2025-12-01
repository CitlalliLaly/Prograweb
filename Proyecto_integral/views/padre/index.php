<?php
session_start();
$page_title = "Inicio | CUSJ";

// Verificado que sea padre el usuario que accede
if (!isset($_SESSION['id_usuario']) || strtolower($_SESSION['rol']) !== 'padre') {
    header('Location: ../../login.php');
    exit();
}

include '../../Includes/header.php';
include '../../Includes/conexion.php';

include '../../Includes/welcome.php';

// Obtener id_padre del usuario actual
$id_usuario = $_SESSION['id_usuario'];
$sql_padre = "SELECT id_padre FROM usuarios WHERE id_usuario = :id_usuario";
$stmt_padre = $conexion->prepare($sql_padre);
$stmt_padre->execute([':id_usuario' => $id_usuario]);
$padre_row = $stmt_padre->fetch(PDO::FETCH_ASSOC);

if (!$padre_row) {
    echo "<div class='alert alert-danger'>Error: No se pudo identificar como padre.</div>";
    include '../../Includes/footer.php';
    exit();
}

$id_padre = $padre_row['id_padre'];

// Obtener información del padre
$sql_info = "SELECT nombre, apellido, correo, telefono FROM padres WHERE id_padre = :id_padre";
$stmt_info = $conexion->prepare($sql_info);
$stmt_info->execute([':id_padre' => $id_padre]);
$info_padre = $stmt_info->fetch(PDO::FETCH_ASSOC);

// Obtener hijos del padre
$sql_hijos = "SELECT a.id_alumno, a.nombre, a.apellido, a.correo, u.username, u.id_usuario
              FROM padres_alumnos pa
              JOIN alumnos a ON pa.ID_alumno = a.id_alumno
              JOIN usuarios u ON u.id_alumno = a.id_alumno
              WHERE pa.padres_idpadres = :id_padre
              ORDER BY a.nombre";
$stmt_hijos = $conexion->prepare($sql_hijos);
$stmt_hijos->execute([':id_padre' => $id_padre]);
$hijos = $stmt_hijos->fetchAll(PDO::FETCH_ASSOC);

// Obtener ID del hijo seleccionado (si está seleccionado)
$id_alumno_seleccionado = isset($_GET['alumno']) ? intval($_GET['alumno']) : (count($hijos) > 0 ? $hijos[0]['id_alumno'] : null);

$calificaciones = [];
$info_alumno = null;

if ($id_alumno_seleccionado) {
    // Verificar que el hijo pertenece a este padre
    $sql_verificar = "SELECT 1 FROM padres_alumnos WHERE ID_alumno = :id_alumno AND padres_idpadres = :id_padre";
    $stmt_verificar = $conexion->prepare($sql_verificar);
    $stmt_verificar->execute([':id_alumno' => $id_alumno_seleccionado, ':id_padre' => $id_padre]);
    
    if ($stmt_verificar->rowCount() === 0) {
        echo "<div class='alert alert-danger'>No tienes permiso para ver este alumno.</div>";
        include '../../Includes/footer.php';
        exit();
    }
    
    // Obtener información del alumno
    $sql_alumno = "SELECT id_alumno, nombre, apellido, correo FROM alumnos WHERE id_alumno = :id_alumno";
    $stmt_alumno = $conexion->prepare($sql_alumno);
    $stmt_alumno->execute([':id_alumno' => $id_alumno_seleccionado]);
    $info_alumno = $stmt_alumno->fetch(PDO::FETCH_ASSOC);
    
    // Obtener calificaciones del alumno
    $sql_califs = "SELECT 
                    i.ID_inscripcion,
                    m.nombre AS materia,
                    c.nombre_curso AS curso,
                    c.id_curso,
                    a.titulo AS actividad,
                    cal.calificacion_obtenida,
                    a.ponderacion,
                    i.estado AS estado_inscripcion
                   FROM inscripciones i
                   JOIN cursos c ON i.ID_curso = c.id_curso
                   JOIN materia m ON c.id_materia = m.id_materia
                   LEFT JOIN calificaciones cal ON i.ID_inscripcion = cal.ID_inscripcion
                   LEFT JOIN actividades a ON cal.ID_actividad = a.ID_actividad
                   WHERE i.ID_alumno = :id_alumno
                   ORDER BY m.nombre, a.titulo";
                   
    $stmt_califs = $conexion->prepare($sql_califs);
    $stmt_califs->execute([':id_alumno' => $id_alumno_seleccionado]);
    $calificaciones_raw = $stmt_califs->fetchAll(PDO::FETCH_ASSOC);
    
    // Agrupar calificaciones por curso (usar ID_curso como clave) para asegurar correspondencia correcta
    $calificaciones = [];
    foreach ($calificaciones_raw as $cal) {
        $cursoKey = $cal['id_curso'] ?? ('curso_' . md5($cal['curso'] ?? uniqid()));
        if (!isset($calificaciones[$cursoKey])) {
            $calificaciones[$cursoKey] = [
                'materia' => $cal['materia'],
                'curso' => $cal['curso'],
                'id_curso' => $cal['id_curso'],
                'estado' => $cal['estado_inscripcion'],
                'actividades' => []
            ];
        }
        // Incluir calificaciones numéricas (incluye 0). Evitar usar truthy check que ignora 0.
        if (is_numeric($cal['calificacion_obtenida'])) {
            $calificaciones[$cursoKey]['actividades'][] = [
                'actividad' => $cal['actividad'],
                'calificacion' => $cal['calificacion_obtenida'],
                'ponderacion' => $cal['ponderacion']
            ];
        }
    }
    
    // Calcular promedios por materia
    foreach ($calificaciones as $cursoKey => &$datos) {
        if (!empty($datos['actividades'])) {
            $promedio = 0;
            $sumaPonderaciones = 0;
            foreach ($datos['actividades'] as $act) {
                // Asegurar valores numéricos
                $cal = is_numeric($act['calificacion']) ? (float)$act['calificacion'] : 0.0;
                $pond = is_numeric($act['ponderacion']) ? (float)$act['ponderacion'] : 0.0;
                $promedio += $cal * $pond / 100;
                $sumaPonderaciones += $pond;
            }
            // Si la suma de ponderaciones es 0, no hay forma de calcular promedio correcto
            if ($sumaPonderaciones <= 0) {
                $datos['promedio'] = 'Sin calificaciones';
            } else {
                // Normalizar por 100 si las ponderaciones suman 100, de lo contrario usamos el cálculo directo
                $datos['promedio'] = round($promedio, 2);
                // Determinar estado según promedio real (umbral 60)
                if (is_numeric($datos['promedio'])) {
                    $umbral = 60.0;
                    if ($datos['promedio'] >= $umbral) {
                        $datos['estado'] = 'Aprobado';
                    } else {
                        $datos['estado'] = 'Reprobado';
                    }
                }
            }
        } else {
            $datos['promedio'] = 'Sin calificaciones';
        }
    }
}
?>

<div class="page-content">
    <div class="container-fluid">
        <!-- Encabezado -->
    

        <!-- Información del Padre -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card p-4">
                    <h5 class="primary-dark-strong">Mi Información</h5>
                    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($info_padre['nombre'] . ' ' . $info_padre['apellido']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($info_padre['correo']); ?></p>
                    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($info_padre['telefono'] ?? 'No registrado'); ?></p>
                </div>
            </div>

            <!-- Selector de Hijo -->
            <div class="col-md-8">
                <div class="card p-4">
                    <h5 class="primary-dark-strong">Seleccionar Hijo</h5>
                    <?php if (count($hijos) > 0): ?>
                        <div class="row">
                            <?php foreach ($hijos as $hijo): ?>
                                <div class="col-md-6 mb-2">
                                    <a href="?alumno=<?php echo $hijo['id_alumno']; ?>" 
                                       class="btn <?php echo ($id_alumno_seleccionado === $hijo['id_alumno']) ? 'btn-primary' : 'btn-outline-primary'; ?> w-100">
                                        <?php echo htmlspecialchars($hijo['nombre'] . ' ' . $hijo['apellido']); ?>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">No tiene hijos registrados en el sistema.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Calificaciones del Hijo Seleccionado -->
        <?php if ($info_alumno): ?>
            <div class="row">
                <div class="col-12">
                        <div class="card p-4 mb-4">
                        <h3 class="primary-dark-strong-large">
                            Calificaciones de <?php echo htmlspecialchars($info_alumno['nombre'] . ' ' . $info_alumno['apellido']); ?>
                        </h3>
                        <p class="muted-small-085">Email: <?php echo htmlspecialchars($info_alumno['correo']); ?></p>
                    </div>
                </div>
            </div>

            <?php if (count($calificaciones) > 0): ?>
                <?php foreach ($calificaciones as $cursoKey => $datos): ?>
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card p-4">
                                <div class="row mb-3">
                                    <div class="col-md-8">
                                            <h5 class="primary-dark-strong"><?php echo htmlspecialchars($datos['materia'] ?? 'Materia'); ?></h5>
                                        <p class="muted-small-085">
                                            Curso: <?php echo htmlspecialchars($datos['curso'] ?? 'No especificado'); ?>
                                            <small class="text-muted">(ID: <?php echo htmlspecialchars($datos['id_curso'] ?? 'n/a'); ?>)</small>
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <div class="bg-primary-light-padded">
                                            <p class="muted-small-085">Promedio</p>
                                            <h3 class="primary-dark-strong-large">
                                                <?php echo is_numeric($datos['promedio']) ? number_format($datos['promedio'], 2) : $datos['promedio']; ?>
                                            </h3>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tabla de Actividades -->
                                <?php if (!empty($datos['actividades'])): ?>
                                    <table class="table table-sm table-bordered">
                                        <thead class="thead-primary-light">
                                            <tr>
                                                <th>Actividad</th>
                                                <th class="text-center">Calificación</th>
                                                <th class="text-center">Ponderación</th>
                                                <th class="text-center">Contribución al Promedio</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($datos['actividades'] as $act): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($act['actividad']); ?></td>
                                                    <td class="text-center">
                                                        <span class="primary-dark-strong">
                                                            <?php echo number_format($act['calificacion'], 2); ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-center"><?php echo $act['ponderacion']; ?>%</td>
                                                    <td class="text-center">
                                                        <?php echo number_format($act['calificacion'] * $act['ponderacion'] / 100, 2); ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <div class="alert alert-info">Sin calificaciones registradas aún.</div>
                                <?php endif; ?>

                                <!-- Badge de Estado -->
                                <div class="mt-3">
                                    <span class="badge <?php echo ($datos['estado'] === 'Aprobado') ? 'badge-approved' : 'badge-warning'; ?>">
                                        Estado: <?php echo htmlspecialchars($datos['estado']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-info">
                            El alumno aún no tiene materias inscritas.
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php include '../../Includes/footer.php'; ?>

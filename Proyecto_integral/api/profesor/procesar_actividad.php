<?php
session_start();
include '../../Includes/conexion.php';

if (!isset($_SESSION['id_usuario']) || !in_array(strtolower($_SESSION['rol']), ['profesor','maestro'])) {
    header('Location: ../../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../home/profesor/crear_actividad.php');
    exit();
}

$id_curso = isset($_POST['id_curso']) ? intval($_POST['id_curso']) : 0;
$titulo = trim($_POST['titulo']);
$descripcion = trim($_POST['descripcion']);
$fecha_limite = !empty($_POST['fecha_limite']) ? $_POST['fecha_limite'] : null;
$ponderacion = isset($_POST['ponderacion']) ? floatval($_POST['ponderacion']) : 0;
$tipo = isset($_POST['tipo']) ? $_POST['tipo'] : 'Tarea';

try {
    $sql = "INSERT INTO actividades (ID_curso, titulo, descripcion, fecha_limite, ponderacion, tipo)
            VALUES (:id_curso, :titulo, :descripcion, :fecha_limite, :ponderacion, :tipo)";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        ':id_curso' => $id_curso,
        ':titulo' => $titulo,
        ':descripcion' => $descripcion,
        ':fecha_limite' => $fecha_limite,
        ':ponderacion' => $ponderacion,
        ':tipo' => $tipo
    ]);

    $_SESSION['mensaje_ok'] = 'Actividad creada correctamente.';
} catch (PDOException $e) {
    $_SESSION['mensaje_error'] = 'Error al crear actividad: ' . $e->getMessage();
}

header('Location: ../../home/profesor/crear_actividad.php');
exit();

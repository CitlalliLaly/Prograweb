<?php
session_start();
include '../../Includes/conexion.php';

// Helper de redirección con mensajes consistentes
function redirect_inscribir($type, $message) {
    $param = $type === 'err' ? 'err' : 'msg';
    header('Location: ../../home/usuarios/inscribir.php?' . $param . '=' . urlencode($message));
    exit();
}

if (!isset($_SESSION['id_usuario']) || strtolower($_SESSION['rol']) !== 'alumno') {
    redirect_inscribir('err', 'Acceso denegado. Inicia sesión como alumno.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_curso'])) {
    redirect_inscribir('err', 'Petición inválida.');
}

$id_curso = intval($_POST['id_curso']);
if ($id_curso <= 0) {
    redirect_inscribir('err', 'Curso inválido.');
}

// Resolver id_alumno: preferir sesión, si no existe intentar buscar desde usuarios
$id_alumno = isset($_SESSION['id_alumno']) ? intval($_SESSION['id_alumno']) : 0;
if ($id_alumno <= 0) {
    // Intentar buscar id_alumno asociado al id_usuario
    $stmt = $conexion->prepare("SELECT id_alumno FROM usuarios WHERE id_usuario = :uid AND id_alumno IS NOT NULL LIMIT 1");
    $stmt->execute([':uid' => $_SESSION['id_usuario']]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($res && isset($res['id_alumno'])) {
        $id_alumno = (int)$res['id_alumno'];
        // Guardar en sesión para futuras llamadas
        $_SESSION['id_alumno'] = $id_alumno;
    }
}

if ($id_alumno <= 0) {
    redirect_inscribir('err', 'No se pudo determinar el alumno autenticado.');
}

try {
    // Verificar existencia previa
    $check = $conexion->prepare("SELECT ID_inscripcion FROM inscripciones WHERE ID_curso = :id_curso AND ID_alumno = :id_alumno LIMIT 1");
    $check->execute([':id_curso' => $id_curso, ':id_alumno' => $id_alumno]);
    if ($check->fetch()) {
        redirect_inscribir('err', 'Ya existe una solicitud o inscripción para ese curso.');
    }

    $ins = $conexion->prepare("INSERT INTO inscripciones (ID_alumno, ID_curso, estado) VALUES (:id_alumno, :id_curso, 'Pendiente')");
    $ins->execute([':id_alumno' => $id_alumno, ':id_curso' => $id_curso]);

    redirect_inscribir('msg', 'Solicitud enviada. Espera la aprobación del profesor.');

} catch (PDOException $e) {
    // Si es duplicate key por alguna razón, mostrar mensaje amigable
    if ($e->getCode() === '23000') {
        redirect_inscribir('err', 'Ya existe una solicitud o inscripción para ese curso.');
    }
    redirect_inscribir('err', 'Error al solicitar inscripción: ' . $e->getMessage());
}

<?php
session_start();
include '../../Includes/conexion.php';

// ============================================
// VALIDACIONES INICIALES
// ============================================

// Solo profesores pueden acceder
if (!isset($_SESSION['id_usuario']) || !in_array(strtolower($_SESSION['rol']), ['profesor','maestro'])) {
    http_response_code(403);
    exit('Acceso denegado');
}

// Solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    exit('Método no permitido');
}

// Validar parámetros POST
$id_inscripcion = isset($_POST['id_inscripcion']) ? intval($_POST['id_inscripcion']) : 0;
$accion = isset($_POST['accion']) ? trim($_POST['accion']) : '';

if (!$id_inscripcion || !in_array($accion, ['aprobar', 'rechazar'])) {
    $_SESSION['mensaje_error'] = 'Parámetros inválidos.';
    header('Location: ../../home/profesor/inscripciones.php');
    exit();
}

// ============================================
// OBTENER INFORMACIÓN DEL PROFESOR
// ============================================

try {
    $sentencia_profesor = $conexion->prepare("
        SELECT id_profesor 
        FROM usuarios 
        WHERE id_usuario = :uid 
        LIMIT 1
    ");
    $sentencia_profesor->execute([':uid' => $_SESSION['id_usuario']]);
    $datos_profesor = $sentencia_profesor->fetch(PDO::FETCH_ASSOC);
    
    if (!$datos_profesor) {
        throw new Exception('No se encontró perfil de profesor');
    }
    
    $id_profesor = $datos_profesor['id_profesor'];

} catch (Exception $e) {
    $_SESSION['mensaje_error'] = 'Error: ' . $e->getMessage();
    header('Location: ../../home/profesor/inscripciones.php');
    exit();
}

// ============================================
// VERIFICAR AUTORIZACIÓN
// ============================================

try {
    // El profesor solo puede procesar inscripciones de sus propios cursos
    $sentencia_autorizar = $conexion->prepare("
        SELECT ins.ID_inscripcion, ins.ID_alumno, ins.ID_curso, ins.estado
        FROM inscripciones ins
        INNER JOIN cursos c ON ins.ID_curso = c.id_curso
        WHERE ins.ID_inscripcion = :id_inscripcion 
          AND c.id_profesor = :id_profesor
        LIMIT 1
    ");
    
    $sentencia_autorizar->execute([
        ':id_inscripcion' => $id_inscripcion,
        ':id_profesor' => $id_profesor
    ]);
    
    $inscripcion = $sentencia_autorizar->fetch(PDO::FETCH_ASSOC);
    
    if (!$inscripcion) {
        $_SESSION['mensaje_error'] = 'No autorizado para procesar esta inscripción.';
        header('Location: ../../home/profesor/inscripciones.php');
        exit();
    }
    
    // Validar que solo se procesan pendientes
    if ($inscripcion['estado'] !== 'Pendiente') {
        $_SESSION['mensaje_error'] = 'Esta inscripción ya fue procesada.';
        header('Location: ../../home/profesor/inscripciones.php');
        exit();
    }

} catch (PDOException $e) {
    $_SESSION['mensaje_error'] = 'Error de autorización: ' . $e->getMessage();
    header('Location: ../../home/profesor/inscripciones.php');
    exit();
}

// ============================================
// PROCESAR LA ACCIÓN
// ============================================

try {
    $nuevo_estado = ($accion === 'aprobar') ? 'Aprobado' : 'Rechazado';
    
    $sentencia_actualizar = $conexion->prepare("
        UPDATE inscripciones 
        SET 
            estado = :nuevo_estado,
            fecha_aprobacion = NOW(),
            ID_profesor_que_aprueba = :id_profesor
        WHERE ID_inscripcion = :id_inscripcion
    ");
    
    $sentencia_actualizar->execute([
        ':nuevo_estado' => $nuevo_estado,
        ':id_profesor' => $id_profesor,
        ':id_inscripcion' => $id_inscripcion
    ]);
    
    // Mensaje de éxito
    if ($accion === 'aprobar') {
        $_SESSION['mensaje_ok'] = '✓ Solicitud aprobada correctamente.';
    } else {
        $_SESSION['mensaje_ok'] = '✓ Solicitud rechazada correctamente.';
    }

} catch (PDOException $e) {
    $_SESSION['mensaje_error'] = 'Error al procesar: ' . $e->getMessage();
}

// Redirigir de vuelta al listado del profesor
header('Location: ../../home/profesor/inscripciones.php');
exit();

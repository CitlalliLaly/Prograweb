<?php
session_start();
$connPath = __DIR__ . '/../../Includes/conexion.php';
if (!file_exists($connPath)) { http_response_code(500); echo 'DB error'; exit(); }
include $connPath;
$base_web = '/Proyecto_integral';

// Role check
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol']) || strtolower($_SESSION['rol']) !== 'alumno') {
    header('Location: ' . $base_web . '/index.php');
    exit();
}

$id_alumno = $_SESSION['id_alumno'] ?? $_SESSION['id_usuario'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $base_web . '/home/usuarios/index_alumno.php');
    exit();
}

$id_actividad = isset($_POST['id_actividad']) ? intval($_POST['id_actividad']) : 0;
$comentario = isset($_POST['comentario']) ? trim($_POST['comentario']) : null;

if ($id_actividad <= 0) {
    header('Location: ' . $base_web . '/home/usuarios/index_alumno.php?error=' . urlencode('Actividad inválida'));
    exit();
}

// get materia for redirect
try {
    $q = $conexion->prepare("SELECT c.id_materia FROM actividades a JOIN cursos c ON a.ID_curso = c.id_curso WHERE a.ID_actividad = :id_act LIMIT 1");
    $q->execute([':id_act' => $id_actividad]);
    $row = $q->fetch(PDO::FETCH_ASSOC);
    $id_materia = $row ? $row['id_materia'] : null;
} catch (Exception $e) {
    $id_materia = null;
}

if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
    $err = 'No se recibió archivo o hubo un error en la subida.';
    $to = $id_materia ? $base_web . "/views/alumno/ver_materia.php?id={$id_materia}&error=".urlencode($err) : $base_web . '/home/usuarios/index_alumno.php?error=' . urlencode($err);
    header('Location: ' . $to);
    exit();
}

$file = $_FILES['archivo'];
$maxSize = 12 * 1024 * 1024;
if ($file['size'] > $maxSize) {
    $err = 'El archivo excede el tamaño máximo de 12MB.';
    $to = $id_materia ? $base_web . "/views/alumno/ver_materia.php?id={$id_materia}&error=".urlencode($err) : $base_web . '/home/usuarios/index_alumno.php?error=' . urlencode($err);
    header('Location: ' . $to);
    exit();
}

$allowed = ['pdf','doc','docx','zip','rar','png','jpg','jpeg','txt','ppt','pptx'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowed)) {
    $err = 'Tipo de archivo no permitido.';
    $to = $id_materia ? $base_web . "/views/alumno/ver_materia.php?id={$id_materia}&error=".urlencode($err) : $base_web . '/home/usuarios/index_alumno.php?error=' . urlencode($err);
    header('Location: ' . $to);
    exit();
}

$uploadsDir = __DIR__ . '/../../assets/uploads';
if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0755, true);
$userDir = $uploadsDir . DIRECTORY_SEPARATOR . 'alumno_' . $id_alumno;
if (!is_dir($userDir)) mkdir($userDir, 0755, true);

$safeName = preg_replace('/[^A-Za-z0-9_\\-\\.]/', '_', basename($file['name']));
$destName = date('Ymd_His') . '_u' . $id_alumno . '_' . $id_actividad . '_' . $safeName;
$destPath = $userDir . DIRECTORY_SEPARATOR . $destName;

if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    $err = 'Error al mover el archivo subido.';
    $to = $id_materia ? $base_web . "/views/alumno/ver_materia.php?id={$id_materia}&error=".urlencode($err) : $base_web . '/home/usuarios/index_alumno.php?error=' . urlencode($err);
    header('Location: ' . $to);
    exit();
}

$archivo_url = substr($destPathNorm, strlen($docroot));
if ($archivo_url === '' || $archivo_url[0] !== '/') { $archivo_url = '/' . ltrim($archivo_url, '/'); }
try {
    $ins = $conexion->prepare('INSERT INTO entregas (id_actividad, id_alumno, archivo_url, comentario, fecha_entrega) VALUES (:id_act, :id_alum, :archivo, :comentario, NOW())');
    $ins->execute([
        ':id_act' => $id_actividad,
        ':id_alum' => $id_alumno,
        ':archivo' => $archivo_url,
        ':comentario' => $comentario,
    ]);
} catch (PDOException $e) {
    // try create
    if ($e->getCode() === '42S02') {
        $ddl = "CREATE TABLE entregas (
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            id_actividad INT NOT NULL,
            id_alumno INT NOT NULL,
            archivo_url VARCHAR(255),
            comentario TEXT,
            fecha_entrega DATETIME DEFAULT CURRENT_TIMESTAMP,
            calificacion DECIMAL(5,2) NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $conexion->exec($ddl);
        $ins = $conexion->prepare('INSERT INTO entregas (id_actividad, id_alumno, archivo_url, comentario, fecha_entrega) VALUES (:id_act, :id_alum, :archivo, :comentario, NOW())');
        $ins->execute([
            ':id_act' => $id_actividad,
            ':id_alum' => $id_alumno,
            ':archivo' => $archivo_url,
            ':comentario' => $comentario,
        ]);
    } else {
        $err = 'Error en la base de datos: ' . $e->getMessage();
        $to = $id_materia ? $base_web . "/views/alumno/ver_materia.php?id={$id_materia}&error=".urlencode($err) : $base_web . '/home/usuarios/index_alumno.php?error=' . urlencode($err);
        header('Location: ' . $to);
        exit();
    }
}

// success
$to = $id_materia ? "$base_web/views/alumno/ver_materia.php?id=$id_materia&msg=entrega_ok" : "$base_web/home/usuarios/index_alumno.php?msg=entrega_ok";
header('Location: ' . $to);
exit();
?>

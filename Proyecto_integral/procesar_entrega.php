<?php
session_start();
$connPath = __DIR__ . '/Includes/conexion.php';
if (!file_exists($connPath)) { http_response_code(500); exit('DB'); }
include $connPath;
$base_web = '/Proyecto_integral';
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
$comentario = isset($_POST['comentario']) ? trim($_POST['comentario']) : '';
if ($id_actividad <= 0) {
    header('Location: ' . $base_web . '/home/usuarios/index_alumno.php?error=Invalid');
    exit();
}
$id_materia = null;
try {
    $q = $conexion->prepare("SELECT c.id_materia FROM actividades a JOIN cursos c ON a.ID_curso = c.id_curso WHERE a.ID_actividad = :id_act LIMIT 1");
    $q->execute([':id_act' => $id_actividad]);
    $row = $q->fetch(PDO::FETCH_ASSOC);
    $id_materia = $row['id_materia'] ?? null;
} catch (Exception $e) {}
if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
    $to = $id_materia ? "$base_web/views/alumno/ver_materia.php?id=$id_materia&error=No%20file" : "$base_web/home/usuarios/index_alumno.php?error=No%20file";
    header('Location: ' . $to);
    exit();
}
$file = $_FILES['archivo'];
if ($file['size'] > 12 * 1024 * 1024 || !in_array(strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)), ['pdf','doc','docx','zip','rar','png','jpg','jpeg','txt','ppt','pptx'])) {
    $to = $id_materia ? "$base_web/views/alumno/ver_materia.php?id=$id_materia&error=File%20error" : "$base_web/home/usuarios/index_alumno.php?error=File%20error";
    header('Location: ' . $to);
    exit();
}
$uploadsDir = __DIR__ . '/assets/uploads';
if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0755, true);
$userDir = $uploadsDir . '/alumno_' . $id_alumno;
if (!is_dir($userDir)) mkdir($userDir, 0755, true);
$safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($file['name']));
$destName = date('Ymd_His') . '_' . $id_alumno . '_' . $id_actividad . '_' . $safeName;
$destPath = $userDir . '/' . $destName;
if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    $to = $id_materia ? "$base_web/views/alumno/ver_materia.php?id=$id_materia&error=Upload%20fail" : "$base_web/home/usuarios/index_alumno.php?error=Upload%20fail";
    header('Location: ' . $to);
    exit();
}
$docroot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/');
$destPathNorm = str_replace('\\', '/', $destPath);
if (stripos($destPathNorm, $docroot) === 0) {
    $archivo_url = substr($destPathNorm, strlen($docroot));
} else {
    $archivo_url = $destPathNorm;
}
if ($archivo_url === '' || $archivo_url[0] !== '/') {
    $archivo_url = '/' . ltrim($archivo_url, '/');
}
try {
    $conexion->exec("CREATE TABLE IF NOT EXISTS entregas (id INT AUTO_INCREMENT PRIMARY KEY, id_actividad INT NOT NULL, id_alumno INT NOT NULL, archivo_url VARCHAR(255), comentario TEXT, fecha_entrega DATETIME DEFAULT CURRENT_TIMESTAMP, calificacion DECIMAL(5,2) NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
} catch (Exception $e) {}
try {
    $ins = $conexion->prepare('INSERT INTO entregas (id_actividad, id_alumno, archivo_url, comentario, fecha_entrega) VALUES (:id_act, :id_alum, :archivo, :comentario, NOW())');
    $ins->execute([':id_act' => $id_actividad, ':id_alum' => $id_alumno, ':archivo' => $archivo_url, ':comentario' => $comentario]);
} catch (Exception $e) {
    $to = $id_materia ? "$base_web/views/alumno/ver_materia.php?id=$id_materia&error=DB%20error" : "$base_web/home/usuarios/index_alumno.php?error=DB%20error";
    header('Location: ' . $to);
    exit();
}
$to = $id_materia ? "$base_web/views/alumno/ver_materia.php?id=$id_materia&msg=entrega_ok" : "$base_web/home/usuarios/index_alumno.php?msg=entrega_ok";
header('Location: ' . $to);
exit();
?>

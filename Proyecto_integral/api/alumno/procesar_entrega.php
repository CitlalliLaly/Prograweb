<?php
// Procesador exclusivo para alumnos: subir archivo de entrega y registrar en DB
session_start();

// Incluir conexión
$connPath = __DIR__ . '/../../Includes/conexion.php';
if (file_exists($connPath)) include $connPath;

// Calcular base_path para redirecciones web
$script = $_SERVER['SCRIPT_NAME'];
$levels = substr_count($script, '/') - 2;
$base_path = '';
if ($levels > 0) $base_path = str_repeat('../', $levels);

// Verificar sesión y rol alumno
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol']) || strtolower($_SESSION['rol']) !== 'alumno') {
    header('Location: ' . $base_path . 'index.php');
    exit();
}

$id_alumno = $_SESSION['id_alumno'] ?? $_SESSION['id_usuario'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $base_path . 'home/usuarios/index_alumno.php');
    exit();
}

$id_actividad = isset($_POST['id_actividad']) ? intval($_POST['id_actividad']) : 0;
$comentario = isset($_POST['comentario']) ? trim($_POST['comentario']) : null;

if ($id_actividad <= 0) {
    header('Location: ' . $base_path . 'home/usuarios/index_alumno.php?error=' . urlencode('Actividad inválida'));
    exit();
}

// Obtener id_materia para redirección
try {
    $q = $conexion->prepare("SELECT c.id_materia FROM actividades a JOIN cursos c ON a.ID_curso = c.id_curso WHERE a.ID_actividad = :id_act LIMIT 1");
    $q->execute([':id_act' => $id_actividad]);
    $row = $q->fetch(PDO::FETCH_ASSOC);
    $id_materia = $row ? $row['id_materia'] : null;
} catch (Exception $e) {
    $id_materia = null;
}

// Validar archivo
if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
    $err = 'No se recibió archivo o hubo un error en la subida.';
    $to = $id_materia ? $base_path . "views/alumno/ver_materia.php?id={$id_materia}&error=".urlencode($err) : $base_path . 'home/usuarios/index_alumno.php?error=' . urlencode($err);
    header('Location: ' . $to);
    exit();
}

$file = $_FILES['archivo'];
$maxSize = 12 * 1024 * 1024; // 12 MB por seguridad
if ($file['size'] > $maxSize) {
    $err = 'El archivo excede el tamaño máximo de 12MB.';
    $to = $id_materia ? $base_path . "views/alumno/ver_materia.php?id={$id_materia}&error=".urlencode($err) : $base_path . 'home/usuarios/index_alumno.php?error=' . urlencode($err);
    header('Location: ' . $to);
    exit();
}

$allowed = ['pdf','doc','docx','zip','rar','png','jpg','jpeg','txt','ppt','pptx'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowed)) {
    $err = 'Tipo de archivo no permitido.';
    $to = $id_materia ? $base_path . "views/alumno/ver_materia.php?id={$id_materia}&error=".urlencode($err) : $base_path . 'home/usuarios/index_alumno.php?error=' . urlencode($err);
    header('Location: ' . $to);
    exit();
}

// Preparar destino: carpeta por alumno para orden
$uploadsDir = __DIR__ . '/../../assets/uploads';
if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0755, true);
$userDir = $uploadsDir . DIRECTORY_SEPARATOR . 'alumno_' . $id_alumno;
if (!is_dir($userDir)) mkdir($userDir, 0755, true);

$safeName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', basename($file['name']));
$destName = date('Ymd_His') . '_u' . $id_alumno . '_' . $id_actividad . '_' . $safeName;
$destPath = $userDir . DIRECTORY_SEPARATOR . $destName;

if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    $err = 'Error al mover el archivo subido.';
    $to = $id_materia ? $base_path . "views/alumno/ver_materia.php?id={$id_materia}&error=".urlencode($err) : $base_path . 'home/usuarios/index_alumno.php?error=' . urlencode($err);
    header('Location: ' . $to);
    exit();
}

$webPath = str_replace('\\', '/', str_replace(rtrim($_SERVER['DOCUMENT_ROOT'], '\\/'), '', $destPath));
if ($webPath === '' || $webPath[0] !== '/') {
    $webPath = '/' . ltrim($webPath, '\\/');
}
$archivo_url = $webPath;

// Insertar en DB
try {
    $ins = $conexion->prepare('INSERT INTO entregas (id_actividad, id_alumno, archivo_url, comentario, fecha_entrega) VALUES (:id_act, :id_alum, :archivo, :comentario, NOW())');
    $ins->execute([
        ':id_act' => $id_actividad,
        ':id_alum' => $id_alumno,
        ':archivo' => $archivo_url,
        ':comentario' => $comentario,
    ]);
    $lastEntregaId = $conexion->lastInsertId();
} catch (PDOException $e) {
    if ($e->getCode() === '42S02') {
        // crear tabla minimal
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
        $lastEntregaId = $conexion->lastInsertId();
    } else {
        $err = 'Error en la base de datos: ' . $e->getMessage();
        $to = $id_materia ? $base_path . "views/alumno/ver_materia.php?id={$id_materia}&error=".urlencode($err) : $base_path . 'home/usuarios/index_alumno.php?error=' . urlencode($err);
        header('Location: ' . $to);
        exit();
    }
}

// Redirigir con éxito
$msg = 'entrega_ok';
$to = $id_materia ? $base_path . "views/alumno/ver_materia.php?id={$id_materia}&msg={$msg}" : $base_path . 'home/usuarios/index_alumno.php?msg=' . $msg;
header('Location: ' . $to);
exit();

?>

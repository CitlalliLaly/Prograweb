<?php
session_start();
include '../../Includes/conexion.php';
// Usar URL base absoluta del proyecto
$base_url = '/Proyecto_integral';

// Validar sesión
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ' . $base_url . '/index.php');
    exit();
}

// Si es GET con id, mostrar formulario de entrega; si no, redirigir
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if (isset($_GET['id'])) {
        $id_actividad = intval($_GET['id']);
        if ($id_actividad <= 0) {
            header('Location: ' . $base_path . 'home/usuarios/index_alumno.php');
            exit();
        }
        // Obtener info básica de la actividad y su materia para el formulario
        try {
            $q = $conexion->prepare("SELECT a.titulo, c.id_materia FROM actividades a JOIN cursos c ON a.ID_curso = c.id_curso WHERE a.ID_actividad = :id LIMIT 1");
            $q->execute([':id' => $id_actividad]);
            $ar = $q->fetch(PDO::FETCH_ASSOC);
            $actividad_titulo = $ar['titulo'] ?? 'Entregar tarea';
            $id_materia = $ar['id_materia'] ?? null;
        } catch (Exception $e) {
            $actividad_titulo = 'Entregar tarea';
            $id_materia = null;
        }

        include '../../Includes/header.php';
        ?>
        <div class="container mt-4">
            <h3><?php echo htmlspecialchars($actividad_titulo); ?></h3>
            <form action="/Proyecto_integral/procesar_entrega.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_actividad" value="<?php echo $id_actividad; ?>">
                <div class="mb-3">
                    <label for="archivo" class="form-label">Archivo</label>
                    <input class="form-control" type="file" name="archivo" id="archivo" required>
                </div>
                <div class="mb-3">
                    <label for="comentario" class="form-label">Comentario (opcional)</label>
                    <textarea class="form-control" name="comentario" id="comentario" rows="3"></textarea>
                </div>
                <button class="btn btn-primary" type="submit">Enviar entrega</button>
                <?php if ($id_materia): ?>
                    <a class="btn btn-secondary ms-2" href="<?php echo $base_url . '/views/alumno/ver_materia.php?id=' . $id_materia; ?>">Volver</a>
                <?php else: ?>
                    <a class="btn btn-secondary ms-2" href="<?php echo $base_url . '/home/usuarios/index_alumno.php'; ?>">Volver</a>
                <?php endif; ?>
            </form>
        </div>
        <?php
        include '../../Includes/footer.php';
        exit();
    }
    header('Location: ' . $base_url . '/home/usuarios/index_alumno.php');
    exit();
}

$id_alumno = $_SESSION['id_alumno'] ?? $_SESSION['id_usuario'];
$id_actividad = isset($_POST['id_actividad']) ? intval($_POST['id_actividad']) : 0;
$comentario = isset($_POST['comentario']) ? trim($_POST['comentario']) : null;

    if ($id_actividad <= 0) {
    header('Location: ' . $base_url . '/home/usuarios/index_alumno.php');
    exit();
}

// Determinar id_materia para redirección (opcional)
try {
    $q = $conexion->prepare("SELECT c.id_materia FROM actividades a JOIN cursos c ON a.ID_curso = c.id_curso WHERE a.id_actividad = :id_act LIMIT 1");
    $q->execute([':id_act' => $id_actividad]);
    $row = $q->fetch(PDO::FETCH_ASSOC);
    $id_materia = $row ? $row['id_materia'] : null;
} catch (Exception $e) {
    $id_materia = null;
}

// Validar archivo
if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
    $err = 'No se recibió archivo o hubo un error en la subida.';
    $to = $id_materia ? $base_url . "/views/alumno/ver_materia.php?id={$id_materia}&error=".urlencode($err) : $base_url . '/home/usuarios/index_alumno.php?error=' . urlencode($err);
    header('Location: ' . $to);
    exit();
}

$file = $_FILES['archivo'];
$maxSize = 10 * 1024 * 1024; // 10 MB
if ($file['size'] > $maxSize) {
    $err = 'El archivo excede el tamaño máximo de 10MB.';
    $to = $id_materia ? $base_url . "/views/alumno/ver_materia.php?id={$id_materia}&error=".urlencode($err) : $base_url . '/home/usuarios/index_alumno.php?error=' . urlencode($err);
    header('Location: ' . $to);
    exit();
}

$allowed = ['pdf','doc','docx','zip','rar','png','jpg','jpeg','txt','ppt','pptx'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowed)) {
    $err = 'Tipo de archivo no permitido.';
    $to = $id_materia ? $base_url . "/views/alumno/ver_materia.php?id={$id_materia}&error=".urlencode($err) : $base_url . '/home/usuarios/index_alumno.php?error=' . urlencode($err);
    header('Location: ' . $to);
    exit();
}

// Preparar destino
$uploadsDir = __DIR__ . '/../../assets/uploads';
if (!is_dir($uploadsDir)) {
    mkdir($uploadsDir, 0755, true);
}

$safeName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', basename($file['name']));
$destName = date('Ymd_His') . '_u' . $id_alumno . '_' . $id_actividad . '_' . $safeName;
$destPath = $uploadsDir . DIRECTORY_SEPARATOR . $destName;

if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    $err = 'Error al mover el archivo subido.';
    $to = $id_materia ? $base_path . "views/alumno/ver_materia.php?id={$id_materia}&error=".urlencode($err) : $base_path . 'home/usuarios/index_alumno.php?error=' . urlencode($err);
    header('Location: ' . $to);
    exit();
}

// Guardar registro en la tabla 'entregas'. Si la tabla no existe, crearla.
$archivo_url = $base_url . '/assets/uploads/' . $destName;

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
    // Si la tabla no existe (SQLSTATE 42S02), la creamos y reintentamos
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
        // reintentar
        $ins = $conexion->prepare('INSERT INTO entregas (id_actividad, id_alumno, archivo_url, comentario, fecha_entrega) VALUES (:id_act, :id_alum, :archivo, :comentario, NOW())');
        $ins->execute([
            ':id_act' => $id_actividad,
            ':id_alum' => $id_alumno,
            ':archivo' => $archivo_url,
            ':comentario' => $comentario,
        ]);
        $lastEntregaId = $conexion->lastInsertId();
    } else {
        // otro error
        $err = 'Error en la base de datos: ' . $e->getMessage();
        $to = $id_materia ? $base_path . "views/alumno/ver_materia.php?id={$id_materia}&error=".urlencode($err) : $base_path . 'home/usuarios/index_alumno.php?error=' . urlencode($err);
        header('Location: ' . $to);
        exit();
    }
}

// Éxito: redirigir de vuelta a la materia con mensaje
$msg = 'entrega_ok';
$to = $id_materia ? $base_path . "views/alumno/ver_materia.php?id={$id_materia}&msg={$msg}" : $base_path . 'home/usuarios/index_alumno.php?msg=' . $msg;
header('Location: ' . $to);
exit();

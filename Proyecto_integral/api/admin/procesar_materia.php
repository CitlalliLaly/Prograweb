<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../../login.php');
    exit();
}

include '../../Includes/conexion.php';

// Solo aceptamos POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../views/administrador/materias_list.php');
    exit();
}

$action = $_POST['action'] ?? '';

//  funcion para redireccionar con mensaje
function redirect_list($type, $message) {
    $param = $type === 'err' ? 'err' : 'msg';
    header('Location: ../../views/administrador/materias_list.php?' . $param . '=' . urlencode($message));
    exit();
}

try {
    if ($action === 'create') {
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $creditos = isset($_POST['creditos']) ? intval($_POST['creditos']) : 0;
        $id_departamento = isset($_POST['id_departamento']) ? intval($_POST['id_departamento']) : null;

        if ($nombre === '' || $id_departamento === null || $id_departamento <= 0) {
            redirect_list('err', 'Datos inválidos para crear materia');
        }

        // Evitar duplicados por nombre en mismo departamento
        $dup = $conexion->prepare("SELECT COUNT(*) FROM materia WHERE nombre = :nombre AND id_departamento = :dep");
        $dup->execute([':nombre' => $nombre, ':dep' => $id_departamento]);
        if ((int)$dup->fetchColumn() > 0) {
            redirect_list('err', 'Ya existe una materia con ese nombre en el departamento seleccionado');
        }

        $ins = $conexion->prepare("INSERT INTO materia (nombre, descripcion, creditos, id_departamento) VALUES (:nombre, :descripcion, :creditos, :dep)");
        $ins->execute([':nombre' => $nombre, ':descripcion' => $descripcion, ':creditos' => $creditos, ':dep' => $id_departamento]);

        redirect_list('msg', 'Materia creada');

    } elseif ($action === 'update') {
        $id = (int)($_POST['id_materia'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $creditos = isset($_POST['creditos']) ? intval($_POST['creditos']) : 0;
        $id_departamento = isset($_POST['id_departamento']) ? intval($_POST['id_departamento']) : null;

        if ($id <= 0 || $nombre === '' || $id_departamento === null || $id_departamento <= 0) {
            redirect_list('err', 'Datos inválidos para actualizar materia');
        }

        $stmt = $conexion->prepare("UPDATE materia SET nombre = :nombre, descripcion = :descripcion, creditos = :creditos, id_departamento = :dep WHERE id_materia = :id");
        $stmt->execute([':nombre' => $nombre, ':descripcion' => $descripcion, ':creditos' => $creditos, ':dep' => $id_departamento, ':id' => $id]);

        redirect_list('msg', 'Materia actualizada');

    } elseif ($action === 'delete') {
        $id = (int)($_POST['id_materia'] ?? 0);
        if ($id <= 0) {
            redirect_list('err', 'ID inválido');
        }

        // Verificar si existen cursos asociados
        $c = $conexion->prepare("SELECT COUNT(*) AS cnt FROM cursos WHERE id_materia = :id");
        $c->execute([':id' => $id]);
        $row = $c->fetch(PDO::FETCH_ASSOC);
        if ($row && isset($row['cnt']) && (int)$row['cnt'] > 0) {
            redirect_list('err', 'No se puede eliminar: existen cursos asociados.');
        }

        $d = $conexion->prepare("DELETE FROM materia WHERE id_materia = :id");
        $d->execute([':id' => $id]);

        redirect_list('msg', 'Materia eliminada');

    } else {
        redirect_list('err', 'Acción desconocida');
    }
} catch (PDOException $e) {
    // Errores de BD con mensaje claro
    redirect_list('err', 'Error de base de datos: ' . $e->getMessage());
} catch (Exception $e) {
    redirect_list('err', $e->getMessage());
}

<?php
/* Evitamos la inyeccion sql */
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../../login.php');
    exit();
}
/*conectividad*/
include '../../Includes/conexion.php';

$action = $_POST['action'] ?? '';

if ($action === 'update') {
    $id = (int)($_POST['id_departamento'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    if ($id <= 0 || $nombre === '') {
        header('Location: ../../views/administrador/departamentos_list.php?err=' . urlencode('Datos inválidos'));
        exit();
    }
    try {
        $stmt = $conexion->prepare("UPDATE departamento SET nombre = :nombre WHERE id_departamento = :id");
        $stmt->execute([':nombre' => $nombre, ':id' => $id]);
        header('Location: ../../views/administrador/departamentos_list.php?msg=' . urlencode('Departamento actualizado'));
        exit();
    } catch (PDOException $e) {
        header('Location: ../../views/administrador/departamentos_list.php?err=' . urlencode('Error: ' . $e->getMessage()));
        exit();
    }
} elseif ($action === 'delete') {
    $id = (int)($_POST['id_departamento'] ?? 0);
    if ($id <= 0) {
        header('Location: ../../views/administrador/departamentos_list.php?err=' . urlencode('ID inválido'));
        exit();
    }
    // Verificar dependencias: materias y profesores
    $cntMat = $conexion->prepare("SELECT COUNT(*) FROM materia WHERE id_departamento = :id");
    $cntMat->execute([':id' => $id]);
    $nMat = (int)$cntMat->fetchColumn();

    $cntProf = $conexion->prepare("SELECT COUNT(*) FROM profesores WHERE id_departamento = :id");
    $cntProf->execute([':id' => $id]);
    $nProf = (int)$cntProf->fetchColumn();

    if ($nMat > 0 || $nProf > 0) {
        $msg = 'No se puede eliminar: existen ' . $nMat . ' materias y ' . $nProf . ' profesores vinculados. Reasigne o elimine esas dependencias primero.';
        header('Location: ../../views/administrador/departamentos_list.php?err=' . urlencode($msg));
        exit();
    }

    try {
        $del = $conexion->prepare("DELETE FROM departamento WHERE id_departamento = :id");
        $del->execute([':id' => $id]);
        header('Location: ../../views/administrador/departamentos_list.php?msg=' . urlencode('Departamento eliminado'));
        exit();
    } catch (PDOException $e) {
        header('Location: ../../views/administrador/departamentos_list.php?err=' . urlencode('Error: ' . $e->getMessage()));
        exit();
    }
} elseif ($action === 'assign') {
    // Asignacion profesor a departamento
    $id_dep = (int)($_POST['id_departamento'] ?? 0);
    $id_prof = (int)($_POST['id_profesor'] ?? 0);
    if ($id_dep <= 0 || $id_prof <= 0) {
        header('Location: ../../views/administrador/editar_departamento.php?id=' . $id_dep . '&err=' . urlencode('Datos inválidos'));
        exit();
    }
    try {
        $upd = $conexion->prepare("UPDATE profesores SET id_departamento = :dep WHERE id_profesor = :prof");
        $upd->execute([':dep' => $id_dep, ':prof' => $id_prof]);
        header('Location: ../../views/administrador/editar_departamento.php?id=' . $id_dep . '&msg=' . urlencode('Profesor asignado al departamento'));
        exit();
    } catch (PDOException $e) {
        header('Location: ../../views/administrador/editar_departamento.php?id=' . $id_dep . '&err=' . urlencode('Error: ' . $e->getMessage()));
        exit();
    }
} elseif ($action === 'unassign') {
    // Desasignacion profesor del departamento
    $id_dep = (int)($_POST['id_departamento'] ?? 0);
    $id_prof = (int)($_POST['id_profesor'] ?? 0);
    if ($id_dep <= 0 || $id_prof <= 0) {
        header('Location: ../../views/administrador/editar_departamento.php?id=' . $id_dep . '&err=' . urlencode('Datos inválidos'));
        exit();
    }
    try {
        $upd = $conexion->prepare("UPDATE profesores SET id_departamento = NULL WHERE id_profesor = :prof");
        $upd->execute([':prof' => $id_prof]);
        header('Location: ../../views/administrador/editar_departamento.php?id=' . $id_dep . '&msg=' . urlencode('Profesor desasignado del departamento'));
        exit();
    } catch (PDOException $e) {
        header('Location: ../../views/administrador/editar_departamento.php?id=' . $id_dep . '&err=' . urlencode('Error: ' . $e->getMessage()));
        exit();
    }
} else {
    header('Location: ../../views/administrador/departamentos_list.php');
    exit();
}

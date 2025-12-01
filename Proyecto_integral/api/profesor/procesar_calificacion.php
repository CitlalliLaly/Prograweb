<?php
session_start();
include '../../Includes/conexion.php';

if (!isset($_SESSION['id_usuario']) || !in_array(strtolower($_SESSION['rol']), ['profesor','maestro'])) {
    header('Location: ../../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../home/profesor/calificar.php');
    exit();
}

$id_actividad = isset($_POST['id_actividad']) ? intval($_POST['id_actividad']) : 0;
$ins_ids = isset($_POST['inscripcion_id']) ? $_POST['inscripcion_id'] : [];
$califs = isset($_POST['calificacion']) ? $_POST['calificacion'] : [];
$coms = isset($_POST['comentario']) ? $_POST['comentario'] : [];

$profesor_usuario_id = $_SESSION['id_usuario'];

// Obtener id_profesor
$stmt = $conexion->prepare("SELECT id_profesor FROM usuarios WHERE id_usuario = :uid");
$stmt->execute([':uid' => $profesor_usuario_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$id_profesor = $row ? $row['id_profesor'] : null;

try {
    foreach ($ins_ids as $index => $id_ins) {
        $id_ins = intval($id_ins);
        $cal = isset($califs[$index]) && $califs[$index] !== '' ? floatval($califs[$index]) : null;
        $com = isset($coms[$index]) ? trim($coms[$index]) : null;

        if ($cal === null) continue; // omitir vacíos

        // Verificar si ya existe calificacion
        $check = $conexion->prepare("SELECT ID_calificacion FROM calificaciones WHERE ID_inscripcion = :id_ins AND ID_actividad = :id_act");
        $check->execute([':id_ins' => $id_ins, ':id_act' => $id_actividad]);
        $exists = $check->fetch(PDO::FETCH_ASSOC);

        if ($exists) {
            // Update
            $upd = $conexion->prepare("UPDATE calificaciones SET calificacion_obtenida = :cal, comentarios = :com, ID_profesor_que_califica = :prof WHERE ID_calificacion = :cid");
            $upd->execute([':cal' => $cal, ':com' => $com, ':prof' => $id_profesor, ':cid' => $exists['ID_calificacion']]);
            $calificacionId = $exists['ID_calificacion'];
        } else {
            // Insert
            $ins = $conexion->prepare("INSERT INTO calificaciones (ID_inscripcion, ID_actividad, calificacion_obtenida, ID_profesor_que_califica, comentarios) VALUES (:id_ins, :id_act, :cal, :prof, :com)");
            $ins->execute([':id_ins' => $id_ins, ':id_act' => $id_actividad, ':cal' => $cal, ':prof' => $id_profesor, ':com' => $com]);
            $calificacionId = $conexion->lastInsertId();
        }

        // ----------------------
        // NOTIFICACIONES: avisar al alumno de la nueva calificación
        // ----------------------
        try {
            // Obtener id_alumno desde la inscripción
            $qAl = $conexion->prepare("SELECT ID_alumno FROM inscripciones WHERE ID_inscripcion = :id_ins LIMIT 1");
            $qAl->execute([':id_ins' => $id_ins]);
            $rowAl = $qAl->fetch(PDO::FETCH_ASSOC);
            $id_alumno = $rowAl ? $rowAl['ID_alumno'] : null;

            if ($id_alumno) {
                // Obtener título de la actividad
                $qAct = $conexion->prepare("SELECT titulo FROM actividades WHERE ID_actividad = :id_act LIMIT 1");
                $qAct->execute([':id_act' => $id_actividad]);
                $rAct = $qAct->fetch(PDO::FETCH_ASSOC);
                $tituloAct = $rAct ? $rAct['titulo'] : 'Actividad';

                $titulo_notif = ($exists ? 'Calificación actualizada' : 'Nueva calificación') . ": " . $tituloAct;
                $mensaje = "Tu calificación en '" . $tituloAct . "' es: " . (is_numeric($cal) ? number_format($cal, 2) : $cal);

                $insNotif = $conexion->prepare("INSERT INTO notificaciones (id_usuario, tipo, titulo, mensaje, referencia_tipo, referencia_id) VALUES (:uid, :tipo, :titulo, :mensaje, :rtipo, :rid)");
                $insNotif->execute([
                    ':uid' => $id_alumno,
                    ':tipo' => 'calificacion',
                    ':titulo' => $titulo_notif,
                    ':mensaje' => $mensaje,
                    ':rtipo' => 'calificacion',
                    ':rid' => $calificacionId
                ]);
            }
        } catch (Exception $e) {
            // No detener flujo por fallos en notificaciones
        }
        
        // GAMIFICACIÓN: otorgar insignia si la calificación es destacada (umbral configurable)
        try {
            $umbral = 90; // calificación mínima para obtener insignia (puedes ajustar)
            if (is_numeric($cal) && $cal >= $umbral) {
                // Obtener id_alumno desde la inscripción
                $qAl = $conexion->prepare("SELECT ID_alumno FROM inscripciones WHERE ID_inscripcion = :id_ins LIMIT 1");
                $qAl->execute([':id_ins' => $id_ins]);
                $rowAl = $qAl->fetch(PDO::FETCH_ASSOC);
                $id_alumno = $rowAl ? $rowAl['ID_alumno'] : null;

                if ($id_alumno) {
                    // Asegurar tablas achievements y user_achievements existen (igual que en entregas)
                    $conexion->exec("CREATE TABLE IF NOT EXISTS achievements (
                        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        clave VARCHAR(100) NOT NULL UNIQUE,
                        titulo VARCHAR(150) NOT NULL,
                        descripcion TEXT NULL,
                        icono VARCHAR(100) NULL,
                        puntos INT DEFAULT 0,
                        creado_en DATETIME DEFAULT CURRENT_TIMESTAMP
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

                    $conexion->exec("CREATE TABLE IF NOT EXISTS user_achievements (
                        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        id_usuario INT NOT NULL,
                        id_achievement INT NOT NULL,
                        referencia_tipo VARCHAR(50) NULL,
                        referencia_id INT NULL,
                        otorgado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
                        INDEX (id_usuario),
                        INDEX (id_achievement)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

                    // Asegurar que exista el achievement clave 'calificacion_destacada'
                    $stmtAch = $conexion->prepare("SELECT id FROM achievements WHERE clave = :clave LIMIT 1");
                    $stmtAch->execute([':clave' => 'calificacion_destacada']);
                    $ach = $stmtAch->fetch(PDO::FETCH_ASSOC);
                    if (!$ach) {
                        $insA = $conexion->prepare("INSERT INTO achievements (clave, titulo, descripcion, icono, puntos) VALUES (:clave, :titulo, :desc, :icono, :puntos)");
                        $insA->execute([
                            ':clave' => 'calificacion_destacada',
                            ':titulo' => 'Calificación Destacada',
                            ':desc' => 'Obtuviste una calificación destacada en una actividad.',
                            ':icono' => 'fa-star',
                            ':puntos' => 10
                        ]);
                        $achievementId = $conexion->lastInsertId();
                    } else {
                        $achievementId = $ach['id'];
                    }

                    // Evitar duplicados: comprobar si ya existe este logro para la misma referencia
                    $chkUA = $conexion->prepare("SELECT id FROM user_achievements WHERE id_usuario = :uid AND id_achievement = :aid AND referencia_tipo = 'calificacion' AND referencia_id = :rid LIMIT 1");
                    $chkUA->execute([':uid' => $id_alumno, ':aid' => $achievementId, ':rid' => $calificacionId]);
                    $existsUA = $chkUA->fetch(PDO::FETCH_ASSOC);
                    if (!$existsUA) {
                        $insUA = $conexion->prepare("INSERT INTO user_achievements (id_usuario, id_achievement, referencia_tipo, referencia_id) VALUES (:uid, :aid, 'calificacion', :rid)");
                        $insUA->execute([':uid' => $id_alumno, ':aid' => $achievementId, ':rid' => $calificacionId]);
                    }
                }
            }
        } catch (Exception $e) {
            // No detener el flujo por errores de gamificación
        }
    }
    $_SESSION['mensaje_ok'] = 'Calificaciones guardadas.';
} catch (PDOException $e) {
    $_SESSION['mensaje_error'] = 'Error al guardar calificaciones: ' . $e->getMessage();
}

header('Location: ../../home/profesor/calificar.php');
exit();

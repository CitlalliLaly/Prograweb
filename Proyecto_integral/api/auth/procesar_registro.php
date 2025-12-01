<?php
session_start();

include '../../Includes/conexion.php';

$error = "";
$exito = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Validar que las contraseñas coincidan
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $rol = 'alumno'; // Asignar automáticamente como alumno
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    
    if ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden.";
    } else if (empty($_POST['nombres']) || empty($_POST['a_paterno']) || empty($_POST['username']) || empty($_POST['password']) || empty($email)) {
        $error = "Faltan datos obligatorios (nombre, apellido, username, email).";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "El email no es válido.";
    } else {
        // 2. Preparar datos del alumno
        $nombres = trim($_POST['nombres']);
        $a_paterno = trim($_POST['a_paterno']);
        $a_materno = isset($_POST['a_materno']) ? trim($_POST['a_materno']) : '';
        $username = trim($_POST['username']);
        
        // Datos del padre (opcionales)
        $padre_numero = isset($_POST['padre_numero']) ? trim($_POST['padre_numero']) : null;
        $padre_nombre = isset($_POST['padre_nombre']) ? trim($_POST['padre_nombre']) : null;
        $padre_telefono = isset($_POST['padre_telefono']) ? trim($_POST['padre_telefono']) : null;
        $padre_email = isset($_POST['padre_email']) ? trim($_POST['padre_email']) : null;
        
        // Combinar nombre completo del alumno
        $nombre_completo = $nombres . " " . $a_paterno;
        if (!empty($a_materno)) {
            $nombre_completo .= " " . $a_materno;
        }
        
        // 3. Hashear contraseña
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        
        // 4. Verificar si el usuario ya existe
        $verificar_sql = "SELECT id_usuario FROM usuarios WHERE username = :username";
        $verificar_stmt = $conexion->prepare($verificar_sql);
        $verificar_stmt->execute([':username' => $username]);
        
        if ($verificar_stmt->rowCount() > 0) {
            $error = "El usuario ya existe. Intenta con otro username.";
        } else {
            try {
                // Iniciar transacción
                $conexion->beginTransaction();
                
                $id_alumno = NULL;
                $id_padre = NULL;
                
                // 5a. Insertar padre si se proporcionó información
                if (!empty($padre_nombre)) {
                    $insert_padre = "INSERT INTO padres (nombre, apellido, telefono, correo) 
                                   VALUES (:nombre, :apellido, :telefono, :correo)";
                    $stmt_padre = $conexion->prepare($insert_padre);
                    $stmt_padre->execute([
                        ':nombre' => $padre_nombre,
                        ':apellido' => '', // El nombre completo va en 'nombre'
                        ':telefono' => $padre_telefono,
                        ':correo' => $padre_email
                    ]);
                    $id_padre = $conexion->lastInsertId();
                }
                
                // 5b. Insertar en tabla alumnos
                $insert_alumno = "INSERT INTO alumnos (nombre, apellido, correo) VALUES (:nombre, :apellido, :correo)";
                $stmt_a = $conexion->prepare($insert_alumno);
                $stmt_a->execute([':nombre' => $nombres, ':apellido' => $a_paterno, ':correo' => $email]);
                $id_alumno = $conexion->lastInsertId();
                
                // 5c. Vincular padre con alumno si existe padre
                if ($id_padre !== NULL) {
                    $insert_padre_alumno = "INSERT IGNORE INTO padres_alumnos (ID_alumno, parentesco, padres_idpadres) 
                                          VALUES (:id_alumno, :parentesco, :id_padre)";
                    $stmt_pa = $conexion->prepare($insert_padre_alumno);
                    $stmt_pa->execute([
                        ':id_alumno' => $id_alumno,
                        ':parentesco' => 'Padre/Tutor',
                        ':id_padre' => $id_padre
                    ]);
                }
                
                // 5d. Insertar en tabla usuarios con referencia al perfil
                $insert_sql = "INSERT INTO `usuarios` (`username`, `password`, `rol`, `id_alumno`, `id_padre`)
                              VALUES (:username, :password, :rol, :id_alumno, :id_padre)";
                $insert_stmt = $conexion->prepare($insert_sql);
                $insert_stmt->execute([
                    ':username' => $username,
                    ':password' => $password_hash,
                    ':rol' => $rol,
                    ':id_alumno' => $id_alumno,
                    ':id_padre' => $id_padre
                ]);
                
                // Confirmar transacción
                $conexion->commit();
                
                $exito = "¡Usuario registrado exitosamente! Redirigiendo al login...";
                
                // Redirigir después de 2 segundos
                header("refresh:2;url=login.php");
                
            } catch (PDOException $e) {
                // Revertir transacción en caso de error
                $conexion->rollBack();
                $error = "Error al registrar: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado del Registro | CUSJ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="contenedor-flex-40">
        <div class="contenedor-centrado contenedor-900px">
            <div class="card p-4">
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <h4 class="alert-heading">Error en el registro</h4>
                    <p><?php echo $error; ?></p>
                    <hr>
                    <a href="registro.php" class="btn btn-primary">Volver al registro</a>
                </div>
            <?php elseif ($exito): ?>
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading">¡Éxito!</h4>
                    <p><?php echo $exito; ?></p>
                </div>
            <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

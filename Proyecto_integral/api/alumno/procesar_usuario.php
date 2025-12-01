<?php
    /*
    * Activamos el manejo de sesiones
    */
    session_start();

    try {
        // 1. Conexión a Base de Datos
        require_once "../../Includes/conexion.php"; 

        // 2. Validaciones de entrada
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        if ($password !== $confirm_password) {
            die("Error: Las contraseñas no coinciden. <a href='javascript:history.back()'>Volver</a>");
        }

        if (empty($_POST['nombres']) || empty($_POST['a_paterno']) || empty($_POST['username']) || empty($_POST['password'])) {
            die("Error: Faltan datos obligatorios. <a href='javascript:history.back()'>Volver</a>");
        }

        // 3. Preparación de datos para tu tabla 'usuarios'
        $nombres    = trim($_POST['nombres']);
        $a_paterno  = trim($_POST['a_paterno']);
        $a_materno  = isset($_POST['a_materno']) ? trim($_POST['a_materno']) : '';
        $username_usuario = trim($_POST['username']);

        // Cifrado de contraseña
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // 4. Verificar duplicados (Usando campo 'username')
        $verificar = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE username = :username");
        $verificar->execute([':username' => $username_usuario]);

        if ($verificar->rowCount() > 0) {
            die("El usuario ya está registrado. <a href='javascript:history.back()'>Intentar con otro</a>");
        }

        // 5. Insertar alumno en tabla alumnos
        $sql_alumno = "INSERT INTO alumnos (nombre, apellido, correo) VALUES (:nombre, :apellido, :correo)";
        $stmt_alumno = $conexion->prepare($sql_alumno);
        $stmt_alumno->execute([
            ':nombre' => $nombres,
            ':apellido' => $a_paterno,
            ':correo' => trim($_POST['email'] ?? '')
        ]);
        $id_alumno = $conexion->lastInsertId();

        // 6. Insertar en la tabla usuarios
        $sql = "INSERT INTO usuarios (username, password, rol, id_alumno) 
                VALUES (:username, :password, :rol, :id_alumno)";

        $stmt = $conexion->prepare($sql);

        // Ejecutar consulta
        $resultado = $stmt->execute([
            ':username'   => $username_usuario,
            ':password' => $password_hash,
            ':rol'      => 'Alumno',
            ':id_alumno' => $id_alumno
        ]);

        if ($resultado) {
            echo "<h3>¡Usuario registrado correctamente!</h3>";
            echo "<p>Redirigiendo al login...</p>";
            // Redirección automática
            header("refresh:2;url=../../login.php"); 
        } else {
            echo "Hubo un error al guardar en la base de datos.";
        }

    } catch (PDOException $e) {
        die("Error PDO: " . $e->getMessage());
    }
?>
<?php
session_start();
include '../../Includes/conexion.php';

// Verificar si es administrador PRIMERO para seguridad
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'administrador') {
    $base_path = realpath(__DIR__ . '/../..');
    $relative_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $base_path);
    header('Location: ' . $relative_path . '/login.php');
    exit();
}

$base_path = realpath(__DIR__ . '/../..');
$relative_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $base_path);

include '../../Includes/header.php';
?>

<?php
$mensaje = '';
$error = '';

// Cargar departamentos antes dek select
$depsStmt = $conexion->query("SELECT id_departamento, nombre FROM departamento ORDER BY nombre");
$departamentos = $depsStmt->fetchAll(PDO::FETCH_ASSOC);


// Procesar formulario para agregar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombres = trim($_POST['nombres'] ?? '');
    $a_paterno = trim($_POST['a_paterno'] ?? '');
    $a_materno = trim($_POST['a_materno'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $rol = $_POST['rol'] ?? '';
    
    // Validaciones
    if (empty($nombres) || empty($a_paterno) || empty($username) || empty($email) || empty($password) || empty($rol)) {
        $error = "Todos los campos son obligatorios.";
    } elseif ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden.";
    } elseif (strlen($password) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "El email no es válido.";
    } else {
        // Verificar si el username ya existe
        $check_sql = "SELECT id_usuario FROM usuarios WHERE username = :username";
        $check_stmt = $conexion->prepare($check_sql);
        $check_stmt->execute([':username' => $username]);
        
        if ($check_stmt->rowCount() > 0) {
            $error = "El usuario ya existe.";
        } else {
            try {
                $conexion->beginTransaction();
                
                $id_alumno = NULL;
                $id_profesor = NULL;
                $id_admin = NULL;
                
                // Insertar en tabla correspondiente según rol
                $id_departamento = isset($_POST['id_departamento']) && is_numeric($_POST['id_departamento']) ? (int)$_POST['id_departamento'] : null;

                if ($rol === 'alumno') {
                    $insert_perfil = "INSERT INTO alumnos (nombre, apellido, correo) VALUES (:nombre, :apellido, :correo)";
                    $stmt_perfil = $conexion->prepare($insert_perfil);
                    $stmt_perfil->execute([':nombre' => $nombres, ':apellido' => $a_paterno, ':correo' => $email]);
                    $id_alumno = $conexion->lastInsertId();
                } elseif ($rol === 'profesor') {
                    $insert_perfil = "INSERT INTO profesores (nombre, apellido, correo, id_departamento) VALUES (:nombre, :apellido, :correo, :id_departamento)";
                    $stmt_perfil = $conexion->prepare($insert_perfil);
                    $stmt_perfil->execute([':nombre' => $nombres, ':apellido' => $a_paterno, ':correo' => $email, ':id_departamento' => $id_departamento]);
                    $id_profesor = $conexion->lastInsertId();
                } elseif ($rol === 'administrador') {
                    $insert_perfil = "INSERT INTO administradores (nombre, apellido, correo) VALUES (:nombre, :apellido, :correo)";
                    $stmt_perfil = $conexion->prepare($insert_perfil);
                    $stmt_perfil->execute([':nombre' => $nombres, ':apellido' => $a_paterno, ':correo' => $email]);
                    $id_admin = $conexion->lastInsertId();
                }
                
                // Insertar en tabla usuarios
                $password_hash = password_hash($password, PASSWORD_BCRYPT);
                $insert_usuario = "INSERT INTO usuarios (username, password, rol, id_alumno, id_profesor, id_admin) 
                                   VALUES (:username, :password, :rol, :id_alumno, :id_profesor, :id_admin)";
                $stmt_usuario = $conexion->prepare($insert_usuario);
                $stmt_usuario->execute([
                    ':username' => $username,
                    ':password' => $password_hash,
                    ':rol' => $rol,
                    ':id_alumno' => $id_alumno,
                    ':id_profesor' => $id_profesor,
                    ':id_admin' => $id_admin
                ]);
                
                $conexion->commit();
                $mensaje = "Usuario creado exitosamente.";
                
            } catch (PDOException $e) {
                $conexion->rollBack();
                $error = "Error al crear el usuario: " . $e->getMessage();
            }
        }
    }
}
?>

<div class="contenedor-flex-40">
    <div class="contenedor-centrado-lg">
        <h2 class="titulo-principal-seccion">Agregar Nuevo Usuario</h2>
        <p class="subtitulo-atenuado-pequeño margen-inferior-30">Crea un nuevo usuario en el sistema</p>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($mensaje): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($mensaje); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Formulario -->
        <div class="card border-0 shadow-sm">
            <div class="card-body relleno-30">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label etiqueta-formulario-negrita">Nombres</label>
                            <input type="text" name="nombres" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label etiqueta-formulario-negrita">Apellido Paterno</label>
                            <input type="text" name="a_paterno" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label etiqueta-formulario-negrita">Apellido Materno</label>
                        <input type="text" name="a_materno" class="form-control">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label etiqueta-formulario-negrita">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label etiqueta-formulario-negrita">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label etiqueta-formulario-negrita">Tipo de Usuario</label>
                        <select id="rolSelect" name="rol" class="form-select" required>
                            <option value="">-- Selecciona un rol --</option>
                            <option value="alumno">Alumno</option>
                            <option value="profesor">Profesor</option>
                            <option value="administrador">Administrador</option>
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="prof-dep-select">
                        <label class="form-label etiqueta-formulario-negrita">Departamento (solo para profesores)</label>
                        <select name="id_departamento" class="form-select">
                            <option value="">-- Ninguno --</option>
                            <?php foreach ($departamentos as $dep): ?>
                                <option value="<?php echo $dep['id_departamento']; ?>"><?php echo htmlspecialchars($dep['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label etiqueta-formulario-negrita">Contraseña</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label etiqueta-formulario-negrita">Confirmar Contraseña</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" class="btn-primary-custom boton-formulario ancho-100">
                                Crear Usuario
                            </button>
                        </div>
                        <div class="col-md-6">
                            <a href="<?php echo $relative_path; ?>/views/administrador/index.php" class="btn btn-secondary w-100">
                                Cancelar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script>
            // Mostrar/ocultar select de departamento según rol
            (function(){
                var rol = document.getElementById('rolSelect');
                var depDiv = document.getElementById('prof-dep-select');
                function toggle() {
                    if (!rol) return;
                    if (rol.value === 'profesor') depDiv.classList.remove('d-none');
                    else depDiv.classList.add('d-none');
                }
                if (rol) {
                    rol.addEventListener('change', toggle);
                    toggle();
                }
            })();
        </script>

    </div>
</div>

<?php include '../../Includes/footer.php'; ?>

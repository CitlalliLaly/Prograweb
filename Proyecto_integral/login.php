<?php   
session_start();
include 'Includes/conexion.php'; 
include 'Includes/header.php';

// Si ya está logueado, redirigir al index
if (isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit();
}

$error = "";

// Lógica de Login aca chevere
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (empty($username) || empty($password)) {
        $error = "Username y contraseña son requeridos.";
    } else {
        $sql = "SELECT id_usuario, username, password, rol, id_alumno, id_profesor, id_admin FROM usuarios WHERE username = :username";
        $resultado = $conexion->prepare($sql);
        $resultado->execute([':username' => $username]);

        if ($resultado->rowCount() == 1) {
            $usuario = $resultado->fetch(PDO::FETCH_ASSOC);
            // Verificar contraseña concuerda 
            if (password_verify($password, $usuario['password'])) {
                // Guardar datos en sesión
                $_SESSION['usuario_id'] = $usuario['id_usuario'];
                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['nombre'] = $usuario['username'];
                $_SESSION['id_alumno'] = $usuario['id_alumno'];
                $_SESSION['id_profesor'] = $usuario['id_profesor'];
                $_SESSION['id_admin'] = $usuario['id_admin'];
                // Normalizar rol en minúsculas para lógica de las rutas
                $rol = strtolower($usuario['rol']);
                $_SESSION['rol'] = $rol;

                // Redirigir según rol ya que no podemos enviar a todos al mismo eda
                switch ($rol) {
                    case 'administrador':
                        header('Location: views/administrador/index.php');
                        break;
                    case 'profesor':
                    case 'maestro':
                        header('Location: views/profesor/index.php');
                        break;
                    case 'alumno':
                    case 'estudiante':
                        header('Location: home/usuarios/index_alumno.php');
                        break;
                    default:
                        // Rol no reconocido: llevar al index principal por que no existe
                        header('Location: index.php');
                }
                exit();
            } else {
                $error = "Contraseña incorrecta.";
            }
        } else {
            $error = "El usuario no existe.";
        }
    }
}
?>

<div class="contenedor centrado">
    <div class="caja-bienvenida">
        <div class="color-icono-principal margen-inferior-20">
            <i class="fas fa-user-circle icono-50"></i>
        </div>

        <h2>Iniciar Sesión</h2>
        <p>Ingresa tus credenciales para acceder</p>

        <?php if($error): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            
            <div class="input-group mb-3">
                <span class="input-group-text">
                    <i class="fas fa-envelope color-principal-oscuro"></i>
                </span>
                <input type="varchar" name="username" class="form-control input-altura" placeholder="Username" required>
            </div>

            <div class="input-group mb-4">
                <span class="input-group-text">
                    <i class="fas fa-lock color-principal-oscuro"></i>
                </span>
                <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
            </div>
            <button type="submit" class="btn-primary-custom boton-principal-oscuro ancho-100 boton-formulario">INGRESAR</button>

            <div class="margen-superior-20 texto-pequeno">
                <a href="registro.php" class="enlace-acento">¿No tienes cuenta? Regístrate</a>
                <span class="separator">|</span>
                <a href="recuperar_contrasena.php" class="color-principal-oscuro">¿Olvidaste tu contraseña?</a>
            </div>

        </form>
    </div>
</div>

<?php include 'Includes/footer.php'; ?>
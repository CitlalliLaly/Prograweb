<?php
session_start();
include 'Includes/header.php';
include 'Includes/conexion.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    
    if (empty($username)) {
        $error = 'Por favor ingresa tu nombre de usuario.';
    } else {
        // Buscar el usuario
        $sql = "SELECT id_usuario, username FROM usuarios WHERE username = :username LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([':username' => $username]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario) {
            // Generar token único
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Guardar token en BD (si las columnas no existen, crearlas)
            try {
                // Añadir columna reset_token si no existe
                $col1 = $conexion->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND COLUMN_NAME = 'reset_token'");
                $col1->execute();
                if ($col1->fetchColumn() == 0) {
                    $conexion->exec("ALTER TABLE usuarios ADD COLUMN reset_token VARCHAR(255) NULL");
                }

                // Añadir columna reset_expiry si no existe
                $col2 = $conexion->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND COLUMN_NAME = 'reset_expiry'");
                $col2->execute();
                if ($col2->fetchColumn() == 0) {
                    $conexion->exec("ALTER TABLE usuarios ADD COLUMN reset_expiry DATETIME NULL");
                }

                $update_sql = "UPDATE usuarios SET reset_token = :token, reset_expiry = :expiry WHERE id_usuario = :id";
                $update_stmt = $conexion->prepare($update_sql);
                $update_stmt->execute([
                    ':token' => $token,
                    ':expiry' => $expiry,
                    ':id' => $usuario['id_usuario']
                ]);
            } catch (PDOException $e) {
                $error = 'Error al preparar recuperación: ' . $e->getMessage();
            }
            
            $message = 'Te hemos enviado un enlace de recuperación a tu correo. Si no lo ves, revisa la carpeta de spam.';
            
            // En producción, aquí enviarías un email con el enlace
            // Por ahora mostramos el enlace de recuperación directamente (solo para desarrollo)
            $reset_link = "/Proyecto_integral/reset_contrasena.php?token=" . $token;
            $message .= '<br><br><strong>Enlace de recuperación (desarrollo):</strong> <a href="' . htmlspecialchars($reset_link) . '" class="btn btn-sm btn-primary mt-2">Hacer clic aquí</a>';
        } else {
            $error = 'El usuario no existe en el sistema.';
        }
    }
}
?>

<div class="contenedor-flex-40">
    <div class="caja-bienvenida">
        <h2 class="titulo-principal-grande">Recuperar Contraseña</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>¡Éxito!</strong> <?php echo $message; ?>
            </div>
        <?php else: ?>
            <p class="subtitulo-atenuado margen-inferior-20">Ingresa tu nombre de usuario para recibir un enlace de recuperación.</p>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label etiqueta-formulario-negrita">Nombre de Usuario</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        class="form-control" 
                        required 
                        placeholder="Tu nombre de usuario"
                    >
                </div>
                
                <button type="submit" class="btn-primary-custom boton-principal-oscuro ancho-100 boton-formulario">Enviar Enlace</button>
            </form>
        <?php endif; ?>
        
        <div class="margen-superior-20 texto-centrado-personalizado">
            <a href="<?php echo $relative_path ?? '/Proyecto_integral'; ?>/login.php" class="color-principal-oscuro">← Volver a Login</a>
        </div>
    </div>
</div>

<?php include 'Includes/footer.php'; ?>


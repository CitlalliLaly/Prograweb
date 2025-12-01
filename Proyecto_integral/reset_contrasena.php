<?php
session_start();
include 'Includes/header.php';
include 'Includes/conexion.php';

$error = '';
$success = '';
$token = $_GET['token'] ?? '';
$usuario = null;

// Validar token
if (!empty($token)) {
    $sql = "SELECT id_usuario, username FROM usuarios WHERE reset_token = :token AND reset_expiry > NOW()";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([':token' => $token]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        $error = 'El enlace ha expirado o no es válido. <a href="/Proyecto_integral/recuperar_contrasena.php">Solicitar nuevo enlace</a>';
    }
}

// Procesar nueva contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $usuario) {
    $nueva_contrasena = $_POST['nueva_contrasena'] ?? '';
    $confirmar_contrasena = $_POST['confirmar_contrasena'] ?? '';
    
    if (empty($nueva_contrasena) || empty($confirmar_contrasena)) {
        $error = 'Por favor completa todos los campos.';
    } elseif ($nueva_contrasena !== $confirmar_contrasena) {
        $error = 'Las contraseñas no coinciden.';
    } elseif (strlen($nueva_contrasena) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } else {
        // Actualizar contraseña
        $password_hash = password_hash($nueva_contrasena, PASSWORD_BCRYPT);
        $update_sql = "UPDATE usuarios SET password = :password, reset_token = NULL, reset_expiry = NULL WHERE id_usuario = :id";
        $update_stmt = $conexion->prepare($update_sql);
        $update_stmt->execute([
            ':password' => $password_hash,
            ':id' => $usuario['id_usuario']
        ]);
        
        $success = 'Contraseña actualizada correctamente. <a href="/Proyecto_integral/login.php">Ir a Login</a>';
        $usuario = null; // Ocultar formulario
    }
}
?>

<div class="contenedor-flex-40">
    <div class="caja-bienvenida">
        <h2 class="titulo-principal-grande">Restablecer Contraseña</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error:</strong> <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>¡Éxito!</strong> <?php echo $success; ?>
            </div>
            <?php elseif ($usuario): ?>
            <p class="subtitulo-atenuado margen-inferior-20">Ingresa tu nueva contraseña para <strong><?php echo htmlspecialchars($usuario['username']); ?></strong></p>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="nueva_contrasena" class="form-label etiqueta-formulario-negrita">Nueva Contraseña</label>
                    <input 
                        type="password" 
                        id="nueva_contrasena" 
                        name="nueva_contrasena" 
                        class="form-control" 
                        required 
                        placeholder="Mínimo 6 caracteres"
                    >
                </div>
                
                <div class="mb-3">
                    <label for="confirmar_contrasena" class="form-label etiqueta-formulario-negrita">Confirmar Contraseña</label>
                    <input 
                        type="password" 
                        id="confirmar_contrasena" 
                        name="confirmar_contrasena" 
                        class="form-control" 
                        required 
                        placeholder="Confirma tu contraseña"
                    >
                </div>
                
                <button type="submit" class="btn-primary-custom boton-principal-oscuro ancho-100 boton-formulario">Actualizar Contraseña</button>
            </form>
        <?php endif; ?>
        
        <div class="margen-superior-20 texto-centrado-personalizado">
            <a href="/Proyecto_integral/login.php" class="color-principal-oscuro">← Volver a Login</a>
        </div>
    </div>
</div>

<?php include 'Includes/footer.php'; ?>

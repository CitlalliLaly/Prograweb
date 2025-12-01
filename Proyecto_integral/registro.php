<?php
$page_title = "Registro | CUSJ";
include 'Includes/header.php';
include 'Includes/conexion.php';

// Calcular ruta base relativa correctamente
$base_path_calc = realpath(__DIR__);
$relative_path_calc = str_replace($_SERVER['DOCUMENT_ROOT'], '', $base_path_calc);
?>

<div class="contenedor-flex-40">
    <div class="caja-bienvenida caja-bienvenida--wide">
        <h2 class="titulo-principal-grande">Crear cuenta</h2>
        <p class="margen-inferior-20">Regístrate con tu username para acceder al sistema.</p>

        <form action="<?php echo $relative_path_calc; ?>/api/auth/procesar_registro.php" method="POST">
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

            <div class="mb-3">
                <label class="form-label etiqueta-formulario-negrita">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>

            <div class="mb-3">
                <label class="form-label etiqueta-formulario-negrita">Email</label>
                <input type="email" name="email" class="form-control" placeholder="tu.email@ejemplo.com" required>
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

            <hr>
            <h5 class="titulo-principal-medio">Datos del Padre/Tutor</h5>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label etiqueta-formulario-negrita">Cédula/Número del Padre</label>
                    <input type="text" name="padre_numero" class="form-control" placeholder="Ej: 001-123456-7890">
                </div>
                <div class="col-md-6 mb-3">
                        <label class="form-label etiqueta-formulario-negrita">Nombre del Padre</label>
                    <input type="text" name="padre_nombre" class="form-control" placeholder="Nombre completo">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label etiqueta-formulario-negrita">Teléfono del Padre</label>
                <input type="tel" name="padre_telefono" class="form-control" placeholder="555-1234567">
            </div>

            <div class="mb-3">
                <label class="form-label etiqueta-formulario-negrita">Email del Padre</label>
                <input type="email" name="padre_email" class="form-control" placeholder="padre@ejemplo.com">
            </div>

            <button type="submit" class="btn-register ancho-100 boton-formulario">Crear cuenta</button>
        </form>
        <p class="margen-superior-20">¿Ya tienes cuenta? <a href="login.php" class="enlace-acento">Ingresar</a></p>
    </div>
</div>

<?php include 'Includes/footer.php'; ?>

<?php
session_start();
// Solo administradores
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: /Proyecto_integral/login.php');
    exit();
}

include '../../Includes/conexion.php';
include '../../Includes/header.php';

$msg = $_GET['msg'] ?? '';
$err = $_GET['err'] ?? '';

$stmt = $conexion->prepare("SELECT m.*, d.nombre AS departamento FROM materia m LEFT JOIN departamento d ON m.id_departamento = d.id_departamento ORDER BY m.nombre");
$stmt->execute();
$materias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="contenedor-flex-40">
    <div class="contenedor-centrado-lg">
        <h2 class="titulo-principal-seccion">Materias</h2>
        <p class="subtitulo-atenuado-pequeño margen-inferior-20">Listado de materias. Puedes crear, editar o eliminar.</p>

        <?php if ($msg): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>
        <?php if ($err): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div>
        <?php endif; ?>

        <div class="mb-3 text-end">
            <a href="/Proyecto_integral/views/administrador/agregar_materia.php" class="btn btn-primary">+ Nueva Materia</a>
            <a href="/Proyecto_integral/views/administrador/index.php" class="btn btn-secondary">Volver</a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Créditos</th>
                                <th>Departamento</th>
                                <th class="ancho-180">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($materias as $m): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($m['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($m['descripcion']); ?></td>
                                    <td><?php echo htmlspecialchars($m['creditos'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($m['departamento'] ?? '-'); ?></td>
                                    <td>
                                        <a href="/Proyecto_integral/views/administrador/editar_materia.php?id=<?php echo $m['id_materia']; ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                        <form method="POST" action="/Proyecto_integral/api/admin/procesar_materia.php" class="bloque-en-linea" onsubmit="return confirm('¿Eliminar esta materia? Esto puede remover cursos asociados.');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id_materia" value="<?php echo $m['id_materia']; ?>">
                                            <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../Includes/footer.php'; ?>

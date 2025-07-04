<?php
include('../../../includes/session.php');
verificarRol('cliente');
include('../../../includes/db.php');

$nombre_actual = $_SESSION['nombre'];
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'No disponible';
$mensaje = "";

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_nombre = trim($_POST['nombre']);
    $nueva_contraseña = $_POST['password'];
    $id_usuario = $_SESSION['id'];

    if (!empty($nuevo_nombre)) {
        $stmt = $conn->prepare("UPDATE usuarios SET nombre = ? WHERE id = ?");
        $stmt->bind_param("si", $nuevo_nombre, $id_usuario);
        $stmt->execute();
        $_SESSION['nombre'] = $nuevo_nombre;
        $mensaje .= "Nombre actualizado correctamente. ";
    }

    if (!empty($nueva_contraseña)) {
        $hash = password_hash($nueva_contraseña, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hash, $id_usuario);
        $stmt->execute();
        $mensaje .= "Contraseña actualizada.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil | CinemaWeb</title>
</head>
<body>

<header>
    <nav>
        <div class="logo">Cinema<span class="gold-accent">Web</span></div>
        <div class="nav-links">
            <a href="cliente_inicio.php">Inicio</a>
            <a href="perfil.php" class="active">Perfil</a>
        </div>
        <div class="user-menu">
            <div class="user-info">¡Hola, <?= htmlspecialchars($_SESSION['nombre']) ?>!</div>
            <div class="dropdown">
                <div class="user-avatar"><?= strtoupper(substr($_SESSION['nombre'], 0, 1)) ?></div>
                <div class="dropdown-content">
                    <a href="perfil.php">Mi Perfil</a>
                    <a href="logout.php">Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </nav>
</header>

<main>
    <section class="form-container">
        <h2>Editar Perfil</h2>

        <?php if ($mensaje): ?>
            <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Nuevo Nombre:</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($nombre_actual) ?>" required>
            </div>
            <div class="form-group">
                <label>Nueva Contraseña (opcional):</label>
                <input type="password" name="password" placeholder="Deja en blanco si no deseas cambiarla">
            </div>
            <button type="submit" class="btn-primary">Guardar Cambios</button>
        </form>
    </section>
</main>

<footer>
    <p>&copy; 2025 <span class="gold-accent">CinemaWeb</span>. Todos los derechos reservados.</p>
</footer>

</body>
</html>

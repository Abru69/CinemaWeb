<?php
include('../../../includes/session.php');
verificarRol('admin');
include('../../../includes/db.php');

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $sinopsis = $_POST['sinopsis'];
    $genero = $_POST['genero'];
    $clasificacion = $_POST['clasificacion'];
    $duracion = $_POST['duracion'];
    
    $imagenNombre = $_FILES['imagen']['name'];
    $imagenTemp = $_FILES['imagen']['tmp_name'];
    $destino = "../../../images/" . basename($imagenNombre);

    if (move_uploaded_file($imagenTemp, $destino)) {
        $stmt = $conn->prepare("INSERT INTO peliculas (titulo, sinopsis, genero, clasificacion, duracion, imagen) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $titulo, $sinopsis, $genero, $clasificacion, $duracion, $imagenNombre);

        if ($stmt->execute()) {
            $mensaje = "Película agregada exitosamente.";
        } else {
            $mensaje = "Error al agregar: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $mensaje = "Error al subir la imagen.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Película</title>
    <link rel="stylesheet" href="styles/styles_crear.css">
</head>
<body>
<header>
    <nav>
        <div class="logo">CinemaWeb</div>
        <div class="user-menu">
            <div class="user-info">
                <div class="user-avatar"><?= strtoupper(substr($_SESSION['nombre'], 0, 1)) ?></div>
                <?= htmlspecialchars($_SESSION['nombre']) ?>
            </div>
            <div class="dropdown">
                <span class="admin-badge">Administrador</span>
                <div class="dropdown-content">
                    <a href="../logout.php">Cerrar sesión</a>
                </div>
            </div>
        </div>
    </nav>
</header>

<main>
    <div class="welcome-section">
        <h1>Agregar Nueva Película</h1>
        <p>Completa el formulario para añadir una película</p>
        <a href="index.php" class="btn logout-btn">← Volver al listado</a>
    </div>

    <div class="stats-section">
        <?php if ($mensaje): ?>
            <div style="text-align:center; margin-bottom: 1rem; color: #FFC857;">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data" style="max-width:600px; margin:0 auto;">
            <div class="form-group">
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" required>
            </div>
            <div class="form-group">
                <label for="sinopsis">Sinopsis:</label>
                <textarea id="sinopsis" name="sinopsis" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="genero">Género:</label>
                <input type="text" id="genero" name="genero" required>
            </div>
            <div class="form-group">
                <label for="clasificacion">Clasificación:</label>
                <input type="text" id="clasificacion" name="clasificacion" required>
            </div>
            <div class="form-group">
                <label for="duracion">Duración:</label>
                <input type="text" id="duracion" name="duracion" placeholder="Ej: 2h15m" required>
            </div>
            <div class="form-group">
                <label for="imagen">Imagen (poster):</label>
                <input type="file" id="imagen" name="imagen" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary">Guardar Película</button>
        </form>
    </div>
</main>

<footer>
    <p>&copy; 2025 CinemaWeb. Todos los derechos reservados.</p>
</footer>
</body>
</html>

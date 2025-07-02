<?php
include('../../../includes/db.php');
include('../../../includes/session.php');
verificarRol('admin');

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero = $_POST['numero'];
    $tipo = $_POST['tipo'];
    $capacidad = $_POST['capacidad'];

    $stmt = $conn->prepare("INSERT INTO salas (numero, tipo, capacidad) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $numero, $tipo, $capacidad);

    if ($stmt->execute()) {
        $mensaje = "Sala creada correctamente.";
    } else {
        $mensaje = "Error al crear la sala: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Sala</title>
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
        </div>
    </nav>
</header>

<main>
    <div class="welcome-section">
        <h1>Nueva <span class="gold-accent">Sala</span></h1>
        <a href="index.php" class="btn logout-btn">← Volver al listado</a>
    </div>

    <div class="stats-section">
        <?php if ($mensaje): ?>
            <div class="mensaje mensaje-exito"><?= $mensaje ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="numero">Número de sala:</label>
                <input type="text" id="numero" name="numero" required>
            </div>
            <div class="form-group">
                <label for="tipo">Tipo:</label>
                <input type="text" id="tipo" name="tipo" placeholder="Ej: 2D, 3D, IMAX" required>
            </div>
            <div class="form-group">
                <label for="capacidad">Capacidad:</label>
                <input type="text" id="capacidad" name="capacidad" required>
            </div>
            <button type="submit" class="btn btn-primary">Guardar Sala</button>
        </form>
    </div>
</main>
</body>
</html>

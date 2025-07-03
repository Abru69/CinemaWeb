<?php
include('../../../includes/db.php');
include('../../../includes/session.php');
verificarRol('admin');

$mensaje = "";

$peliculas = $conn->query("SELECT id, titulo FROM peliculas");
$salas = $conn->query("SELECT id, numero FROM salas");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pelicula = $_POST['id_pelicula'];
    $id_sala = $_POST['id_sala'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];

    $stmt = $conn->prepare("INSERT INTO funciones (id, id_pelicula, id_sala, fecha, hora) VALUES (NULL, ?, ?, ?, ?)");
    $stmt->bind_param("iiss", $id_pelicula, $id_sala, $fecha, $hora);

    if ($stmt->execute()) {
        $mensaje = "Función creada exitosamente.";
    } else {
        $mensaje = "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Función</title>
    <link rel="stylesheet" href="styles/styles_crear.css">
</head>
<body>
    <h1>Agregar Función</h1>
    <?php if ($mensaje): ?>
        <p><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>
    <form method="POST">
        <label>Película:</label>
        <select name="id_pelicula" required>
            <?php while($p = $peliculas->fetch_assoc()): ?>
                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['titulo']) ?></option>
            <?php endwhile; ?>
        </select>

        <label>Sala:</label>
        <select name="id_sala" required>
            <?php while($s = $salas->fetch_assoc()): ?>
                <option value="<?= $s['id'] ?>">Sala <?= $s['numero'] ?></option>
            <?php endwhile; ?>
        </select>

        <label>Fecha:</label>
        <input type="date" name="fecha" required>

        <label>Hora:</label>
        <input type="time" name="hora" required>

        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="index.php" class="btn logout-btn">Cancelar</a>
    </form>
</body>
</html>

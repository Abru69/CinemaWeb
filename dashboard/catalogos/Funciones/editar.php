<?php
include('../../../includes/db.php');
include('../../../includes/session.php');
verificarRol('admin');

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM funciones WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$funcion = $res->fetch_assoc();

$peliculas = $conn->query("SELECT id, titulo FROM peliculas");
$salas = $conn->query("SELECT id, numero FROM salas");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pelicula = $_POST['id_pelicula'];
    $id_sala = $_POST['id_sala'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];

    $update = $conn->prepare("UPDATE funciones SET id_pelicula = ?, id_sala = ?, fecha = ?, hora = ? WHERE id = ?");
    $update->bind_param("iissi", $id_pelicula, $id_sala, $fecha, $hora, $id);
    $update->execute();
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Función</title>
    <link rel="stylesheet" href="styles/styles_editar.css">
</head>
<body>
    <h1>Editar Función</h1>
    <form method="POST">
        <label>Película:</label>
        <select name="id_pelicula">
            <?php while($p = $peliculas->fetch_assoc()): ?>
                <option value="<?= $p['id'] ?>" <?= $p['id'] == $funcion['id_pelicula'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['titulo']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Sala:</label>
        <select name="id_sala">
            <?php while($s = $salas->fetch_assoc()): ?>
                <option value="<?= $s['id'] ?>" <?= $s['id'] == $funcion['id_sala'] ? 'selected' : '' ?>>
                    Sala <?= $s['numero'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Fecha:</label>
        <input type="date" name="fecha" value="<?= $funcion['fecha'] ?>">

        <label>Hora:</label>
        <input type="time" name="hora" value="<?= substr($funcion['hora'], 0, 5) ?>">

        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="index.php" class="btn logout-btn">Cancelar</a>
    </form>
</body>
</html>

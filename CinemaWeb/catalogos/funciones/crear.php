<?php
include('../../includes/session.php');
include('../../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pelicula_id = $_POST['pelicula_id'];
    $sala_id = $_POST['sala_id'];
    $hora = $_POST['hora'];

    $query = "INSERT INTO funciones (pelicula_id, sala_id, hora) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $pelicula_id, $sala_id, $hora);

    if ($stmt->execute()) {
        echo "Función creada exitosamente.";
    } else {
        echo "Error al crear la función: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Función - CinemaWeb</title>
    <link rel="stylesheet" href="../../dashboard/styles/admin_styles.css">
</head>
<body>
    <h1>Crear Nueva Función</h1>
    <form method="POST" action="">
        <label for="pelicula_id">Película:</label>
        <input type="number" name="pelicula_id" required>

        <label for="sala_id">Sala:</label>
        <input type="number" name="sala_id" required>

        <label for="hora">Hora:</label>
        <input type="time" name="hora" required>

        <button type="submit">Crear Función</button>
    </form>
    <a href="../index-admin.php">Volver al Panel de Administración</a>
</body>
</html>
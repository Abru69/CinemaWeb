<?php
include('../../includes/session.php');
include('../../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $rol = $_POST['rol'];

    $query = "INSERT INTO usuarios (nombre, email, rol) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $nombre, $email, $rol);

    if ($stmt->execute()) {
        echo "<script>alert('Usuario creado exitosamente'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Error al crear el usuario');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario - CinemaWeb</title>
    <link rel="stylesheet" href="../../dashboard/styles/admin_styles.css">
</head>
<body>
    <header>
        <h1>Crear Nuevo Usuario</h1>
    </header>
    <main>
        <form method="POST" action="">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="rol">Rol:</label>
            <select id="rol" name="rol" required>
                <option value="admin">Admin</option>
                <option value="usuario">Usuario</option>
            </select>

            <button type="submit">Crear Usuario</button>
        </form>
        <a href="index.php">Volver a la lista de usuarios</a>
    </main>
</body>
</html>
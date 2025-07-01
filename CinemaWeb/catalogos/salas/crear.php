<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Sala - CinemaWeb</title>
    <link rel="stylesheet" href="../../dashboard/styles/admin_styles.css">
</head>
<body>
    <header>
        <h1>Crear Nueva Sala</h1>
    </header>

    <main>
        <form action="guardar.php" method="POST">
            <label for="nombre">Nombre de la Sala:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="capacidad">Capacidad:</label>
            <input type="number" id="capacidad" name="capacidad" required>

            <label for="tipo">Tipo de Sala:</label>
            <select id="tipo" name="tipo" required>
                <option value="normal">Normal</option>
                <option value="imax">IMAX</option>
                <option value="4d">4D</option>
            </select>

            <button type="submit">Crear Sala</button>
        </form>

        <a href="../">Volver al Catálogo de Salas</a>
    </main>

    <footer>
        <p>&copy; 2024 CinemaWeb</p>
    </footer>
</body>
</html>
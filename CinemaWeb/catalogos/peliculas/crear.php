<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Película - CinemaWeb</title>
    <link rel="stylesheet" href="../../styles/admin_styles.css">
</head>
<body>
    <header>
        <h1>Crear Nueva Película</h1>
    </header>

    <main>
        <form action="guardar.php" method="POST">
            <label for="titulo">Título:</label>
            <input type="text" id="titulo" name="titulo" required>

            <label for="genero">Género:</label>
            <input type="text" id="genero" name="genero" required>

            <label for="duracion">Duración (min):</label>
            <input type="number" id="duracion" name="duracion" required>

            <label for="sinopsis">Sinopsis:</label>
            <textarea id="sinopsis" name="sinopsis" required></textarea>

            <button type="submit">Guardar Película</button>
        </form>
        <a href="../index.php">Volver al catálogo</a>
    </main>

    <footer>
        <p>&copy; 2024 CinemaWeb</p>
    </footer>
</body>
</html>
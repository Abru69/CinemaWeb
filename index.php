<?php
session_start();
include('includes/db.php'); // Conexión a la base de datos
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CinemaWeb - Cartelera</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
    <header>
        <nav>
            <img style="width: 70px; height: auto; border-radius: 20%;" src="images/CinemaWeb.png" alt="Logo">
            <div class="logo">Cinema<span class="yellow">Web</span></div>
            <div class="nav-links">
                <a href="#cartelera">Cartelera</a>
                <a href="#promociones">Promociones</a>
                <a href="#contacto">Contacto</a>
            </div>
            <div class="auth-buttons">
                <a href="login.php" class="btn btn-secondary">Iniciar Sesión</a>
                <a href="registro.php" class="btn btn-primary">Registrarse</a>
            </div>
        </nav>
    </header>

    <main>
        <section class="hero">
            <h1>Bienvenido a CinemaWeb</h1>
            <p>Disfruta de las mejores películas en la mejor calidad</p>
        </section>

        <section id="cartelera">
            <h2 style="text-align: center; margin-bottom: 2rem; font-size: 2.2rem; color: #FFC857;">Cartelera Actual</h2>
            <div class="movies-grid">
                <?php
                // Consulta segura
                $query = "SELECT * FROM peliculas";
                $stmt = $conn->prepare($query);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($pelicula = $result->fetch_assoc()) {
                        $id = (int)$pelicula['id'];
                        $titulo = htmlspecialchars($pelicula['titulo']);
                        $genero = htmlspecialchars($pelicula['genero']);
                        $duracion = htmlspecialchars($pelicula['duracion']);
                        $imagen = htmlspecialchars($pelicula['imagen']);

                        echo '<div class="movie-card">';
                        echo '  <div class="movie-poster">';
                        echo '    <img src="images/' . $imagen . '" alt="' . $titulo . '" style="width: auto; height: 100%; object-fit: cover;">';
                        echo '  </div>';
                        echo '  <div class="movie-info">';
                        echo '    <h3 class="movie-title">' . $titulo . '</h3>';
                        echo '    <p class="movie-genre">' . $genero . ' • ' . $duracion . '</p>';

                        // Funciones seguras
                        $funciones_query = "SELECT hora FROM funciones WHERE id_pelicula = ? ORDER BY hora ASC";
                        $func_stmt = $conn->prepare($funciones_query);
                        $func_stmt->bind_param("i", $id);
                        $func_stmt->execute();
                        $funciones_result = $func_stmt->get_result();

                        echo '<div class="movie-times">';
                        while ($funcion = $funciones_result->fetch_assoc()) {
                            $hora = htmlspecialchars(substr($funcion['hora'], 0, 5));
                            echo '<span class="time-slot">' . $hora . '</span>';
                        }
                        echo '</div>'; // cierre movie-times
                        echo '  </div>'; // cierre movie-info
                        echo '</div>'; // cierre movie-card

                        $func_stmt->close();
                    }
                } else {
                    echo "<p style='text-align: center;'>No hay películas en cartelera por el momento.</p>";
                }
                $stmt->close();
                ?>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> CinemaWeb. Todos los derechos reservados.</p>
        <p>Dirección: Av. Principal 123, Ciudad • Teléfono: (555) 123-4567</p>
    </footer>
</body>
</html>

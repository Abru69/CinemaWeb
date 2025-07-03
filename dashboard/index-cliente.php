<?php
include('../includes/session.php');
verificarRol('cliente');
include('../includes/db.php');



// Obtener el nombre e inicial
$nombre = $_SESSION['nombre'];
$inicial = strtoupper(substr($nombre, 0, 1));
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CinemaWeb - Mi Cartelera</title>
    <link rel="stylesheet" href="styles/cliente_styles.css">
</head>

<body>
    <header>
        <nav>
            <div class="logo">Cinema<span class="gold-accent">Web</span></div>
            <div class="nav-links">
                <a href="#cartelera">Cartelera</a>
                <a href="catalogos_usuario/reservas/reservas.html">Reservar</a>
            </div>
            <div class="user-menu">
                <div class="user-info">
                    <span>¡Hola, <?= htmlspecialchars($nombre) ?>!</span>
                </div>
                <div class="dropdown">
                    <div class="user-avatar"><?= $inicial ?></div>
                    <div class="dropdown-content">
                        <a href="#perfil">Mi Perfil</a>
                        <a href="#historial">Historial</a>
                        <a href="#configuracion">Configuración</a>
                        <a href="logout.php">Cerrar Sesión</a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <section class="welcome-section">
            <h1>¡Bienvenido de vuelta, <span class="gold-accent"><?= htmlspecialchars($nombre) ?></span>!</h1>
            <p>Disfruta de las mejores películas en la mejor calidad</p>
        </section>

        <section id="cartelera">
            <h2 class="section-title">Cartelera <span class="gold-accent">Actual</span></h2>
            <div style="margin-bottom: 2rem; text-align: center;">
                <input type="text" id="buscador" placeholder="Buscar por título, género o clasificación...">
            </div>
            <!-- Resultados -->
            <div class="movies-grid" id="resultados">
                <!-- Aquí se cargan las películas dinámicamente -->
            </div>
            <div class="movies-grid">
                <?php
                $peliculas = $conn->query("SELECT * FROM peliculas");

                if ($peliculas->num_rows > 0) {
                    while ($peli = $peliculas->fetch_assoc()) {
                        echo '<div class="movie-card">';
                        echo '  <div class="movie-poster">';
                        echo "    <img src='../images/{$peli['imagen']}' alt='{$peli['titulo']}' style='width: AUTO; height: 100%; object-fit: cover;'>";
                        echo '  </div>';
                        echo '  <div class="movie-info">';
                        echo '    <h3 class="movie-title">' . htmlspecialchars($peli['titulo']) . '</h3>';
                        echo '    <p class="movie-genre">' . htmlspecialchars($peli['genero']) . ' • ' . htmlspecialchars($peli['duracion']) . '</p>';
                        echo '    <div class="movie-times">';

                        $id_peli = $peli['id'];
                        $funciones = $conn->query("SELECT hora FROM funciones WHERE id_pelicula = $id_peli ORDER BY hora ASC");
                        if ($funciones && $funciones->num_rows > 0) {
                            while ($f = $funciones->fetch_assoc()) {
                                echo '<span class="time-slot">' . substr($f['hora'], 0, 5) . '</span>';
                            }
                        } else {
                            echo '<span class="time-slot">Próximamente</span>';
                        }

                        echo '    </div>';
                        echo '  </div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p style="text-align:center;">No hay películas disponibles por el momento.</p>';
                }

                $conn->close();
                ?>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 <span class="gold-accent">CinemaWeb</span>. Todos los derechos reservados.</p>
        <p>Dirección: Av. Principal 123, Ciudad • Teléfono: (555) 123-4567</p>
    </footer>
</body>

</html>

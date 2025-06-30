<?php
include('../../../includes/db.php');
include('../../../includes/session.php');
verificarRol('admin');

$resultado = $conn->query("SELECT * FROM peliculas");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Películas - Admin</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">Cinema<span style="color: #FFC857;">Web</span></div>
            <div class="user-menu">
                <div class="user-info">
                    <div class="user-avatar"><?= strtoupper(substr($_SESSION['nombre'], 0, 1)) ?></div>
                    <span><?= htmlspecialchars($_SESSION['nombre']) ?></span>
                </div>
                <div class="dropdown">
                    <span class="admin-badge">Administrador</span>
                    <div class="dropdown-content">
                        <a href="../../index-admin.php">Panel Principal</a>
                        <a href="../../logout.php">Cerrar sesión</a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <div class="welcome-section">
            <h1>Gestión de Películas</h1>
            <p>Aquí puedes ver, agregar, editar o eliminar películas</p>
            <a href="crear.php" class="btn btn-primary">+ Agregar nueva película</a>
        </div>

        <div class="stats-section">
            <h2 class="stats-title">Lista de Películas</h2>
            <div class="stats-grid">
                <?php if ($resultado && $resultado->num_rows > 0): ?>
                    <?php while ($pelicula = $resultado->fetch_assoc()): ?>
                        <div class="stat-item">
                            <div class="stat-number"><?= htmlspecialchars($pelicula['titulo']) ?></div>
                            <div class="stat-label">
                                <?= htmlspecialchars($pelicula['genero']) ?> | <?= htmlspecialchars($pelicula['duracion']) ?>
                            </div>
                            <div>
                                <a href="editar.php?id=<?= $pelicula['id'] ?>" class="btn btn-primary">
                                    ✏️ Editar
                                </a>
                                <a href="eliminar.php?id=<?= $pelicula['id'] ?>" 
                                class="btn logout-btn" 
                                onclick="return confirm('¿Eliminar esta película?')">
                                🗑️ Eliminar
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="stat-item" style="grid-column: 1 / -1;">
                        <div class="stat-number">No hay películas</div>
                        <div class="stat-label">Agrega tu primera película usando el botón de arriba</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 <span style="color: #FFC857;">CineMax</span>. Todos los derechos reservados.</p>
    </footer>
</body>
</html>

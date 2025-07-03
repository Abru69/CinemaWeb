<?php
include('../../../includes/db.php');
include('../../../includes/session.php');
verificarRol('admin');

$resultado = $conn->query("SELECT * FROM salas");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Salas - Administración</title>
    <link rel="stylesheet" href="styles/style.css"> <!-- Usamos el mismo CSS -->
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
            <div class="dropdown">
                <span class="admin-badge">Administrador</span>
                <div class="dropdown-content">
                    <a href="../../index-admin.php">← Volver al panel</a>
                    <a href="../../dashboard/logout.php">Cerrar sesión</a>
                </div>
            </div>
        </div>
    </nav>
</header>

<main>
    <div class="welcome-section">
        <h1>Gestión de <span class="gold-accent">Salas</span></h1>
        <p>Visualiza y administra las salas del cine</p>
        <a href="crear.php" class="btn btn-primary"> Nueva Sala</a>
    </div>

    <div class="stats-section">
        <table style="width:100%; color:white; text-align:left; border-collapse:collapse;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Número</th>
                    <th>Tipo</th>
                    <th>Capacidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($sala = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?= $sala['id'] ?></td>
                        <td><?= $sala['numero'] ?></td>
                        <td><?= $sala['tipo'] ?></td>
                        <td><?= $sala['capacidad'] ?></td>
                        <td>
                            <a href="editar.php?id=<?= $sala['id'] ?>" class="btn btn-primary">Editar</a>
                            <a href="eliminar.php?id=<?= $sala['id'] ?>" class="btn logout-btn" onclick="return confirm('¿Eliminar esta sala?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>

<footer>
    <p>&copy; 2025 CinemaWeb - Administración de Salas</p>
</footer>
</body>
</html>

<?php
include('../includes/session.php');
verificarRol('admin');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - CineMax</title>
    <link rel="stylesheet" href="styles/admin_styles.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                Cinema<span class="gold-accent">Web</span>
                <span class="admin-badge">ADMIN</span>
            </div>
            <div class="user-menu">
                <div class="user-info">
                    <span>Administrador</span>
                </div>
                <div class="dropdown">
                    <div class="user-avatar">A</div>
                    <div class="dropdown-content">
                        <a href="#configuracion">Configuración</a>
                        <a href="#reportes">Reportes</a>
                        <a href="#respaldos">Respaldos</a>
                        <a href="#" onclick="logout()">Cerrar Sesión</a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <section class="welcome-section">
            <h1>Panel de <span class="gold-accent">Administración</span></h1>
            <p>Gestiona todos los aspectos de CineMax desde este panel de control</p>
        </section>

        <section class="stats-section">
            <h2 class="stats-title">Estadísticas del Sistema</h2>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">24</div>
                    <div class="stat-label">Películas Activas</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">8</div>
                    <div class="stat-label">Salas Disponibles</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">156</div>
                    <div class="stat-label">Funciones Programadas</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">1,247</div>
                    <div class="stat-label">Usuarios Registrados</div>
                </div>
            </div>
        </section>

        <section class="admin-grid">
            <div class="admin-card">
                <div class="admin-card-icon">🎬</div>
                <h3>Gestionar Películas</h3>
                <p>Administra el catálogo de películas, agregar nuevos títulos, editar información y controlar la disponibilidad.</p>
                <a href="../catalogos/peliculas/" class="btn btn-primary">Acceder</a>
            </div>

            <div class="admin-card">
                <div class="admin-card-icon">🏛️</div>
                <h3>Gestionar Salas</h3>
                <p>Configura las salas de cine, capacidad, equipamiento y estado de mantenimiento.</p>
                <a href="../catalogos/salas/" class="btn btn-primary">Acceder</a>
            </div>

            <div class="admin-card">
                <div class="admin-card-icon">📅</div>
                <h3>Gestionar Funciones</h3>
                <p>Programa horarios de películas, asigna salas y controla la disponibilidad de funciones.</p>
                <a href="../catalogos/funciones/" class="btn btn-primary">Acceder</a>
            </div>

            <div class="admin-card">
                <div class="admin-card-icon">👥</div>
                <h3>Gestionar Usuarios</h3>
                <p>Administra cuentas de usuarios, permisos, roles y información de clientes registrados.</p>
                <a href="../catalogos/usuarios/" class="btn btn-primary">Acceder</a>
            </div>

            <div class="admin-card">
                <div class="admin-card-icon">📊</div>
                <h3>Reportes y Análisis</h3>
                <p>Genera reportes de ventas, ocupación de salas y estadísticas de rendimiento.</p>
                <a href="#reportes" class="btn btn-primary">Acceder</a>
            </div>

            <div class="admin-card">
                <div class="admin-card-icon">⚙️</div>
                <h3>Configuración</h3>
                <p>Ajusta configuraciones del sistema, precios, promociones y parámetros generales.</p>
                <a href="#configuracion" class="btn btn-primary">Acceder</a>
            </div>
        </section>

        <section style="text-align: center;">
            <a href="logout.php" class="btn logout-btn" onclick="return confirm('¿Estás seguro de que quieres cerrar sesión?')">
                🚪 Cerrar Sesión
            </a>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 <span class="gold-accent">CineMax</span> - Panel de Administración</p>
        <p>Sistema de Gestión Cinematográfica</p>
    </footer>

    <script>
        function logout() {
            if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
                alert('Cerrando sesión...');
                setTimeout(() => {
                    window.location.href = 'logout.php';
                }, 1000);
            }
        }

        // Simular actualización de estadísticas
        function updateStats() {
            const statNumbers = document.querySelectorAll('.stat-number');
            statNumbers.forEach(stat => {
                const currentValue = parseInt(stat.textContent.replace(',', ''));
                const variation = Math.floor(Math.random() * 10) - 5; // -5 a +5
                const newValue = Math.max(0, currentValue + variation);
                stat.textContent = newValue.toLocaleString();
            });
        }

        // Actualizar estadísticas cada 30 segundos (simulación)
        setInterval(updateStats, 30000);
    </script>
</body>
</html>

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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #0a0a14;
            color: white;
            min-height: 100vh;
        }

        header {
            background-color: #141428;
            padding: 1rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #e50914;
        }

        .admin-badge {
            background-color: #FFD700;
            color: #0a0a14;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
            margin-left: 1rem;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #FFD700;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background-color: #e50914;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #141428;
            min-width: 200px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 8px;
            border: 1px solid #252538;
        }

        .dropdown-content a {
            color: white;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            transition: background-color 0.3s;
        }

        .dropdown-content a:hover {
            background-color: #252538;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .welcome-section {
            background: linear-gradient(135deg, #141428 0%, #252538 100%);
            padding: 3rem 2rem;
            border-radius: 15px;
            margin-bottom: 3rem;
            text-align: center;
            border: 1px solid #3a3a5a;
        }

        .welcome-section h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #e50914;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }

        .welcome-section p {
            font-size: 1.2rem;
            color: #d8d8d8;
        }

        .admin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .admin-card {
            background-color: #141428;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid #252538;
            text-align: center;
        }

        .admin-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(229, 9, 20, 0.2);
        }

        .admin-card-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #e50914;
        }

        .admin-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #FFD700;
        }

        .admin-card p {
            color: #d8d8d8;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            font-size: 1rem;
            font-weight: 500;
        }

        .btn-primary {
            background-color: #e50914;
            color: white;
        }

        .btn-primary:hover {
            background-color: #c70812;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(229, 9, 20, 0.3);
        }

        .stats-section {
            background-color: #141428;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 3rem;
            border: 1px solid #252538;
        }

        .stats-title {
            font-size: 1.8rem;
            color: #FFD700;
            margin-bottom: 2rem;
            text-align: center;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .stat-item {
            background-color: #252538;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            border: 1px solid #3a3a5a;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #e50914;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #d8d8d8;
            font-size: 0.9rem;
        }

        .gold-accent {
            color: #FFD700;
        }

        .logout-btn {
            background-color: #252538;
            color: white;
            border: 2px solid #e50914;
        }

        .logout-btn:hover {
            background-color: #e50914;
            color: white;
        }

        footer {
            background-color: #141428;
            text-align: center;
            padding: 2rem;
            margin-top: 3rem;
            color: #d8d8d8;
            border-top: 1px solid #252538;
        }

        @media (max-width: 768px) {
            .admin-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .welcome-section h1 {
                font-size: 2rem;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                CineMax
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
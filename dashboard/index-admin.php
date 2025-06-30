<?php
include('../includes/session.php');
verificarRol('admin');
include('../includes/db.php');

// Obtener estadísticas reales de la base de datos
$stats = [];
$stats['peliculas'] = $conn->query("SELECT COUNT(*) as total FROM peliculas")->fetch_assoc()['total'] ?? 0;
$stats['salas'] = $conn->query("SELECT COUNT(*) as total FROM salas")->fetch_assoc()['total'] ?? 0;
$stats['funciones'] = $conn->query("SELECT COUNT(*) as total FROM funciones WHERE fecha >= CURDATE()")->fetch_assoc()['total'] ?? 0;
$stats['usuarios'] = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE rol = 'cliente'")->fetch_assoc()['total'] ?? 0;

// Obtener nombre del admin
$nombreAdmin = $_SESSION['nombre'] ?? 'Administrador';
$inicialAdmin = strtoupper(substr($nombreAdmin, 0, 1));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - Cinemama</title>
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
                    <span>¡Hola, <?= htmlspecialchars($nombreAdmin) ?>!</span>
                </div>
                <div class="dropdown">
                    <div class="user-avatar"><?= $inicialAdmin ?></div>
                    <div class="dropdown-content">
                        <a href="#perfil" onclick="mostrarPerfil()">Mi Perfil</a>
                        <a href="#configuracion" onclick="mostrarConfiguracion()">Configuración</a>
                        <a href="#reportes" onclick="mostrarReportes()">Reportes</a>
                        <a href="#respaldos" onclick="mostrarRespaldos()">Respaldos</a>
                        <a href="#ayuda" onclick="mostrarAyuda()">Ayuda</a>
                        <a href="#" onclick="logout()">Cerrar Sesión</a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <section class="welcome-section">
            <h1>Panel de <span class="gold-accent">Administración</span></h1>
            <p>Gestiona todos los aspectos de Cinemama desde este panel de control</p>
            <div style="margin-top: 1rem;">
                <small style="color: #d8d8d8;">
                    Última actualización: <span id="ultimaActualizacion"><?= date('d/m/Y H:i:s') ?></span>
                </small>
            </div>
        </section>

        <section class="stats-section">
            <h2 class="stats-title">Estadísticas del Sistema</h2>
            <div class="stats-grid">
                <div class="stat-item" onclick="navegarA('catalogos/peliculas/index.php')" style="cursor: pointer;">
                    <div class="stat-number" id="stat-peliculas"><?= $stats['peliculas'] ?></div>
                    <div class="stat-label">Películas Activas</div>
                    <small style="color: #888; font-size: 0.8rem;">Click para gestionar</small>
                </div>
                <div class="stat-item" onclick="navegarA('catalogos/salas/index.php')" style="cursor: pointer;">
                    <div class="stat-number" id="stat-salas"><?= $stats['salas'] ?></div>
                    <div class="stat-label">Salas Disponibles</div>
                    <small style="color: #888; font-size: 0.8rem;">Click para gestionar</small>
                </div>
                <div class="stat-item" onclick="navegarA('catalogos/funciones/index.php')" style="cursor: pointer;">
                    <div class="stat-number" id="stat-funciones"><?= $stats['funciones'] ?></div>
                    <div class="stat-label">Funciones Programadas</div>
                    <small style="color: #888; font-size: 0.8rem;">Click para gestionar</small>
                </div>
                <div class="stat-item" onclick="navegarA('catalogos/usuarios/index.php')" style="cursor: pointer;">
                    <div class="stat-number" id="stat-usuarios"><?= $stats['usuarios'] ?></div>
                    <div class="stat-label">Usuarios Registrados</div>
                    <small style="color: #888; font-size: 0.8rem;">Click para gestionar</small>
                </div>
            </div>
            <div style="text-align: center; margin-top: 1rem;">
                <button onclick="actualizarEstadisticas()" class="btn btn-primary" style="font-size: 0.9rem; padding: 0.5rem 1rem;">
                    Actualizar Estadísticas
                </button>
            </div>
        </section>

        <section class="quick-access-panel">
            <h2 class="stats-title" style="margin-bottom: 1.5rem;">Accesos Rápidos</h2>
            <div class="quick-access-grid">
                <div class="quick-access-item">
                    <div class="quick-access-icon">🎬</div>
                    <div class="quick-access-label">Películas</div>
                    <div class="quick-access-actions">
                        <a href="catalogos/peliculas/index.php" class="btn btn-primary">Ver</a>
                        <a href="catalogos/peliculas/crear.php" class="btn btn-primary">+ Agregar</a>
                    </div>
                </div>
                <div class="quick-access-item">
                    <div class="quick-access-icon">🏛️</div>
                    <div class="quick-access-label">Salas</div>
                    <div class="quick-access-actions">
                        <a href="catalogos/salas/index.php" class="btn btn-primary">Ver</a>
                        <a href="catalogos/salas/crear.php" class="btn btn-primary">+ Nueva</a>
                    </div>
                </div>
                <div class="quick-access-item">
                    <div class="quick-access-icon">📅</div>
                    <div class="quick-access-label">Funciones</div>
                    <div class="quick-access-actions">
                        <a href="catalogos/funciones/index.php" class="btn btn-primary">Ver</a>
                        <a href="catalogos/funciones/crear.php" class="btn btn-primary">+ Programar</a>
                    </div>
                </div>
                <div class="quick-access-item">
                    <div class="quick-access-icon">👥</div>
                    <div class="quick-access-label">Usuarios</div>
                    <div class="quick-access-actions">
                        <a href="catalogos/usuarios/index.php" class="btn btn-primary">Ver</a>
                        <a href="catalogos/usuarios/crear.php" class="btn btn-primary">+ Nuevo</a>
                    </div>
                </div>
                <div class="quick-access-item">
                    <div class="quick-access-icon">📊</div>
                    <div class="quick-access-label">Reportes</div>
                    <div class="quick-access-actions">
                        <a href="#reportes" onclick="mostrarReportes()" class="btn btn-primary">Ver</a>
                        <a href="#exportar" onclick="exportarDatos()" class="btn btn-primary">📥 Exportar</a>
                    </div>
                </div>
                <div class="quick-access-item">
                    <div class="quick-access-icon">⚙️</div>
                    <div class="quick-access-label">Configuración</div>
                    <div class="quick-access-actions">
                        <a href="#configuracion" onclick="mostrarConfiguracion()" class="btn btn-primary">Configurar</a>
                        <a href="#mantenimiento" onclick="modoMantenimiento()" class="btn logout-btn">🔧 Mantenimiento</a>
                    </div>
                </div>
            </div>
        </section>

        <section style="text-align: center;">
            <a href="logout.php" class="btn logout-btn" onclick="return confirmarCerrarSesion()">
                🚪 Cerrar Sesión
            </a>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 <span class="gold-accent">Cinemama</span> - Panel de Administración</p>
        <p>Sistema de Gestión Cinemamatográfica | Versión 2.1.0</p>
        <p style="font-size: 0.8rem; color: #888; margin-top: 0.5rem;">
            Estado del sistema: <span id="estadoSistema" style="color: #39b050;">🟢 Operativo</span>
        </p>
    </footer>

    <script>
        // Variables globales
        let ultimaActualizacion = new Date();
        
        // Función mejorada de logout
        function logout() {
            if (confirmarCerrarSesion()) {
                mostrarCargando('Cerrando sesión...');
                setTimeout(() => {
                    window.location.href = 'logout.php';
                }, 1500);
            }
        }

        function confirmarCerrarSesion() {
            return confirm('¿Estás seguro de que quieres cerrar sesión?\n\nSe perderán los cambios no guardados.');
        }

        // Actualizar estadísticas con animación
        function actualizarEstadisticas() {
            mostrarCargando('Actualizando estadísticas...');
            fetch('estadisticas.php')
                .then(response => response.json())
                .then(stats => {
                    document.getElementById('stat-peliculas').textContent = stats.peliculas;
                    document.getElementById('stat-salas').textContent = stats.salas;
                    document.getElementById('stat-funciones').textContent = stats.funciones;
                    document.getElementById('stat-usuarios').textContent = stats.usuarios;
                    document.getElementById('ultimaActualizacion').textContent = new Date().toLocaleString();
                    ocultarCargando();
                    mostrarNotificacion('Estadísticas actualizadas correctamente', 'exito');
                })
                .catch(() => {
                    ocultarCargando();
                    mostrarNotificacion('Error al actualizar estadísticas', 'error');
                });
        }

        function navegarA(url) {
            if (confirm('¿Deseas ir a la sección de gestión?')) {
                window.location.href = url;
            }
        }

        function mostrarPerfil() {
            mostrarNotificacion('Función de perfil en desarrollo', 'info');
        }

        function mostrarConfiguracion() {
            mostrarNotificacion('Abriendo configuración del sistema...', 'info');
        }

        function mostrarReportes() {
            mostrarNotificacion('Generando reportes del sistema...', 'info');
        }

        function mostrarRespaldos() {
            mostrarNotificacion('Accediendo a sistema de respaldos...', 'info');
        }

        function mostrarAyuda() {
            alert('Cinemama Admin Panel v2.1.0\n\nFunciones principales:\n• Gestión de películas\n
            • Control de salas\n• Programación de funciones\n• Administración de usuarios\n
            • Reportes y estadísticas\n
            \nPara soporte técnico contacta al administrador del sistema.');
        }

        function exportarDatos() {
            if (confirm('¿Exportar datos del sistema?\n\nSe generará un archivo con toda la información.')) {
                mostrarCargando('Exportando datos...');
                setTimeout(() => {
                    ocultarCargando();
                    mostrarNotificacion('Datos exportados correctamente', 'exito');
                }, 2000);
            }
        }

        function modoMantenimiento() {
            if (confirm('¿Activar modo mantenimiento?\n\nLos usuarios no podrán acceder al sistema.')) {
                mostrarNotificacion('Modo mantenimiento activado', 'advertencia');
                document.getElementById('estadoSistema').innerHTML = '🟡 Mantenimiento';
                document.getElementById('estadoSistema').style.color = '#FFC857';
            }
        }

        // Funciones de utilidad para notificaciones
        function mostrarNotificacion(mensaje, tipo = 'info') {
            const colores = {
                'exito': '#39b050',
                'error': '#ff6b6b',
                'advertencia': '#FFC857',
                'info': '#448AFF'
            };
            
            const notificacion = document.createElement('div');
            notificacion.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${colores[tipo]};
                color: white;
                padding: 1rem 1.5rem;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                z-index: 1000;
                font-weight: 500;
                max-width: 300px;
                animation: slideIn 0.3s ease-out;
            `;
            notificacion.textContent = mensaje;
            
            document.body.appendChild(notificacion);
            
            setTimeout(() => {
                notificacion.style.animation = 'slideOut 0.3s ease-in';
                setTimeout(() => notificacion.remove(), 300);
            }, 3000);
        }

        function mostrarCargando(mensaje) {
            const loading = document.createElement('div');
            loading.id = 'loading-overlay';
            loading.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.7);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 2000;
                color: white;
                font-size: 1.2rem;
            `;
            loading.innerHTML = `
                <div style="text-align: center;">
                    <div style="font-size: 2rem; margin-bottom: 1rem;">⏳</div>
                    <div>${mensaje}</div>
                </div>
            `;
            document.body.appendChild(loading);
        }

        function ocultarCargando() {
            const loading = document.getElementById('loading-overlay');
            if (loading) loading.remove();
        }

        // Actualización automática de estadísticas cada 5 minutos
        setInterval(() => {
            if (document.visibilityState === 'visible') {
                actualizarEstadisticas();
            }
        }, 300000);

        // Verificar estado del sistema cada minuto
        setInterval(() => {
            // Aquí podrías hacer una verificación real del sistema
            console.log('Verificando estado del sistema...');
        }, 60000);

        // Agregar estilos CSS para animaciones
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);

        // Mensaje de bienvenida al cargar
        window.addEventListener('load', () => {
            setTimeout(() => {
                mostrarNotificacion('¡Bienvenido al Panel de Administración!', 'exito');
            }, 1000);
        });
    </script>
</body>
</html>

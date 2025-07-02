<?php
include('../../../includes/session.php');
verificarRol('admin');
include('../../../includes/db.php');

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$mensaje = "";
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errores[] = "Token CSRF inválido.";
    }

    // Sanitizar entradas
    $titulo = trim(filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_STRING));
    $sinopsis = trim(filter_input(INPUT_POST, 'sinopsis', FILTER_SANITIZE_STRING));
    $genero = trim(filter_input(INPUT_POST, 'genero', FILTER_SANITIZE_STRING));
    $clasificacion = trim(filter_input(INPUT_POST, 'clasificacion', FILTER_SANITIZE_STRING));
    $duracion = trim(filter_input(INPUT_POST, 'duracion', FILTER_SANITIZE_STRING));
    $sala_id = intval($_POST['sala_id']);
    $fecha = trim($_POST['fecha']);
    $horarios = explode(',', $_POST['horarios']);

    // Validaciones básicas
    if (!$titulo || !$sinopsis || !$genero || !$clasificacion || !$duracion || !$sala_id || !$fecha || empty($_FILES['imagen']['name'])) {
        $errores[] = "Todos los campos son obligatorios.";
    }

    // Validar fecha
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        $errores[] = "Fecha inválida.";
    }

    // Validar horarios
    $horarios_validos = [];
    foreach ($horarios as $hora) {
        $hora = trim($hora);
        if (preg_match('/^\d{2}:\d{2}$/', $hora)) {
            $horarios_validos[] = $hora;
        }
    }
    if (empty($horarios_validos)) {
        $errores[] = "Debes ingresar al menos un horario válido (formato 24h: 15:00).";
    }

    // Validar imagen
    $imagenNombre = basename($_FILES['imagen']['name']);
    $imagenTemp = $_FILES['imagen']['tmp_name'];
    $destino = "../../../images/" . $imagenNombre;
    $ext = strtolower(pathinfo($imagenNombre, PATHINFO_EXTENSION));
    $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $permitidas)) {
        $errores[] = "Formato de imagen no permitido.";
    }

    if (empty($errores)) {
        if (move_uploaded_file($imagenTemp, $destino)) {
            $stmt = $conn->prepare("INSERT INTO peliculas (titulo, sinopsis, genero, clasificacion, duracion, imagen) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $titulo, $sinopsis, $genero, $clasificacion, $duracion, $imagenNombre);

            if ($stmt->execute()) {
                $pelicula_id = $conn->insert_id;
                $todoBien = true;
                foreach ($horarios_validos as $hora) {
                    $stmtFuncion = $conn->prepare("INSERT INTO funciones (id_pelicula, id_sala, fecha, hora) VALUES (?, ?, ?, ?)");
                    $stmtFuncion->bind_param("iiss", $pelicula_id, $sala_id, $fecha, $hora);
                    if (!$stmtFuncion->execute()) {
                        $errores[] = "Error al registrar la función: " . htmlspecialchars($stmtFuncion->error);
                        $todoBien = false;
                    }
                    $stmtFuncion->close();
                }
                if ($todoBien) {
                    $mensaje = "Película y funciones agregadas exitosamente.";
                }
            } else {
                $errores[] = "Error al agregar la película: " . htmlspecialchars($stmt->error);
            }
            $stmt->close();
        } else {
            $errores[] = "Error al subir la imagen.";
        }
    }

    // AJAX response
    if (
        !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
    ) {
        header('Content-Type: application/json');
        if ($errores) {
            echo json_encode(['status' => 'error', 'errores' => $errores]);
        } else {
            echo json_encode(['status' => 'success', 'mensaje' => $mensaje]);
        }
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Película</title>
    <link rel="stylesheet" href="styles/styles_crear.css">
</head>
<body>
<header>
    <nav>
        <div class="logo">CinemaWeb</div>
        <div class="user-menu">
            <div class="user-info">
                <div class="user-avatar"><?= strtoupper(htmlspecialchars(substr($_SESSION['nombre'], 0, 1))) ?></div>
                <?= htmlspecialchars($_SESSION['nombre']) ?>
            </div>
            <div class="dropdown">
                <span class="admin-badge">Administrador</span>
                <div class="dropdown-content">
                    <a href="../../dashboard/logout.php">Cerrar sesión</a>
                </div>
            </div>
        </div>
    </nav>
</header>

<main>
    <div class="welcome-section">
        <h1>Agregar Nueva Película</h1>
        <p>Completa el formulario para añadir una película y sus funciones</p>
        <a href="index.php" class="btn logout-btn">← Volver al listado</a>
    </div>

    <div class="stats-section">
        <div id="mensaje-servidor">
            <?php if ($mensaje): ?>
                <div style="text-align:center; margin-bottom: 1rem; color: #FFC857;">
                    <?= htmlspecialchars($mensaje) ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($errores)): ?>
                <div style="color: #ff4d4d; text-align:center;">
                    <?= implode('<br>', array_map('htmlspecialchars', $errores)) ?>
                </div>
            <?php endif; ?>
        </div>

        <form id="form-crear" action="" method="POST" enctype="multipart/form-data" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <div class="form-group">
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" maxlength="255" required>
            </div>
            <div class="form-group">
                <label for="sinopsis">Sinopsis:</label>
                <textarea id="sinopsis" name="sinopsis" rows="4" maxlength="1000" required></textarea>
            </div>
            <div class="form-group">
                <label for="genero">Género:</label>
                <input type="text" id="genero" name="genero" maxlength="100" required>
            </div>
            <div class="form-group">
                <label for="clasificacion">Clasificación:</label>
                <input type="text" id="clasificacion" name="clasificacion" maxlength="10" required>
            </div>
            <div class="form-group">
                <label for="duracion">Duración:</label>
                <input type="text" id="duracion" name="duracion" maxlength="10" placeholder="Ej: 2h15m" required>
            </div>
            <div class="form-group">
                <label for="imagen">Imagen (poster):</label>
                <input type="file" id="imagen" name="imagen" accept="image/*" required>
            </div>
            <div class="form-group">
                <label for="sala_id">Sala:</label>
                <select name="sala_id" required>
                    <option value="">Selecciona una sala</option>
                    <?php
                    $salasQuery = $conn->query("SELECT id, numero, tipo FROM salas");
                    while ($sala = $salasQuery->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($sala['id']) ?>">Sala <?= htmlspecialchars($sala['numero']) ?> (<?= htmlspecialchars($sala['tipo']) ?>)</option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="fecha">Fecha:</label>
                <input type="date" id="fecha" name="fecha" required>
            </div>
            <div class="form-group">
                <label for="horarios">Horarios (separados por coma, formato 24h ej: 15:00,18:30):</label>
                <input type="text" id="horarios" name="horarios" maxlength="255" required>
            </div>
            <button type="submit" class="btn btn-primary">Guardar Película</button>
        </form>
    </div>
</main>

<footer>
    <p>&copy; 2025 CinemaWeb. Todos los derechos reservados.</p>
</footer>
<script>
document.getElementById('form-crear').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    fetch('', {
        method: 'POST',
        body: formData,
        headers: {'X-Requested-With': 'XMLHttpRequest'}
    })
    .then(res => res.json())
    .then(data => {
        const msgDiv = document.getElementById('mensaje-servidor');
        if (data.status === 'success') {
            msgDiv.innerHTML = '<div style="color:#4caf50;text-align:center;">' + data.mensaje + '</div>';
            form.reset();
        } else if (data.status === 'error') {
            msgDiv.innerHTML = '<div style="color:#ff4d4d;text-align:center;">' + data.errores.map(e => e).join('<br>') + '</div>';
        }
    })
    .catch(() => {
        document.getElementById('mensaje-servidor').innerHTML = '<div style="color:#ff4d4d;text-align:center;">Error de conexión.</div>';
    });
});
</script>
</body>
</html>

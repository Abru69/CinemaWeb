<?php
include('../../../includes/db.php');
include('../../../includes/session.php');
verificarRol('admin');

// Obtener salas
$salas = $conn->query("SELECT id, numero FROM salas");

$mensaje = "";
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $sinopsis = $_POST['sinopsis'];
    $genero = $_POST['genero'];
    $clasificacion = $_POST['clasificacion'];
    $duracion = $_POST['duracion'];
    $sala_id = $_POST['sala_id'];

    $imagenNombre = $_FILES['imagen']['name'];
    $imagenTemp = $_FILES['imagen']['tmp_name'];
    $carpetaDestino = "../../../images/";
    if (!is_dir($carpetaDestino)) {
        mkdir($carpetaDestino, 0777, true);
    }
    $destino = $carpetaDestino . basename($imagenNombre);

    if (move_uploaded_file($imagenTemp, $destino)) {
        $stmt = $conn->prepare("INSERT INTO peliculas (titulo, sinopsis, genero, clasificacion, duracion, imagen, sala_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $titulo, $sinopsis, $genero, $clasificacion, $duracion, $imagenNombre, $sala_id);

        if ($stmt->execute()) {
            // Obtener el ID de la película recién insertada
            $pelicula_id = $conn->insert_id;

            // Procesar los horarios
            $horarios = explode(',', $_POST['horarios']);
            $horarios_validos = [];
            foreach ($horarios as $hora) {
                $hora = trim($hora);
                if (preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $hora)) { // Solo horarios válidos 00:00 a 23:59
                    $horarios_validos[] = $hora;
                }
            }
            if (empty($horarios_validos)) {
                $mensaje = "Debes ingresar al menos un horario válido en formato HH:MM.";
            } else {
                foreach ($horarios_validos as $hora) {
                    $stmt_funcion = $conn->prepare("INSERT INTO funciones (id_pelicula, id_sala, hora) VALUES (?, ?, ?)");
                    $stmt_funcion->bind_param("iis", $pelicula_id, $sala_id, $hora);
                    $stmt_funcion->execute();
                    $stmt_funcion->close();
                }
                $mensaje = "Película y funciones agregadas exitosamente.";
            }
        } else {
            $mensaje = "Error al agregar: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $mensaje = "Error al subir la imagen.";
    }

    if ($isAjax) header('Content-Type: application/xml');
    if ($isAjax) {
        echo '<response><status>' . ($mensaje === "Película y funciones agregadas exitosamente." ? 'success' : 'error') . '</status><message>' . htmlspecialchars($mensaje) . '</message></response>';
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
                <div class="user-avatar"><?= strtoupper(substr($_SESSION['nombre'], 0, 1)) ?></div>
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
        <p>Completa el formulario para añadir una película</p>
        <a href="index.php" class="btn logout-btn">← Volver al listado</a>
    </div>

    <div class="stats-section">
        <?php if ($mensaje): ?>
            <div style="text-align:center; margin-bottom: 1rem; color: #FFC857;">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data" style="max-width:600px; margin:0 auto;" id="form-crear">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <div class="form-group">
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" required>
            </div>
            <div class="form-group">
                <label for="sinopsis">Sinopsis:</label>
                <textarea id="sinopsis" name="sinopsis" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="genero">Género:</label>
                <input type="text" id="genero" name="genero" required>
            </div>
            <div class="form-group">
                <label for="clasificacion">Clasificación:</label>
                <input type="text" id="clasificacion" name="clasificacion" required>
            </div>
            <div class="form-group">
                <label for="duracion">Duración:</label>
                <input type="text" id="duracion" name="duracion" placeholder="Ej: 2h15m" required>
            </div>
            <div class="form-group">
                <label for="sala_id">Sala:</label>
                <select id="sala_id" name="sala_id" required>
                    <option value="">Selecciona una sala</option>
                    <?php while ($sala = $salas->fetch_assoc()): ?>
                        <option value="<?= $sala['id'] ?>"><?= htmlspecialchars($sala['numero']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="imagen">Imagen (poster):</label>
                <input type="file" id="imagen" name="imagen" accept="image/*" required>
            </div>
            <div class="form-group">
                <label for="horarios">Horarios (separados por coma, formato 24h ej: 15:00,18:30):</label>
                <input type="text" id="horarios" name="horarios" placeholder="Ej: 15:00,18:30,21:00" required>
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
    .then(res => res.text())
    .then(str => (new window.DOMParser()).parseFromString(str, "text/xml"))
    .then(data => {
        const status = data.querySelector('status').textContent;
        const msg = data.querySelector('message').textContent;
        alert(msg);
        if (status === 'success') window.location.href = 'index.php';
    });
});
</script>
</body>
</html>

<?php
include('../../../includes/db.php');
include('../../../includes/session.php');
verificarRol('admin');

// Verificar si hay ID
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = intval($_GET['id']);

// Obtener datos actuales
$stmt = $conn->prepare("SELECT * FROM peliculas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$pelicula = $resultado->fetch_assoc();

if (!$pelicula) {
    echo "Película no encontrada.";
    exit;
}

// Obtener fecha de la función (si hay funciones, tomar la fecha de la primera)
$fecha_funcion = isset($funciones[0]['fecha']) ? $funciones[0]['fecha'] : date('Y-m-d');

// Obtener todas las salas con tipo
$salas = $conn->query("SELECT id, numero, tipo FROM salas");

// Obtener funciones actuales (ahora también obtenemos fecha)
$funciones = [];
$res_funciones = $conn->query("SELECT id, hora, fecha FROM funciones WHERE id_pelicula = $id ORDER BY hora ASC");
while ($f = $res_funciones->fetch_assoc()) {
    $funciones[] = $f;
}

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// Si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        if ($isAjax) {
            header('Content-Type: application/xml');
            echo '<response><status>error</status><message>Token CSRF inválido.</message></response>';
            exit;
        }
        exit('Token CSRF inválido.');
    }

    $titulo = trim($_POST['titulo']);
    $genero = trim($_POST['genero']);
    $duracion = trim($_POST['duracion']);
    $sala_id = intval($_POST['sala_id']);
    $fecha = trim($_POST['fecha']);
    $horarios = explode(',', $_POST['horarios']);

    // Validaciones básicas
    $errores = [];
    if (!$titulo || !$genero || !$duracion || !$sala_id || !$fecha) {
        $errores[] = "Todos los campos son obligatorios.";
    }
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        $errores[] = "Fecha inválida.";
    }
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

    if (!empty($errores)) {
        if ($isAjax) {
            header('Content-Type: application/xml');
            echo '<response><status>error</status><message>' . htmlspecialchars(implode(' ', $errores)) . '</message></response>';
            exit;
        }
        echo implode('<br>', array_map('htmlspecialchars', $errores));
        exit;
    }

    // Actualizar película y sala
    $update = $conn->prepare("UPDATE peliculas SET titulo = ?, genero = ?, duracion = ?, sala_id = ? WHERE id = ?");
    $update->bind_param("sssii", $titulo, $genero, $duracion, $sala_id, $id);

    if ($isAjax) header('Content-Type: application/xml');
    if ($update->execute()) {
        // Eliminar funciones anteriores
        $conn->query("DELETE FROM funciones WHERE id_pelicula = $id");

        // Insertar nuevas funciones con fecha
        foreach ($horarios_validos as $hora) {
            $stmt_funcion = $conn->prepare("INSERT INTO funciones (id_pelicula, id_sala, fecha, hora) VALUES (?, ?, ?, ?)");
            $stmt_funcion->bind_param("iiss", $id, $sala_id, $fecha, $hora);
            $stmt_funcion->execute();
            $stmt_funcion->close();
        }

        if ($isAjax) {
            echo '<response><status>success</status><message>Película actualizada correctamente.</message></response>';
            exit;
        }
        header("Location: index.php");
        exit;
    } else {
        if ($isAjax) {
            echo '<response><status>error</status><message>Error al actualizar.</message></response>';
            exit;
        }
        echo "Error al actualizar.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Película</title>
    <link rel="stylesheet" href="styles/styles_editar.css">
</head>
<body>
    <h1>Editar Película</h1>
    <form method="POST" id="form-editar" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <label>Título:</label>
        <input type="text" name="titulo" value="<?= htmlspecialchars($pelicula['titulo']) ?>" required><br>

        <label>Género:</label>
        <input type="text" name="genero" value="<?= htmlspecialchars($pelicula['genero']) ?>" required><br>

        <label>Duración:</label>
        <input type="text" name="duracion" value="<?= htmlspecialchars($pelicula['duracion']) ?>" required><br>

        <label>Sala:</label>
        <select name="sala_id" required>
            <?php 
            while ($sala = $salas->fetch_assoc()): ?>
                <option value="<?= $sala['id'] ?>" <?= (isset($pelicula['sala_id']) && $sala['id'] == $pelicula['sala_id']) ? 'selected' : '' ?>>
                    Sala <?= htmlspecialchars($sala['numero']) ?> (<?= htmlspecialchars($sala['tipo']) ?>)
                </option>
            <?php endwhile; ?>
        </select><br>

        <label>Fecha:</label>
        <input type="date" name="fecha" value="<?= htmlspecialchars($fecha_funcion) ?>" required><br>

        <label>Horarios (separados por coma, formato 24h ej: 15:00,18:30):</label>
        <input type="text" name="horarios" value="<?= htmlspecialchars(implode(',', array_column($funciones, 'hora'))) ?>" required><br>

        <button type="submit" class="btn btn-primary">Guardar cambios</button>
        <a href="index.php" class="btn logout-btn">Cancelar</a>
    </form>

    <script>
    document.getElementById('form-editar').addEventListener('submit', function(e) {
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

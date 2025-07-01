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

// Obtener todas las salas
$salas = $conn->query("SELECT id, numero FROM salas");

// Obtener funciones actuales
$funciones = [];
$res_funciones = $conn->query("SELECT id, hora FROM funciones WHERE id_pelicula = $id ORDER BY hora ASC");
while ($f = $res_funciones->fetch_assoc()) {
    $funciones[] = $f;
}

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// Si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $genero = $_POST['genero'];
    $duracion = $_POST['duracion'];
    $sala_id = $_POST['sala_id'];
    $horarios = explode(',', $_POST['horarios']);

    // Actualizar película y sala
    $update = $conn->prepare("UPDATE peliculas SET titulo = ?, genero = ?, duracion = ?, sala_id = ? WHERE id = ?");
    $update->bind_param("sssii", $titulo, $genero, $duracion, $sala_id, $id);

    if ($isAjax) header('Content-Type: application/xml');
    if ($update->execute()) {
        // Eliminar funciones anteriores
        $conn->query("DELETE FROM funciones WHERE id_pelicula = $id");

        // Insertar nuevas funciones
        foreach ($horarios as $hora) {
            $hora = trim($hora);
            if (preg_match('/^\d{2}:\d{2}$/', $hora)) {
                $stmt_funcion = $conn->prepare("INSERT INTO funciones (id_pelicula, id_sala, hora) VALUES (?, ?, ?)");
                $stmt_funcion->bind_param("iis", $id, $sala_id, $hora);
                $stmt_funcion->execute();
                $stmt_funcion->close();
            }
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
    <form method="POST" id="form-editar">
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
                <option value="<?= $sala['id'] ?>" <?= $sala['id'] == $pelicula['sala_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($sala['numero']) ?>
                </option>
            <?php endwhile; ?>
        </select><br>

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

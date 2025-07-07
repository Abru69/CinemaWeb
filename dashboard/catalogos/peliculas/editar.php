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

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// Si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        if ($isAjax) {
            header('Content-Type: application/xml');
            ob_clean();
            echo '<response><status>error</status><message>Token CSRF inválido.</message></response>';
            exit;
        }
        exit('Token CSRF inválido.');
    }

    $titulo = trim($_POST['titulo']);
    $genero = trim($_POST['genero']);
    $sinopsis = trim($_POST['sinopsis']);
    $clasificacion = trim($_POST['clasificacion']);
    $duracion_min = intval($_POST['duracion_min']);
    $fecha_estreno = $_POST['fecha_estreno'];

    // Validaciones básicas
    $errores = [];
    if (!$titulo || !$genero || !$sinopsis || !$clasificacion || !$duracion_min || !$fecha_estreno) {
        $errores[] = "Todos los campos son obligatorios.";
    }

    // Procesar imagen si se sube una nueva
    $imagenNombre = $pelicula['imagen_url'];
    if (!empty($_FILES['imagen']['name'])) {
        $imagenNombre = basename($_FILES['imagen']['name']);
        $imagenTemp = $_FILES['imagen']['tmp_name'];
        $destino = "../../../images/" . $imagenNombre;
        $ext = strtolower(pathinfo($imagenNombre, PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($ext, $permitidas)) {
            $errores[] = "Formato de imagen no permitido.";
        } else {
            if (!move_uploaded_file($imagenTemp, $destino)) {
                $errores[] = "Error al subir la imagen.";
            }
        }
    }

    if (!empty($errores)) {
        if ($isAjax) {
            header('Content-Type: application/xml');
            ob_clean();
            echo '<response><status>error</status><message>' . htmlspecialchars(implode(' ', $errores)) . '</message></response>';
            exit;
        }
        echo implode('<br>', array_map('htmlspecialchars', $errores));
        exit;
    }

    // Actualizar película con fecha_estreno
    $update = $conn->prepare("UPDATE peliculas SET titulo = ?, genero = ?, sinopsis = ?, clasificacion = ?, duracion_min = ?, imagen_url = ?, fecha_estreno = ? WHERE id = ?");
    $update->bind_param("ssssissi", $titulo, $genero, $sinopsis, $clasificacion, $duracion_min, $imagenNombre, $fecha_estreno, $id);

    if ($isAjax) header('Content-Type: application/xml');
    if ($update->execute()) {
        if ($isAjax) {
            ob_clean();
            echo '<response><status>success</status><message>Película actualizada correctamente.</message></response>';
            exit;
        }
        header("Location: index.php");
        exit;
    } else {
        if ($isAjax) {
            ob_clean();
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
<main>
    <div class="stats-section">
        <h1>Editar Película</h1>
        <form method="POST" id="form-editar" enctype="multipart/form-data" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="form-group">
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" value="<?= htmlspecialchars($pelicula['titulo']) ?>" required>
            </div>
            <div class="form-group">
                <label for="genero">Género:</label>
                <input type="text" id="genero" name="genero" value="<?= htmlspecialchars($pelicula['genero']) ?>" required>
            </div>
            <div class="form-group">
                <label for="sinopsis">Sinopsis:</label>
                <textarea id="sinopsis" name="sinopsis" rows="4" required><?= htmlspecialchars($pelicula['sinopsis']) ?></textarea>
            </div>
            <div class="form-group">
                <label for="clasificacion">Clasificación:</label>
                <input type="text" id="clasificacion" name="clasificacion" value="<?= htmlspecialchars($pelicula['clasificacion']) ?>" required>
            </div>
            <div class="form-group">
                <label for="duracion_min">Duración (minutos):</label>
                <input type="number" id="duracion_min" name="duracion_min" value="<?= htmlspecialchars($pelicula['duracion_min']) ?>" min="1" required>
            </div>
            <div class="form-group">
                <label for="imagen">Imagen (poster):</label>
                <input type="file" id="imagen" name="imagen" accept="image/*">
                <small>Si no seleccionas una imagen, se mantendrá la actual.</small>
            </div>
            <div class="form-group">
                <label for="fecha_estreno">Fecha de Estreno:</label>
                <input type="date" id="fecha_estreno" name="fecha_estreno" 
       value="<?= !empty($pelicula['fecha_estreno']) ? htmlspecialchars($pelicula['fecha_estreno']) : '' ?>" required>

            </div>
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
            <a href="index.php" class="btn logout-btn">Cancelar</a>
        </form>
    </div>
</main>

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
    .then(str => {
        let data, status, msg;
        try {
            data = (new window.DOMParser()).parseFromString(str, "text/xml");
            status = data.querySelector('status')?.textContent;
            msg = data.querySelector('message')?.textContent;
            if (!status || !msg) throw new Error();
        } catch {
            alert('Ocurrió un error inesperado. Intenta nuevamente.');
            return;
        }
        alert(msg);
        if (status === 'success') window.location.href = 'index.php';
    });
});
</script>
</body>
</html>

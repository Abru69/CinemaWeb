<?php
include('../../../includes/db.php');
include('../../../includes/session.php');
verificarRol('admin');

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM salas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$sala = $result->fetch_assoc();

if (!$sala) {
    echo "Sala no encontrada.";
    exit;
}

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        if ($isAjax) {
            header('Content-Type: application/xml');
            echo '<response><status>error</status><message>Token CSRF inválido.</message></response>';
            exit;
        }
        exit('Token CSRF inválido.');
    }

    $numero = trim($_POST['numero']);
    $tipo = trim($_POST['tipo']);
    $capacidad = trim($_POST['capacidad']);

    $errores = [];
    if (!$numero || !$tipo || !$capacidad) {
        $errores[] = "Todos los campos son obligatorios.";
    }
    if (!ctype_digit($capacidad)) {
        $errores[] = "La capacidad debe ser un número válido.";
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

    $stmt = $conn->prepare("UPDATE salas SET numero = ?, tipo = ?, capacidad = ? WHERE id = ?");
    $stmt->bind_param("isii", $numero, $tipo, $capacidad, $id);

    if ($isAjax) header('Content-Type: application/xml');
    if ($stmt->execute()) {
        if ($isAjax) {
            echo '<response><status>success</status><message>Sala actualizada correctamente.</message></response>';
            exit;
        }
        header("Location: index.php");
        exit;
    } else {
        if ($isAjax) {
            echo '<response><status>error</status><message>Error al actualizar la sala.</message></response>';
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
    <title>Editar Sala</title>
    <link rel="stylesheet" href="../peliculas/styles/styles_crear.css">
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
        </div>
    </nav>
</header>

<main>
    <div class="welcome-section">
        <h1>Editar <span class="gold-accent">Sala</span></h1>
        <a href="index.php" class="btn logout-btn">← Volver</a>
    </div>

    <div class="stats-section">
        <form method="POST" id="form-editar">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="form-group">
                <label>Número de sala:</label>
                <input type="text" name="numero" value="<?= htmlspecialchars($sala['numero']) ?>" required>
            </div>
            <div class="form-group">
                <label>Tipo:</label>
                <input type="text" name="tipo" value="<?= htmlspecialchars($sala['tipo']) ?>" required>
            </div>
            <div class="form-group">
                <label>Capacidad:</label>
                <input type="text" name="capacidad" value="<?= htmlspecialchars($sala['capacidad']) ?>" required>
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
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
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
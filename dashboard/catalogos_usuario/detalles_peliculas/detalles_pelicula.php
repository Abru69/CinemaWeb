<?php
include('../../../includes/session.php');
verificarRol('cliente');
include('../../../includes/db.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index-cliente.php');
    exit;
}

$id_pelicula = intval($_GET['id']);

// Obtener datos de la película
$stmt = $conn->prepare("SELECT p.*, s.numero AS sala_numero, s.tipo AS sala_tipo, s.capacidad, f.id AS funcion_id, f.fecha, f.hora 
                        FROM peliculas p
                        LEFT JOIN funciones f ON p.id = f.id_pelicula
                        LEFT JOIN salas s ON f.id_sala = s.id
                        WHERE p.id = ?
                        LIMIT 1");
$stmt->bind_param("i", $id_pelicula);
$stmt->execute();
$resultado = $stmt->get_result();
$pelicula = $resultado->fetch_assoc();

if (!$pelicula) {
    echo "Película no encontrada.";
    exit;
}

// Simulación de asientos reservados
$reservados = [3, 5, 8]; // reemplazar con consulta real más adelante
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pelicula['titulo']) ?> - Detalles</title>
    <link rel="stylesheet" href="styles/styles.css">
    
</head>
<body>

<header>
    <nav>
        <div class="logo">Cinema<span class="gold-accent">Web</span></div>
        <div class="nav-links">
            <a href="../../index-cliente.php">Inicio</a>
            <a href="../catalogos_usuario/reservas/reservas.html">Reservar</a>
        </div>
        <div class="user-menu">
            <span>Hola, <?= $_SESSION['nombre'] ?></span>
        </div>
    </nav>
</header>

<main>
    <section class="welcome-section">
        <h1><?= htmlspecialchars($pelicula['titulo']) ?></h1>
        <p><?= htmlspecialchars($pelicula['genero']) ?> • <?= htmlspecialchars($pelicula['clasificacion']) ?> • <?= htmlspecialchars($pelicula['duracion']) ?></p>
        <p><strong>Sala:</strong> <?= $pelicula['sala_numero'] ?> (<?= $pelicula['sala_tipo'] ?>)</p>
        <p><strong>Fecha:</strong> <?= $pelicula['fecha'] ?> | <strong>Hora:</strong> <?= substr($pelicula['hora'], 0, 5) ?></p>
        <img src="../images/<?= $pelicula['imagen'] ?>" style="max-width:200px; border-radius:8px;" alt="<?= $pelicula['titulo'] ?>">
        <p style="margin-top:1rem;"><?= htmlspecialchars($pelicula['sinopsis']) ?></p>
    </section>

    <section class="stats-section">
        <h2>Selecciona tus asientos</h2>
        <form action="../catalogos_usuario/reservas/procesar_reserva.php" method="POST">
            <input type="hidden" name="id_funcion" value="<?= $pelicula['funcion_id'] ?>">
            <div class="asientos">
                <?php
                $capacidad = $pelicula['capacidad'] ?? 0;
                for ($i = 1; $i <= $capacidad; $i++) {
                    $clase = in_array($i, $reservados) ? 'asiento reservado' : 'asiento';
                    echo "<div class='$clase' data-num='$i'>$i</div>";
                }
                ?>
            </div>
            <input type="hidden" name="asientos" id="asientosSeleccionados">
            <button type="submit" class="btn btn-primary" style="margin-top: 2rem;">Reservar</button>
        </form>
    </section>
</main>

<script>
    const asientos = document.querySelectorAll('.asiento:not(.reservado)');
    const inputSeleccionados = document.getElementById('asientosSeleccionados');
    let seleccionados = [];

    asientos.forEach(asiento => {
        asiento.addEventListener('click', () => {
            const num = asiento.dataset.num;
            if (seleccionados.includes(num)) {
                seleccionados = seleccionados.filter(n => n !== num);
                asiento.classList.remove('seleccionado');
            } else {
                seleccionados.push(num);
                asiento.classList.add('seleccionado');
            }
            inputSeleccionados.value = seleccionados.join(',');
        });
    });
</script>

<footer>
    <p>&copy; 2025 CinemaWeb. Todos los derechos reservados.</p>
</footer>

</body>
</html>

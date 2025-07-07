<?php
session_start();

// Generar token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Si es una petición AJAX POST (desde JavaScript), procesar login y devolver XML
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    header('Content-Type: text/xml');
    echo '<?xml version="1.0" encoding="UTF-8"?>';

    include('includes/db.php');

    // Validar token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo '<response><status>error</status><message>Token CSRF inválido.</message></response>';
        exit;
    }

    // Sanitizar entrada
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, nombre, correo, contraseña, rol FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado && $resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        if (password_verify($password, $usuario['contraseña'])) {
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['correo'] = $usuario['correo']; 
            $_SESSION['rol'] = $usuario['rol'];

            $redirect = match ($usuario['rol']) {
                'admin' => 'dashboard/index-admin.php',
                'empleado' => 'dashboard/index-editor.php',
                default => 'dashboard/index-cliente.php',
            };

            echo "<response><status>success</status><message>$redirect</message></response>";
        } else {
            echo "<response><status>error</status><message>Contraseña incorrecta.</message></response>";
        }
    } else {
        echo "<response><status>error</status><message>Usuario no encontrado.</message></response>";
    }

    $stmt->close();
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CinemaWeb - Iniciar Sesión</title>
    <link rel="stylesheet" href="styles/login_styles.css">
</head>

<body>
    <div class="back-home">
        <a href="index.php">← Volver al inicio</a>
    </div>

    <div class="login-container">
        <div class="logo">Cinema<span class="gold-accent">Web</span></div>

        <form id="loginForm">
            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>

            <!-- Token CSRF oculto -->
            <input type="hidden" id="csrf_token" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <button type="submit" class="btn">Iniciar Sesión</button>
        </form>

        <div id="responseMessage" style="text-align:center; margin-top:10px;"></div>

        <div class="forgot-password">
            <a href="#">¿Olvidaste tu contraseña?</a>
        </div>

        <div class="divider">
            <span>o</span>
        </div>

        <div class="register-link">
            ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>
        </div>
    </div>

    <script>
    document.getElementById('loginForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const csrf_token = document.getElementById('csrf_token').value;

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'login.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest'); // Marca la petición como AJAX

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const xml = xhr.responseXML;
                const status = xml.getElementsByTagName('status')[0].textContent;
                const message = xml.getElementsByTagName('message')[0].textContent;

                if (status === 'success') {
                    window.location.href = message;
                } else {
                    document.getElementById('responseMessage').innerHTML =
                        `<span style="color:red;">${message}</span>`;
                }
            }
        };

        const params = `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}&csrf_token=${encodeURIComponent(csrf_token)}`;
        xhr.send(params);
    });
    </script>
</body>
</html>

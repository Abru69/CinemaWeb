<?php
include('../../includes/session.php');
verificarRol('cliente');
include('../../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_SESSION['id'];
    $id_funcion = intval($_POST['id_funcion']);
    $asientos = $_POST['asientos'] ?? [];

    if (empty($asientos)) {
        echo json_encode(['status' => 'error', 'message' => 'No se seleccionaron asientos.']);
        exit;
    }

    // Prevenir asientos duplicados
    $stmt_check = $conn->prepare("SELECT asiento FROM reservas WHERE id_funcion = ? AND asiento = ?");
    $stmt_insert = $conn->prepare("INSERT INTO reservas (id_usuario, id_funcion, asiento) VALUES (?, ?, ?)");

    $insertados = 0;
    foreach ($asientos as $asiento) {
        $stmt_check->bind_param("is", $id_funcion, $asiento);
        $stmt_check->execute();
        $stmt_check->store_result();
        if ($stmt_check->num_rows === 0) {
            // Asiento disponible, insertar
            $stmt_insert->bind_param("iis", $id_usuario, $id_funcion, $asiento);
            if ($stmt_insert->execute()) {
                $insertados++;
            }
        }
    }

    $stmt_check->close();
    $stmt_insert->close();
    $conn->close();

    if ($insertados > 0) {
        echo json_encode(['status' => 'success', 'message' => "$insertados asiento(s) reservados correctamente."]);
    } else {
        echo json_encode(['status' => 'error', 'message' => "Los asientos seleccionados ya están ocupados."]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
}
?>

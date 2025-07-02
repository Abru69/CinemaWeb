<?php
include('../../../includes/session.php');
verificarRol('admin');
include('../../../includes/db.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM salas WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error al eliminar: " . $stmt->error;
    }
}
?>

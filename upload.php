
<?php
if (isset($_FILES['file'])) {
    $uploadDir = 'uploads/'; // Asegúrate de que este directorio exista y tenga permisos
    $uploadFile = $uploadDir . basename($_FILES['file']['name']);

    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
        echo json_encode(['success' => true, 'file' => $uploadFile]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al subir el archivo']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No se recibió ningún archivo']);
}
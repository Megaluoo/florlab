<?php
// upload_result.php
session_start();

// 1. Proteger
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    die("Acceso denegado.");
}

// 2. Definir carpeta de destino
$uploadDir = 'resultados/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true); // Crear la carpeta si no existe
}

// 3. Validar datos
if (isset($_POST['order_number']) && !empty($_POST['order_number']) && isset($_FILES['result_pdf']) && $_FILES['result_pdf']['error'] == 0) {
    
    // 4. Sanitizar número de orden
    $order_number = preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['order_number']);
    
    // 5. Validar que sea PDF
    $fileInfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $fileInfo->file($_FILES['result_pdf']['tmp_name']);
    
    if ($mimeType == 'application/pdf') {
        
        // 6. Crear nombre de archivo final
        $filename = $order_number . '.pdf';
        $targetPath = $uploadDir . $filename;
        
        // 7. Mover el archivo
        if (move_uploaded_file($_FILES['result_pdf']['tmp_name'], $targetPath)) {
            $msg = urlencode("Éxito: El resultado $filename fue cargado.");
            header("Location: admin.php?msg=$msg&type=success");
        } else {
            $msg = urlencode("Error: No se pudo mover el archivo al servidor.");
            header("Location: admin.php?msg=$msg&type=error");
        }
    } else {
        $msg = urlencode("Error: El archivo no es un PDF válido.");
        header("Location: admin.php?msg=$msg&type=error");
    }
} else {
    $msg = urlencode("Error: Faltaron datos o hubo un error al subir el archivo.");
    header("Location: admin.php?msg=$msg&type=error");
}
?>
<?php
// download.php

if (isset($_POST['order_number'])) {
    
    // 1. Sanitizar el número de orden (¡MUY IMPORTANTE!)
    // Esto solo permite números. Evita que busquen "..\..\etc.pdf"
    $order_number = preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['order_number']);

    if (empty($order_number)) {
        die("Número de orden inválido.");
    }

    // 2. Definir la ruta del archivo
    // Como pediste: "222456.pdf"
    $filename = $order_number . '.pdf';
    $filepath = 'resultados/' . $filename; // La carpeta que creaste

    // 3. Comprobar si el archivo existe
    if (file_exists($filepath)) {
        
        // 4. Enviar los headers para forzar la descarga
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($filepath) . '"'); // 'inline' intenta abrirlo en el navegador
        // header('Content-Disposition: attachment; filename="' . basename($filepath) . '"'); // 'attachment' fuerza la descarga
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        
        // 5. Leer el archivo y enviarlo
        flush(); // Limpiar buffer
        readfile($filepath);
        exit;
        
    } else {
        // Archivo no encontrado
        echo "Error: Resultado no encontrado. Verifique su número de orden.";
    }
} else {
    // No se envió un número de orden
    echo "Error: No se proporcionó un número de orden.";
}
?>
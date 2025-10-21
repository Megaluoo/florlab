<?php
// edit_exam.php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    die("Acceso denegado.");
}

$msg = "Error: Datos incompletos.";
$type = "error";

if (isset($_POST['index']) && is_numeric($_POST['index']) && isset($_POST['name']) && !empty($_POST['name']) && isset($_POST['keywords']) && isset($_POST['price_usd'])) {
    
    $index = (int)$_POST['index'];
    $jsonFile = 'tests.json';
    $tests = [];
    
    if (file_exists($jsonFile)) {
        $tests = json_decode(file_get_contents($jsonFile), true);
    }

    if (isset($tests[$index])) {
        
        // --- ¡CORRECCIÓN IMPORTANTE! ---
        // Convertir la coma (20,60) a un punto (20.60) para guardarlo
        $price_string = (string)$_POST['price_usd'];
        $price_string_safe = str_replace(',', '.', $price_string);
        $price_float = (float)$price_string_safe;
        // --- FIN DE LA CORRECCIÓN ---

        $updated_exam = [
            'name'      => (string) $_POST['name'],
            'keywords'  => array_map('trim', explode(',', (string) $_POST['keywords'])),
            'price_usd' => number_format($price_float, 2, '.', '') // Guardar siempre con punto
        ];

        $tests[$index] = $updated_exam;

        if (file_put_contents($jsonFile, json_encode($tests, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
            $msg = "Éxito: El exámen '" . htmlspecialchars($updated_exam['name']) . "' fue actualizado.";
            $type = "success";
        } else {
            $msg = "Error: No se pudo escribir en el archivo tests.json. (Verifica los permisos)";
        }

    } else {
        $msg = "Error: El exámen que intentas editar no existe.";
    }
}

header("Location: admin.php?msg=" . urlencode($msg) . "&type=$type");
exit;
?>
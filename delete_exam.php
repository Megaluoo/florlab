<?php
// delete_exam.php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    die("Acceso denegado.");
}

$msg = "Error: No se especificó el exámen a borrar.";
$type = "error";

if (isset($_POST['index']) && is_numeric($_POST['index'])) {
    
    $index = (int)$_POST['index'];
    $jsonFile = 'tests.json';
    $tests = [];
    
    if (file_exists($jsonFile)) {
        $tests = json_decode(file_get_contents($jsonFile), true);
    }

    if (isset($tests[$index])) {
        $examName = $tests[$index]['name'];
        array_splice($tests, $index, 1);

        if (file_put_contents($jsonFile, json_encode($tests, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
            $msg = "Éxito: El exámen '" . htmlspecialchars($examName) . "' fue borrado.";
            $type = "success";
        } else {
            $msg = "Error: No se pudo escribir en el archivo tests.json. (Verifica los permisos)";
        }

    } else {
        $msg = "Error: El exámen seleccionado no existe (índice inválido).";
    }
}

header("Location: admin.php?msg=" . urlencode($msg) . "&type=$type");
exit;
?>
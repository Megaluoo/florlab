<?php
// edit_form.php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.html');
    exit;
}

if (!isset($_GET['index']) || !is_numeric($_GET['index'])) {
    header("Location: admin.php?msg=" . urlencode("Error: Exámen no válido.") . "&type=error");
    exit;
}

$index = (int)$_GET['index'];
$jsonFile = 'tests.json';
$tests = [];

if (file_exists($jsonFile)) {
    $tests = json_decode(file_get_contents($jsonFile), true);
}

if (!isset($tests[$index])) {
    header("Location: admin.php?msg=" . urlencode("Error: El exámen no existe.") . "&type=error");
    exit;
}

$exam = $tests[$index];
$keywords_string = implode(', ', $exam['keywords']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Exámen - Florlab</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { background-color: var(--light-color); padding: 20px; }
        .admin-container { max-width: 800px; margin: 20px auto; }
        .admin-card { background: var(--white-color); padding: 30px; border-radius: var(--border-radius); box-shadow: var(--shadow); margin-bottom: 30px; }
        .admin-card h2 { text-align: left; margin-bottom: 20px; border-bottom: 2px solid var(--primary-color); padding-bottom: 10px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 5px; }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #DDD;
            font-family: var(--font-family);
        }
        .form-group textarea { resize: vertical; min-height: 80px; }
        .form-group small { color: #777; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-card">
            <h2>Editando: <?php echo htmlspecialchars($exam['name']); ?></h2>
            <form action="edit_exam.php" method="POST">
                <input type="hidden" name="index" value="<?php echo $index; ?>">
                <div class="form-group">
                    <label for="name">Nombre del Exámen</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($exam['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="keywords">Palabras Clave (Keywords)</label>
                    <textarea id="keywords" name="keywords" required><?php echo htmlspecialchars($keywords_string); ?></textarea>
                    <small>Separar cada palabra o frase con una coma. (Ej: hematologia, sangre, cbc)</small>
                </div>
                <div class="form-group">
                    <label for="price_usd">Precio (USD)</label>
                    <input type="text" id="price_usd" name="price_usd" value="<?php echo htmlspecialchars(number_format($exam['price_usd'], 2, ',', '')); ?>" required>
                    <small>Puedes usar coma o punto decimal. (Ej: 10,50)</small>
                </div>
                <button type="submit" class="cta-button primary">Guardar Cambios</button>
                <a href="admin.php" class="cta-button secondary">Cancelar</a>
            </form>
        </div>
    </div>
</body>
</html>
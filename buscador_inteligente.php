<?php
$serverName = "srvflorlab";
$database = "EnterpriseTest";
$uid = "sa";
$pwd = "@enterprise123456";

try {
    $conn = new PDO("sqlsrv:server=$serverName;Database=$database;TrustServerCertificate=true", $uid, $pwd);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(Exception $e) {
    die("Error de conexión: " . $e->getMessage());
}

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Detector de Estructura Enterprise</title>";
echo "<style>
    body { font-family: Arial; padding: 20px; background: #ecf0f1; }
    .box { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; overflow-x: auto; }
    h2 { color: #2c3e50; }
    table { border-collapse: collapse; width: 100%; white-space: nowrap; }
    th, td { border: 1px solid #ddd; padding: 6px; text-align: left; font-size: 13px; }
    th { background-color: #e74c3c; color: white; }
    tr:nth-child(even) { background-color: #f2f2f2; }
</style></head><body>";

echo "<h2>🕵️‍♂️ Mapeo Profundo de Datos</h2>";
echo "<p>El sistema Enterprise oculta los nombres de las columnas (las llama Lab21c1, Lab21c2, etc.). Vamos a ver los datos reales para adivinar cuál es cuál.</p>";

// Tablas clave conocidas de Enterprise
$tablas_clave = ['Lab21', 'Lab22', 'Lab39', 'Lab113', 'Lab103', 'Lab11'];

foreach ($tablas_clave as $tabla) {
    echo "<div class='box'>";
    echo "<h3>Tabla: $tabla</h3>";
    try {
        $check = $conn->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = ?");
        $check->execute([$tabla]);
        if($check->fetchColumn() > 0) {
            // Extraemos los primeros 2 registros para no saturar la pantalla
            $stmt = $conn->query("SELECT TOP 2 * FROM " . $tabla);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($rows) > 0) {
                echo "<table><tr>";
                foreach (array_keys($rows[0]) as $col) {
                    echo "<th>$col</th>";
                }
                echo "</tr>";
                foreach ($rows as $row) {
                    echo "<tr>";
                    foreach ($row as $val) {
                        // Limitar texto largo
                        $texto = htmlspecialchars((string)$val);
                        if(strlen($texto) > 50) $texto = substr($texto, 0, 50) . "...";
                        echo "<td>$texto</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>La tabla existe pero está vacía.</p>";
            }
        } else {
            echo "<p>Esta tabla no existe en esta versión.</p>";
        }
    } catch(Exception $e) {
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
    echo "</div>";
}

echo "</body></html>";

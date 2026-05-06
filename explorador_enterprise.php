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

$table = isset($_GET['table']) ? $_GET['table'] : '';

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Explorador Enterprise</title>";
echo "<style>
    body { font-family: Arial; margin: 0; display: flex; height: 100vh; }
    #sidebar { width: 250px; background: #2c3e50; color: white; overflow-y: auto; padding: 10px; }
    #sidebar a { color: #ecf0f1; text-decoration: none; display: block; padding: 5px; border-bottom: 1px solid #34495e; }
    #sidebar a:hover { background: #34495e; }
    #content { flex-grow: 1; padding: 20px; overflow-y: auto; background: #ecf0f1; }
    table { border-collapse: collapse; width: 100%; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.2); }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #3498db; color: white; }
</style></head><body>";

echo "<div id='sidebar'><h3>Tablas (Lab)</h3>";
$stmt = $conn->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_NAME LIKE 'Lab%' ORDER BY TABLE_NAME");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<a href='?table=" . $row['TABLE_NAME'] . "'>" . $row['TABLE_NAME'] . "</a>";
}
echo "</div>";

echo "<div id='content'>";
if ($table) {
    echo "<h2>Explorando: " . htmlspecialchars($table) . " (Primeros 5 registros)</h2>";
    try {
        $check = $conn->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = ?");
        $check->execute([$table]);
        if($check->fetchColumn() > 0) {
            $stmt = $conn->query("SELECT TOP 5 * FROM " . $table);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($rows) > 0) {
                echo "<table><tr>";
                foreach (array_keys($rows[0]) as $col) {
                    echo "<th>" . htmlspecialchars($col) . "</th>";
                }
                echo "</tr>";
                foreach ($rows as $row) {
                    echo "<tr>";
                    foreach ($row as $val) {
                        echo "<td>" . htmlspecialchars((string)$val) . "</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>La tabla está vacía o no tiene registros aún.</p>";
            }
        } else {
            echo "<p>Tabla no válida.</p>";
        }
    } catch(Exception $e) {
        echo "<p>Error al leer tabla: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<h2>Selecciona una tabla a la izquierda</h2>";
    echo "<p>Busca tablas que parezcan contener pacientes (cédula, nombres, email) o correos enviados.</p>";
    echo "<p>Ejemplos comunes en este software: Lab21 (Pacientes), Lab22 (Órdenes), Lab39, Lab52.</p>";
}
echo "</div></body></html>";

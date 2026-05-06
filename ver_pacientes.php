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

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Visor de Pacientes (Prueba Segura)</title>";
echo "<style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 30px; background: #f0f2f5; }
    .box { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    h2 { color: #1a237e; margin-top: 0; }
    table { border-collapse: collapse; width: 100%; margin-top: 20px; }
    th, td { border-bottom: 1px solid #eee; padding: 12px; text-align: left; }
    th { background-color: #3949ab; color: white; font-weight: 500; }
    tr:hover { background-color: #f8f9fa; }
    .badge { background: #e8eaf6; color: #3949ab; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
    .encrypted { color: #e53935; font-family: monospace; }
</style></head><body>";

echo "<div class='box'>";
echo "<h2>📋 Últimos 5 Pacientes y sus Órdenes</h2>";
echo "<p>Consulta de solo lectura exitosa. Los datos demográficos se muestran en su estado original cifrado (Enterprise).</p>";

try {
    // Cruce seguro entre Lab22 (Ordenes) y Lab21 (Pacientes) ordenado por la orden más reciente
    $query = "SELECT TOP 5 
                O.Lab22C1 AS NumeroOrden,
                O.Lab22C3 AS FechaOrden,
                P.Lab21C1 AS IDPaciente,
                P.Lab21C2 AS NombreCifrado,
                P.Lab21C4 AS DatoCifrado1,
                P.Lab21C5 AS DatoCifrado2
              FROM Lab22 O
              INNER JOIN Lab21 P ON O.Lab21C1 = P.Lab21C1
              ORDER BY O.Lab22C1 DESC";
              
    $stmt = $conn->query($query);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if(count($resultados) > 0) {
        echo "<table>";
        echo "<tr><th>Número de Orden</th><th>Fecha</th><th>ID Paciente</th><th>Nombre (Cifrado)</th><th>Otros Datos (Cifrados)</th></tr>";
        foreach($resultados as $row) {
            echo "<tr>";
            echo "<td><strong>" . htmlspecialchars($row['NumeroOrden']) . "</strong></td>";
            echo "<td>" . htmlspecialchars($row['FechaOrden']) . "</td>";
            echo "<td><span class='badge'>#" . htmlspecialchars($row['IDPaciente']) . "</span></td>";
            echo "<td class='encrypted'>🔒 " . htmlspecialchars($row['NombreCifrado']) . "</td>";
            echo "<td class='encrypted'>🔒 " . htmlspecialchars($row['DatoCifrado1']) . " / " . htmlspecialchars($row['DatoCifrado2']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No se encontraron registros de órdenes recientes.</p>";
    }
} catch(Exception $e) {
    echo "<p style='color:red;'>Error en la consulta: " . $e->getMessage() . "</p>";
}

echo "</div></body></html>";

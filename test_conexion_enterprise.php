<?php
// =======================================================================
// SCRIPT DE PRUEBA 100% SEGURO (SOLO LECTURA)
// Garantía: Este archivo NO CONTIENE ningún comando INSERT, UPDATE o DELETE.
// Su única función es intentar conectar y "mirar" la lista de tablas.
// =======================================================================

$serverName = "srvflorlab"; // Si la base de datos está en la misma PC, puede que sea "localhost" o el nombre de la PC.
$database = "EnterpriseTest";
$uid = "sa";
$pwd = "@enterprise123456";

echo "<div style='font-family: Arial, sans-serif; padding: 20px;'>";
echo "<h2 style='color: #2c3e50;'>🔍 Prueba de Diagnóstico (Solo Lectura)</h2>";

// Diagnóstico rápido para saber qué DLL necesita
echo "<div style='background:#f1f2f6; padding:10px; margin-bottom:15px; border-radius:5px;'>";
echo "<strong>Tu versión de PHP instalada es:</strong> " . phpversion() . "<br>";
echo "<strong>¿Driver pdo_sqlsrv instalado?:</strong> " . (extension_loaded('pdo_sqlsrv') ? '<span style="color:green">SÍ</span>' : '<span style="color:red">NO</span>');
echo "</div>";

echo "<p>Intentando conectar a la base de datos <strong>$database</strong> en el servidor <strong>$serverName</strong>...</p>";

// Intentar conexión estándar PDO
try {
    // Solo abre la conexión (Añadido TrustServerCertificate para saltar el error SSL)
    $conn = new PDO("sqlsrv:server=$serverName;Database=$database;TrustServerCertificate=true", $uid, $pwd);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color: #27ae60; font-weight: bold;'>✅ ¡Conexión Exitosa con el motor de base de datos!</p>";
    
    // CONSULTA 100% INOFENSIVA: Leemos los NOMBRES de las tablas del sistema
    $query = "SELECT TOP 30 TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE' ORDER BY TABLE_NAME";
    $stmt = $conn->query($query);
    
    echo "<h3>Lista de las primeras 30 tablas encontradas:</h3>";
    echo "<ul>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<li style='padding: 3px 0;'>" . htmlspecialchars($row['TABLE_NAME']) . "</li>";
    }
    echo "</ul>";

    echo "<p style='color: #888;'>Prueba finalizada. No se alteró ninguna información.</p>";

} catch(Exception $e) {
    echo "<div style='background: #fee; padding: 15px; border: 1px solid #fcc; border-radius: 5px;'>";
    echo "<h4 style='color: #c0392b; margin-top:0;'>❌ La conexión falló</h4>";
    echo "<p><strong>Error técnico:</strong> " . $e->getMessage() . "</p>";
    
    // Asistencia si falta el driver de Microsoft en XAMPP
    if (strpos($e->getMessage(), 'could not find driver') !== false) {
        echo "<p><strong>Diagnóstico:</strong> El XAMPP recién instalado no trae los conectores (Drivers) para hablar con Microsoft SQL Server por defecto. Necesitaremos instalar la extensión 'pdo_sqlsrv' en ese XAMPP para que funcione.</p>";
    }
    echo "</div>";
}

echo "</div>";
?>

<?php
$serverName = "srvflorlab";
$database = "EnterpriseTest";
$uid = "sa";
$pwd = "@enterprise123456";

try {
    $conn = new PDO("sqlsrv:server=$serverName;Database=$database;TrustServerCertificate=true", $uid, $pwd);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $conn->query("SELECT ROUTINE_NAME, ROUTINE_TYPE FROM INFORMATION_SCHEMA.ROUTINES WHERE ROUTINE_NAME LIKE '%crypt%' OR ROUTINE_NAME LIKE '%des%' OR ROUTINE_NAME LIKE '%enc%'");
    $funciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($funciones);
} catch(Exception $e) {
    die("Error de conexión: " . $e->getMessage());
}

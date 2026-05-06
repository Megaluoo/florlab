<?php
// init_db.php - Run this once
$host = '127.0.0.1';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create DB
    $pdo->exec("CREATE DATABASE IF NOT EXISTS florlab_db DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;");
    $pdo->exec("USE florlab_db;");

    // Table: tests_catalog
    $pdo->exec("CREATE TABLE IF NOT EXISTS tests_catalog (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        category VARCHAR(100) NOT NULL,
        price DECIMAL(10,2) DEFAULT 0.00,
        requirements VARCHAR(255),
        steps TEXT
    )");
    
    // Si la tabla ya existe y no tiene estas columnas, las añadimos
    try {
        $pdo->exec("ALTER TABLE tests_catalog ADD COLUMN requirements VARCHAR(255) AFTER price");
    } catch (PDOException $e) {}
    try {
        $pdo->exec("ALTER TABLE tests_catalog ADD COLUMN steps TEXT AFTER requirements");
    } catch (PDOException $e) {}

    // Table: patients
    $pdo->exec("CREATE TABLE IF NOT EXISTS patients (
        id INT AUTO_INCREMENT PRIMARY KEY,
        dni VARCHAR(50) UNIQUE NOT NULL,
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Table: exam_results
    $pdo->exec("CREATE TABLE IF NOT EXISTS exam_results (
        id INT AUTO_INCREMENT PRIMARY KEY,
        patient_id INT NOT NULL,
        test_id INT NOT NULL,
        status ENUM('pending', 'completed') DEFAULT 'pending',
        result_value TEXT,
        date_issued TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (patient_id) REFERENCES patients(id),
        FOREIGN KEY (test_id) REFERENCES tests_catalog(id)
    )");

    // Table: admins
    $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL
    )");

    // Insert Default Data
    $admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->exec("INSERT IGNORE INTO admins (username, password_hash) VALUES ('admin', '$admin_pass')");

    // Vaciar tabla e insertar datos frescos para asegurar que tenemos todos los campos correctos
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $pdo->exec("TRUNCATE TABLE tests_catalog");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

    $tests = [
        ['Tipificación de VPH por PCR', 'molecular', 'Muestra: Citológica Especial', "Abstinencia sexual 48 horas previas.\nNo usar duchas vaginales ni óvulos.\nEstar fuera del periodo menstrual."],
        ['Panel Respiratorio Multiplex', 'molecular', 'Muestra: Hisopado Nasofaríngeo', "No requiere ayuno.\nNo aplicar gotas nasales 4 horas antes.\nEvitar enjuagues bucales medicados."],
        ['Detección de ETS por PCR', 'molecular', 'Muestra: Orina / Hisopado', "Retención de orina de al menos 2 horas.\nNo haber tomado antibióticos recientes.\nNo requiere ayuno."],
        ['PCR Dengue/Zika/Chik', 'molecular', 'Muestra: Sangre entera', "Ideal tomar en los primeros 5 días de fiebre.\nNo requiere ayuno."],
        
        ['Hematología 5ta Gen.', 'hematologia', 'Muestra: Sangre (Tubo Lila)', "Ayuno preferible de 4 a 6 horas.\nEvitar ejercicio intenso previo."],
        ['Perfil de Coagulación', 'hematologia', 'Muestra: Plasma (Tubo Azul)', "Informar si toma anticoagulantes.\nAyuno de 8 horas."],
        ['Grupo Sanguíneo', 'hematologia', 'Muestra: Sangre entera', "No requiere preparación previa.\nNo requiere ayuno."],
        ['Screening Neonatal', 'hematologia', 'Muestra: Sangre de Talón', "Exclusivo para bebés de 2 a 7 días de nacidos.\nDebe haber consumido leche materna o fórmula."],
        
        ['Perfil Lipídico', 'quimica', 'Muestra: Suero', "Ayuno estricto de 12 a 14 horas.\nCena ligera libre de grasas la noche anterior.\nCero alcohol 24 horas antes."],
        ['Glicemia Post-Prandial', 'quimica', 'Muestra: Suero seriado', "Ayuno de 8 horas para toma basal.\nDisponer de 2 horas en el laboratorio.\nTraer desayuno o carga de glucosa."],
        ['Perfil Hepático', 'quimica', 'Muestra: Suero', "Ayuno de 8 a 10 horas.\nEvitar consumo de alcohol 48h previas."],
        
        ['Perfil Tiroideo', 'hormonas', 'Muestra: Suero', "Ayuno de 8 horas.\nNo tomar la pastilla de tiroides antes del examen."],
        ['Prolactina y Testosterona', 'hormonas', 'Muestra: Suero matutino', "Toma estricta entre 7:00 AM y 9:00 AM.\nAbstinencia sexual 48 horas previas.\nNo estimular glándulas mamarias (Prolactina)."],
        
        ['Prueba de VIH (4ta Gen)', 'inmunologia', 'Muestra: Suero', "No requiere ayuno estricto, 4 horas son suficientes."],
        ['Marcadores de Hepatitis (A, B, C)', 'inmunologia', 'Muestra: Suero', "Ayuno preferible de 8 horas."],
        ['VDRL / RPR', 'inmunologia', 'Muestra: Suero', "No requiere ayuno estricto."],
        
        ['Uroanálisis', 'rutina', 'Muestra: Orina', "Primera orina de la mañana.\nDesechar el primer chorro.\nRecolectar el chorro medio en envase estéril."],
        ['Coproanálisis / Sangre Oculta', 'rutina', 'Muestra: Heces frescas', "No mezclar con orina.\nEntregar muestra antes de 2 horas de recolectada."]
    ];

    $stmt = $pdo->prepare("INSERT INTO tests_catalog (name, category, requirements, steps) VALUES (?, ?, ?, ?)");
    foreach ($tests as $t) {
        $stmt->execute([$t[0], $t[1], $t[2], $t[3]]);
    }

    echo "Base de datos inicializada exitosamente.";

} catch (PDOException $e) {
    die("DB Setup Error: " . $e->getMessage());
}
?>

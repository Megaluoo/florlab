<?php
// login.php
session_start(); // Iniciar la sesión

// --- Configuración de Usuario Admin ---
// ¡CAMBIAR ESTA CONTRASEÑA EN PRODUCCIÓN!
$ADMIN_USER = 'florlab_admin';
$ADMIN_PASS = 'Florlab2025*'; 

// --- Verificación ---
if (isset($_POST['username']) && isset($_POST['password'])) {
    
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Verificar usuario y contraseña
    if ($user === $ADMIN_USER && $pass === $ADMIN_PASS) {
        
        // Autenticación exitosa
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $ADMIN_USER;
        
        // Redirigir al panel de admin
        header('Location: admin.php');
        exit;
        
    } else {
        // Autenticación fallida
        header('Location: login.html?error=1');
        exit;
    }
} else {
    // Si alguien trata de acceder al script directamente
    header('Location: login.html');
    exit;
}
?>
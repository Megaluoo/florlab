<?php
// api_bcv.php
header('Content-Type: application/json');

$cacheFile = 'bcv_cache.json';
// Tiempo de caché en segundos (4 horas = 14400)
$cacheTime = 14400; 

// Comprobar si el archivo de caché existe y es reciente
if (file_exists($cacheFile) && (time() - file_get_contents($cacheFile, false, null, 0, 10)) < $cacheTime) {
    // Leer el contenido de la caché (ignorando los primeros 10 bytes del timestamp)
    echo substr(file_get_contents($cacheFile), 10);
    exit;
}

// Si la caché no es válida, buscar la data nueva
// Usamos una API pública y confiable para el BCV
$apiUrl = 'https://pydolarvenezuela.com/api/v1/dollar/page?page=bcv';

$options = [
    'http' => [
        'header' => "User-Agent: Florlab-Website-Bot/1.0\r\n",
        'timeout' => 5, // Timeout de 5 segundos
    ],
    // Ignorar errores de certificado SSL si es necesario (no ideal, pero común en hostings)
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ],
];
$context = stream_context_create($options);

$response = @file_get_contents($apiUrl, false, $context);

if ($response === FALSE) {
    // Si la API falla, intentar servir la caché vieja si existe
    if (file_exists($cacheFile)) {
        echo substr(file_get_contents($cacheFile), 10);
        exit;
    }
    // Si no hay nada, error
    http_response_code(500);
    echo json_encode(['error' => 'No se pudo conectar a la API del BCV.']);
    exit;
}

// Decodificar la respuesta
$data = json_decode($response, true);

if (isset($data['monitors']['bcv']['price'])) {
    // Formatear la respuesta deseada para nuestro frontend
    $bcvRate = $data['monitors']['bcv']['price'];
    $output = json_encode([
        'bcv' => [
            'rate' => $bcvRate
        ]
    ]);

    // Guardar en caché: timestamp (10 bytes) + data
    file_put_contents($cacheFile, time() . $output);

    // Enviar respuesta al frontend
    echo $output;

} else {
    // Si la API cambia su formato o falla
    http_response_code(500);
    echo json_encode(['error' => 'Formato de API BCV inesperado.']);
}
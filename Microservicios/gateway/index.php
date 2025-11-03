<?php
header('Content-Type: application/json');

// 1️⃣ URL del servidor Eureka simulado
$eurekaUrl = 'http://localhost:8761';

// 2️⃣ Obtener lista de servicios registrados
$registryJson = @file_get_contents($eurekaUrl);
if (!$registryJson) {
    echo json_encode(['error' => 'No se pudo conectar con Eureka']);
    exit;
}

$registry = json_decode($registryJson, true)['registry'];

// 3️⃣ Obtener parámetros del cliente
$service = $_GET['service'] ?? null;
$path = $_GET['path'] ?? null;

if (!$service || !$path) {
    echo json_encode(['error' => 'Parámetros inválidos. Usa ?service=productos&path=Productos/listar']);
    exit;
}

// 4️⃣ Verificar que el servicio esté registrado
if (!isset($registry["{$service}-service"])) {
    echo json_encode(['error' => "Servicio '{$service}' no registrado en Eureka"]);
    exit;
}

// 5️⃣ Construir URL destino
$targetUrl = $registry["{$service}-service"]['url'] . '?url=' . $path;

// 6️⃣ Redirigir solicitud
$response = @file_get_contents($targetUrl);
if ($response === false) {
    echo json_encode(['error' => "No se pudo contactar con el servicio {$service}"]);
    exit;
}

echo $response;

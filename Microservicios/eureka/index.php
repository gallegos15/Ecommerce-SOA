<?php
header('Content-Type: application/json');

$services = [
    'usuarios-service' => [
        'name' => 'usuarios-service',
        'url' => 'http://localhost:8003/',
        'status' => 'UP'
    ],
    'productos-service' => [
        'name' => 'productos-service',
        'url' => 'http://localhost:8002/',
        'status' => 'UP'
    ],

];

echo json_encode([
    'registry' => $services,
    'timestamp' => date('Y-m-d H:i:s')
]);

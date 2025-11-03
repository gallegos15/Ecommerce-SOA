<?php
require_once 'Config/Config.php';
require_once 'Config/App/Autoload.php';

header('Content-Type: application/json'); // 🔹 Respuestas en JSON

$controller = 'Usuarios';
$method = 'listar';
$params = [];

if (isset($_GET['url'])) {
    $url = explode('/', trim($_GET['url'], '/'));
    if (!empty($url[0])) $controller = ucfirst($url[0]);
    if (isset($url[1]) && $url[1] !== '') $method = $url[1];
    $params = array_slice($url, 2);
}

$file = "Controllers/{$controller}.php";
if (!file_exists($file)) {
    http_response_code(404);
    echo json_encode(['error' => 'Controlador no encontrado']);
    exit;
}

require_once $file;
$ctrl = new $controller();

if (!method_exists($ctrl, $method)) {
    http_response_code(404);
    echo json_encode(['error' => 'Método no encontrado']);
    exit;
}

call_user_func_array([$ctrl, $method], $params);

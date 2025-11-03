<?php
require_once 'Config/Config.php';
require_once 'Config/App/Autoload.php';

header('Content-Type: application/json');

$controller = 'Productos';
$method = 'index';
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
    echo json_encode(['error' => true, 'message' => 'Controlador no encontrado']);
    exit;
}

require_once $file;
$ctrl = new $controller();

if (!method_exists($ctrl, $method)) {
    // Si no existe el método solicitado, usar index() como fallback
    if (method_exists($ctrl, 'index')) {
        $method = 'index';
    } else {
        http_response_code(404);
        echo json_encode(['error' => true, 'message' => 'Método no encontrado']);
        exit;
    }
}

call_user_func_array([$ctrl, $method], $params);

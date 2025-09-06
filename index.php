<?php
require_once 'Config/Config.php';
require_once 'Config/App/Autoload.php';

$controller = 'Home';
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
    $controller = 'Errors';
    $file = "Controllers/Errors.php";
    $method = 'index';
    $params = [];
}

require_once $file;
$ctrl = new $controller();

if (!method_exists($ctrl, $method)) {
    require_once "Controllers/Errors.php";
    $ctrl = new Errors();
    $method = 'index';
    $params = [];
}

call_user_func_array([$ctrl, $method], $params);

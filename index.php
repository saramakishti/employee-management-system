<?php
session_start();

require_once 'vendor/autoload.php';

use Controllers\AuthController;
use Controllers\EmployeeController;

$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = parse_url($_SERVER['REQUEST_URI']);
$requestPath = $requestUri['path']; 

$pathChunks = explode("/", $requestPath); 
array_shift($pathChunks); 

$controllerName = array_shift($pathChunks);
$controller = null;

switch (strtoupper($controllerName)) {
    case 'EMPLOYEE':
        if (!isset($_SESSION[AuthController::USER_SESSION])) {
            header('Location: /auth/login');
        }
        $controller = new EmployeeController(array_merge($_POST, $_GET, $_FILES, $_SERVER, $_SESSION));
        break;
    case 'AUTH':
        $controller = new AuthController(array_merge($_POST, $_GET, $_FILES, $_SERVER, $_SESSION));
        break;
}

if ($controller) {
    if (count($pathChunks) === 2) {
        $id = (int) $pathChunks[0];
        $method = $pathChunks[1];
        call_user_func_array([$controller, $method], [$id]);
    } elseif (count($pathChunks) === 1) {
        $chunk = $pathChunks[0];

        if (is_numeric($chunk)) {
            $id = (int) $chunk;
            $controller->getById($id);
        } else {
            $method = $chunk;
            call_user_func_array([$controller, $method], []);

            if ($requestMethod === 'POST' && $method === 'logout') {
                session_destroy();
            }
        }
    } else {
        $controller->index();
    }

    $_GET = [];
    $_POST = [];
}

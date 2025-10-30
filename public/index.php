<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/../controllers/TodoController.php');

$controller = new TodoController();
$page = $_GET['page'] ?? 'index';

switch ($page) {
    case 'index': $controller->index(); break;
    case 'create': $controller->create(); break;
    case 'delete': $controller->delete(); break;
    case 'toggle': $controller->toggle(); break;
    case 'update': $controller->update(); break;
    case 'detail': $controller->detail(); break;
    case 'reorder': $controller->reorder(); break;


    default: echo "404 Not Found"; break;
}

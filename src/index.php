<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require_once './controllers/Controller.php';
require_once  './controllers/LoginController.php';
require_once './controllers/StaticFileController.php';


$app = new \Slim\App;
$app->get('/assets[/{type}[/{filename}]]', function (Request $request, Response $response, array $args) {
  $controller = new StaticFileController($request, $response, $args);

  return $controller->serve();
});
$app->get('/', function (Request $request, Response $response, array $args) {
  $controller = new LoginController($request, $response, $args);

  return $controller->index();
});
$app->post('/login', function (Request $request, Response $response, array $args) {
  $controller = new LoginController($request, $response, $args);

  return $controller->login();
});

$app->run();
?>
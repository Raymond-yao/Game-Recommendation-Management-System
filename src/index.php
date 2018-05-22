<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require_once './controllers/Controller.php';
require_once  './controllers/LoginController.php';
require_once './controllers/StaticFileController.php';
require_once './controllers/AccountController.php';


$app = new \Slim\App(array(
  'debug' => true
));

// static files routing
$app->get('/assets[/{type}[/{filename}]]', function (Request $request, Response $response, array $args) {
  $controller = new StaticFileController($request, $response, $args);

  return $controller->serve();
});

// normal routing
$app->get('/', function (Request $request, Response $response, array $args) {
  $controller = new LoginController($request, $response, $args);

  return $controller->index();
});
$app->post('/login', function (Request $request, Response $response, array $args) {
  $controller = new LoginController($request, $response, $args);

  return $controller->login();
});
$app->get('/overview', function (Request $request, Response $response, array $args) {
  $controller = new AccountController($request, $response, $args);

  return $controller->overview();
});

$app->run();
?>
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

// index and login routing
$app->get('/', function (Request $request, Response $response, array $args) {
  $controller = new LoginController($request, $response, $args);

  return $controller->index();
});
$app->post('/login', function (Request $request, Response $response, array $args) {
  $controller = new LoginController($request, $response, $args);

  return $controller->login();
});

// two helper to keep the cookie alive. If the cookie times out, a user has to login again
function verify_login() {
  if (isset($_COOKIE["account"])) {
    return $_COOKIE["account"];
  } else {
    return FALSE;
  }
};

function login_helper($controller, $action) {
  return function(Request $request, Response $response, array $args) use ($controller, $action) {
    $cookie = verify_login();
    if ($cookie){
      setcookie("account", $cookie, time()+1800);
      $controller_intance = new $controller($request, $response, $args);
      return $controller_intance->$action();
    } else {
      return $response->withRedirect('/', 301);
    }
  };
};

$app->get('/overview', login_helper("AccountController", "overview"));


$app->run();
?>
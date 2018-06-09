<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Monolog\Logger;
use \Monolog\Formatter\LineFormatter;

require '../vendor/autoload.php';
require_once './models/Model.php';
require_once './models/User.php';
require_once './models/RecommendationList.php';
require_once './controllers/Controller.php';
require_once  './controllers/LoginController.php';
require_once './controllers/StaticFileController.php';
require_once './controllers/AccountController.php';
require_once './controllers/ListController.php';

require_once '../sql/LoggedPDO.php';

$config['log'] = TRUE;
$config['db']['host']   = 'localhost';
$config['db']['user']   = 'root';
$config['db']['pass']   = '';
$config['db']['dbname'] = 'test';

$app = new \Slim\App(array(
  'debug' => true,
  'settings' => $config
));
$container = $app->getContainer();
$container['logger'] = function($c) {
  $path = "php://stdout";
  if ($c['settings']["log"]) {
    $path = __DIR__ . "/../sql.log";
  }
  $logger = new \Monolog\Logger('my_logger');
  $file_handler = new \Monolog\Handler\StreamHandler($path, Logger::WARNING);
  $formatter = new \Monolog\Formatter\LineFormatter(null, null, false, true);
  $file_handler->setFormatter($formatter);
  $logger->pushHandler($file_handler);
  return $logger;
};
$container['db'] = function ($c) {

  $pdo_obj = NULL;

  if ( !$c['settings']["log"] ) {
    $db = $c['settings']['db'];
    $pdo = new PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'],
      $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES , FALSE);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo_obj = $pdo;
  } else {
    $pdo_obj = new LoggedPDO($c['settings']['db']);
  }

  return $pdo_obj;
};

// static files routing
$app->get('/setup_assets', function (Request $request, Response $response, array $args) {
  return (new Controller($request, $response, $args))->render("html","assets.html");
});
$app->get('/assets[/{type}[/{filename}]]', function (Request $request, Response $response, array $args) {
  $controller = new StaticFileController($request, $response, $args);

  return $controller->serve();
});

// index, login, logout and register routing
$app->get('/', function (Request $request, Response $response, array $args) {
  $controller = new LoginController($request, $response, $args);

  return $controller->explor();
});
$app->post('/login', function (Request $request, Response $response, array $args) {
  $controller = new LoginController($request, $response, $args);

  return $controller->login();
});
$app->post('/register', function (Request $request, Response $response, array $args) {
  $controller = new LoginController($request, $response, $args);

  return $controller->registerinfo();
});
$app->get('/logout', function (Request $request, Response $response, array $args) {
  $controller = new LoginController($request, $response, $args);
  // decide if the user has the cookie, if yes, then the user can proceed to logout
  // otherwise, return to main page
  if (isset($_COOKIE["account"])) {
    return $controller->logout();
  } else {
    return $controller->explor();
  }
});
$app->get('/register', function (Request $request, Response $response, array $args) {
  $controller = new LoginController($request, $response, $args);
  return $controller->register();
});

// two helper to keep the cookie alive. If the cookie times out, a user has to login again
function verify_login() {
  if (isset($_COOKIE["account"])) {
    return $_COOKIE["account"];
  } else {
    return FALSE;
  }
};

function with_or_without_cookie($with_cookie_handler, $without_out_cookie_handler) {

  return function(Request $request, Response $response, array $args) use ($with_cookie_handler, $without_out_cookie_handler) {
    $cookie = verify_login();
    if ($cookie) {
      return $with_cookie_handler($request, $response, $args);
    } else {
      return $without_out_cookie_handler($request, $response, $args);
    }
  };
};

function user_or_login_expired($controller, $action) {
  $with_cookie_handler = function(Request $request, Response $response, array $args) use ($controller, $action) {
    $cookie = $_COOKIE["account"];
    setcookie("account", $cookie, time()+1800, '/', 'localhost');
    $controller_intance = new $controller($request, $response, $args);
    return $controller_intance->$action();
  };
  $without_out_cookie_handler = function(Request $request, Response $response, array $args) {
    return (new Controller($request, $response, $args))->render("html","session_time_out.html");
  };

  return with_or_without_cookie($with_cookie_handler, $without_out_cookie_handler);
};

function user_or_visitor(array $actions) {
  $user_controller = $actions["user_controller"];
  $visitor_controller = $actions["visitor_controller"];
  $user_action = $actions["user_action"];
  $visitor_action = $actions["visitor_action"];

  $with_cookie_handler = function(Request $request, Response $response, array $args)
  use ($user_controller, $user_action) {
    $controller = new $user_controller($request, $response, $args);
    return $controller->$user_action();
  };

  $without_out_cookie_handler = function(Request $request, Response $response, array $args)
  use ($visitor_controller,  $visitor_action) {
    $controller = new $visitor_controller($request, $response, $args);
    return $controller->$visitor_action();
  };

  return with_or_without_cookie($with_cookie_handler, $without_out_cookie_handler);
}
$app->get('/header', function(Request $request, Response $response, array $args) {
  $controller = new AccountController($request, $response, $args);

  return $controller->get_header();
});
$app->get('/overview[/{id}]', user_or_login_expired("AccountController", "overview"));

$app->get('/index', function(Request $request, Response $response, array $args) {
  $controller = new LoginController($request, $response, $args);
  return $controller->index();
});
$app->get('/settings', user_or_login_expired("AccountController", "settings"));
$app->post('/settings', function (Request $request, Response $response, array $args) {
  $controller = new AccountController($request, $response, $args);
  
  return $controller->update();
});
$app->get('/accountinfo[/{id}]', function(Request $request, Response $response, array $args) {
  $controller = new AccountController($request, $response, $args);

  return $controller->account_info();
});
$app->get('/listinfo[/{id}]', function(Request $request, Response $response, array $args) {
  $controller = new AccountController($request, $response, $args);

  return $controller->list_info();
});
$app->post('/searchUser', function(Request $request, Response $response, array $args) {
  $controller = new AccountController($request, $response, $args);

  return $controller->searchUser();
});
$app->get('/friendinfo[/{id}]', function(Request $request, Response $response, array $args) {
  $controller = new AccountController($request, $response, $args);

  return $controller->friends_info();
});
$app->post('/manage_friend', function(Request $request, Response $response, array $args) {
  $controller = new AccountController($request, $response, $args);

  return $controller->manage_friend();
});
$app->get('/list[/{id}]', user_or_login_expired("ListController", "getList")); 
$app->run();
?>
<?php 

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class LoginController extends Controller {

  function __construct(Request $request, Response $response, $args) {
    parent::__construct($request, $response, $args);
  }

  function login() {
    $params = $this->request->getParsedBody(); 
    $account = $params["account"];
    $password = $params["password"];

    if ($account === "raymond" && $password === "123") {
      setcookie("account", "raymond", time()+3600);
    }
  }

  function index() {
    $this->render("html", "index.html");
  }
}
?>
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

    if (($account === "raymond" || $account === "dante" || $account === "robin" || $account === "ann") && $password === "123") {
      setcookie("account", "raymond", time() + 1800);
      return $this->render("json", array('status' => "success"));
    } else {
      return $this->render("json", array('status' => "failed"));
    }
  }

  function logout() {
    if (isset($_COOKIE["account"])) {
      setcookie("account", '', time() - 3600);
      unset($_COOKIE["account"]);
    }
    return $this->response->withRedirect("/");
  }

  function index() {
    $this->render("html", "index.html");
  }
}
?>
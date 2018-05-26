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

    $pdo = $GLOBALS["container"]->db;
    $stmt = $pdo->prepare("SELECT username FROM users WHERE (email = :email AND password = :password)");
    $stmt->execute(array(':email' => $account, ':password' => $password));
    $username = $stmt->fetch(PDO::FETCH_OBJ)->username;
    if ($username) {
      setcookie("account", $username, time() + 1800);
      return $this->render("json", array('status' => "success"));
    } else {
      return $this->render("json", array('status' => "failed"));
    }
  }

  function registerinfo() {
    $params = $this->request->getParsedBody(); 
    $registeraccount = $params["registeraccount"];
    $registerpassword = $params["registerpassword"];
    $repeatpassword = $params["repeatpassword"];

    if (($registeraccount !== "") && ($registerpassword === $repeatpassword)){
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
    return $this->render("html", "logout.html");
  }

  function index() {
    $this->render("html", "index.html");
  }

  function register() {
    $this->render("html", "register.html");
  }
}
?>
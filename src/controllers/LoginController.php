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
    $id = User::verify($account, $password);
    if ($id) {
      setcookie("account", $id, time() + 1800, '/', 'localhost');
      return $this->render("json", array('status' => "success"));
    } else {
      return $this->render("json", array('status' => "failed"));
    }
  }

  function registerinfo() {
    $params = $this->request->getParsedBody(); 
    $registerEmail = $params["registerEmail"];
    $registerUsername = $params["registerUsername"];
    $registerPassword = $params["registerPassword"];
    $repeatPassword = $params["repeatPassword"];

    $pdo = $GLOBALS["container"]->db;
    // check if this email already exist
    $stmt = $pdo->prepare("SELECT email FROM users WHERE email = :email");
    $stmt->execute(array(':email' => $registerEmail));
    $email = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!empty($email)){
      return $this->render("json", array('status' => "failed"));
    }
    $stmt->closeCursor();
    
    // insert this email to the user table
    if (($registerEmail !== "") && ($registerPassword === $repeatPassword)){
      // find max id in user table
      $stmt = $pdo->prepare("SELECT MAX(id) FROM users");
      $stmt->execute();
      $maxId = $stmt->fetch(PDO::FETCH_ASSOC)["MAX(id)"];
      $maxId += 1;

      User::create(["id" => $maxId, 
        "username" => $registerUsername, 
        "password" => $registerPassword,
        "email" => $registerEmail]);
      return $this->render("json", array('status' => "success register"));
    } else {
      return $this->render("json", array('status' => "failed"));
    }
  }

  function logout() {
    if (isset($_COOKIE["account"])) {
      setcookie("account", '', time() - 3600, '/', 'localhost');
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
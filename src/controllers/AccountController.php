<?php 

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class AccountController extends Controller {

  function __construct(Request $request, Response $response, $args) {
    parent::__construct($request, $response, $args);
  }

  function overview() {
    $replacement = "var visit_id = " . (isset($this->args["id"]) ? $this->args["id"] : $_COOKIE["account"]) . ";";
    if (isset($this->args["id"]) && $this->args["id"] !== $_COOKIE["account"]) {
      $user = User::get($_COOKIE["account"]);
      $replacement = $replacement . " var is_friend = " . ($user->isFriend($this->args["id"]) ? "true" : "false");
    }
    return $this->render("html", "overview.html", $replacement);
  }

  function get_header() {
    $url = $this->request->getUri();
    if (isset($_COOKIE["account"])) {
      return $this->render("html", "user_header.html");
    } else {
      return $this->render("html", "visitor_header.html");
    }
  }
  
  function updateUsername() {
    $params = $this->request->getParsedBody();
    $Username = $params["Username"];
    $id = isset($this->args["id"]) ? $this->args["id"] : $_COOKIE["account"];

    $pdo = $GLOBALS["container"]->db;
    // check if this username already exist
    $stmt = $pdo->prepare("UPDATE users SET username = :username WHERE id = :id");
    $stmt->execute(array(':id' => $id, ':username' => $Username));
    $stmt->fetch(PDO::FETCH_OBJ);
    $stmt->closeCursor();
    return $this->render("json", array('status' => "success username"));
  }

  function updatePassword() {
    $params = $this->request->getParsedBody();
    $Password = $params["Password"];
    $repeatPassword = $params["repeatPassword"];
    $id = isset($this->args["id"]) ? $this->args["id"] : $_COOKIE["account"];

    if ($Password === $repeatPassword) {
      $pdo = $GLOBALS["container"]->db;
      // check if this username already exist
      $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
      $stmt->execute(array(':id' => $id, ':password' => $Password));
      $stmt->fetch(PDO::FETCH_OBJ);
      $stmt->closeCursor();
      if (isset($_COOKIE["account"])) {
        setcookie("account", '', time() - 3600, '/', 'localhost');
        unset($_COOKIE["account"]);
      }
      return $this->render("json", array('status' => "success password"));
    } else {
      return $this->render("json", array('status' => "failed"));
    }
  }

  function account_info() {
    if (!isset($_COOKIE["account"])) {
      return $this->render("json",array('status' => 'unauthorized' ));
    }

    $id = isset($this->args["id"]) ? $this->args["id"] : $_COOKIE["account"];
    $user = User::get((int) $id);
    if ($user){
      $profile_info = array(
        "cover" => $user->cover(),
        "avatar" => $user->avatar(),
        "username" => $user->username(),
        "list_count" => $user->listCount(),
        "friend_count" => $user->friendCount()
      );
      return $this->render("json",$profile_info);
    } else {
      return $this->render("json",["status" => "failed"]);
    }
  }

  function friends_info() {
    if (!isset($_COOKIE["account"])) {
      return $this->render("json",array('status' => 'unauthorized' ));
    }

    $id = isset($this->args["id"]) ? $this->args["id"] : $_COOKIE["account"];
    $friends = User::getFriends($id);
    $friends_of_logged_in_users = [];
    $check_another_user = isset($this->args["id"]) && $id !== $_COOKIE["account"];
    if ($check_another_user) {
      $friends_of_logged_in_users = User::getFriends($_COOKIE["account"]);
    }
    $friend_list = [];
    foreach ($friends as $f) {
      array_push($friend_list, [
        "username" => $f->username(),
        "avatar" => $f->avatar(),
        "cover" => $f->cover(),
        "id" => $f->id(),
        "email" => $f->email(),
        "following" => $check_another_user ? in_array($f, $friends_of_logged_in_users) : TRUE
      ]);
    }

    return $this->render("json", ["friends" => $friend_list]);
  }

  function manage_friend() {
    if (!isset($_COOKIE["account"])) {
      return $this->render("json",array('status' => 'unauthorized' ));
    }

    $params = $this->request->getParsedBody(); 
    if ($params["followee"] === $_COOKIE["account"]) {
      return $this->render("json", ["status" => "failed"]);
    }
    $user = User::get($_COOKIE["account"]);
    $followee = $params["followee"];
    if ($params["action"] === "follow") {
      if (!$user->isFriend($followee)) {
        $user->addFriend($followee);
        $user->friendCount($user->friendCount() + 1);
        $user->save();
        return $this->render("json", ["status" => "success"]);
      } else {
        return $this->render("json", ["status" => "failed"]);
      }
    } else {
      if ($user->isFriend($followee)) {
        $user->deleteFriend($followee);
        $user->friendCount($user->friendCount() - 1);
        $user->save();
        return $this->render("json", ["status" => "success"]);
      } else {
        return $this->render("json", ["status" => "failed"]);
      }
    }
  }

  function list_info() {
    $path = "/../../stubs/list_info.json";
    $complete_path = __DIR__  . $path;
    $json = file_get_contents($complete_path);

    return $this->render("json",$json);
  }
}
?>
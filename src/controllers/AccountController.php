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
    if (isset($_COOKIE["account"]) && $_COOKIE["account"] !== "visitor") {
      return $this->render("html", "user_header.html");
    } else {
      return $this->render("html", "visitor_header.html");
    }
  }

  function settings() {
    if ($this->request->isXhr()) {
      $id = $_COOKIE["account"];
      $user = User::get($id);
      return $this->render("json", [
        "avatar" => $user->avatar(), 
        "cover" => $user->cover(),
        "username" => $user->username(),
        "email" =>$user->email()
      ]);
    } else {
      return $this->render("html", "settings.html");
    }
  }

  function update() {
    if (!isset($_COOKIE["account"])) {
      return $this->render("json",array('status' => 'unauthorized' ));
    }

    $params = $this->request->getParsedBody();
    $updateType = $params["updateType"];
    $id = $_COOKIE["account"];
    $user = User::get($id);

    switch ($updateType) {
      case 'account':
      if (isset($params["email"])){
        $email = $params["email"];
        $user->email($email);
      }
      if (isset($params["username"])){
        $username = $params["username"];
        $user->username($username);
      }
      $msg = $user->save();
      return $this->render("json", $msg);
      break;

      case 'password':
      $Password = $params["Password"];
      $repeatPassword = $params["repeatPassword"];
      if ($Password === $repeatPassword) {
        $user->password($Password);
        $msg = $user->save();
        if (isset($_COOKIE["account"]) && $msg["status"] === "success") {
          setcookie("account", '', time() - 3600, '/', 'localhost');
          unset($_COOKIE["account"]);
        }
        return $this->render("json", $msg);
      } else {
        return $this->render("json", array('status' => "failed"));
      }
      break;

      case 'image':
      $files = $this->request->getUploadedFiles();
      if (isset($files["avatar"])) {
        $avatar = $files["avatar"];
        $ext = "." . explode("/", $avatar->getClientMediaType())[1];
        $filename = uniqid();
        $avatar->moveTo(__DIR__ . "/../../public/images/" . $filename . $ext);
        $user->avatar($filename . $ext);
      }

      if (isset($files["cover"])) {
        $cover = $files["cover"];
        $ext = "." . explode("/", $cover->getClientMediaType())[1];
        $filename = uniqid();
        $cover->moveTo(__DIR__ . "/../../public/images/" . $filename . $ext);
        $user->cover($filename . $ext);
      }

      $user->save();
      break;
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
    if (!isset($_COOKIE["account"])) {
      return $this->render("json",array('status' => 'unauthorized' ));
    }

    $id = isset($this->args["id"]) ? $this->args["id"] : $_COOKIE["account"];

    $list_info = User::getRecommendationLists($id);
    return $this->render("json",$list_info);
  }

  function stat() {
    if (!isset($_COOKIE["account"])) {
      return $this->render("json",array('status' => 'unauthorized' ));
    }
    $user = User::get($_COOKIE["account"]);
    $params = $this->request->getQueryParams();
    $args = ["type" => $params["type"]];
    if ($params["type"] === "average") {
      $args["extreme"] = $params["extreme"];
    }
    $stat = $user->getStat($args);
    return $this->render("json", $stat);
  }

  function searchUser() {
    $params = $this->request->getParsedBody();
    $search = $params["search"];
    $type = $params["type"];
    if (trim($search) !== "" || $type === '3') {
      $res = User::search($search, $type, $_COOKIE["account"]);
      $result = $res["result"];
      $count = $res["count"];
      $users = [];
      foreach ($result as $user) {
        array_push($users, [
          "username" => $user->username(),
          "avatar" => $user->avatar(),
          "cover" => $user->cover(),
          "id" => $user->id(),
          "email" => $user->email(),
        ]);
      }
      return $this->render("json", [
        "status" => "success",
        "count" => $count,
        "result" => $users
      ]);
    } else {
      return $this->render("json", ["status" => "fail"]);
    }
  }
}
?>
<?php 

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class AccountController extends Controller {

  function __construct(Request $request, Response $response, $args) {
    parent::__construct($request, $response, $args);
  }

  function overview() {
    $replacement = "var visit_id = " . (isset($this->args["id"]) ? $this->args["id"] : $_COOKIE["account"]);
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

  function list_info() {
    $path = "/../../stubs/list_info.json";
    $complete_path = __DIR__  . $path;
    $json = file_get_contents($complete_path);

    return $this->render("json",$json);
  }
}
?>
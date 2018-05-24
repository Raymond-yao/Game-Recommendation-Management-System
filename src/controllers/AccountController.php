<?php 

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class AccountController extends Controller {

  function __construct(Request $request, Response $response, $args) {
    parent::__construct($request, $response, $args);
  }

  function overview() {
    return $this->render("html", "overview.html");
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
    $path = "/../../stubs/account_info.json";
    $complete_path = __DIR__  . $path;
    $json = file_get_contents($complete_path);

    return $this->render("json",$json);
  }

  function list_info() {
    $path = "/../../stubs/list_info.json";
    $complete_path = __DIR__  . $path;
    $json = file_get_contents($complete_path);

    return $this->render("json",$json);
  }
}
?>
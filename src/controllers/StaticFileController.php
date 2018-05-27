<?php 

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class StaticFileController extends Controller {

  function __construct(Request $request, Response $response, $args) {
    parent::__construct($request, $response, $args);
  }

  public function static_render(String $type, $data) {
    switch ($type) {
      case "css":
      $path = "/../../public/css/";
      $complete_path = __DIR__  . $path . $data . ".css";
      $file_content = file_get_contents($complete_path);
      $newResponse = $this->response->withHeader('Content-type', 'text/css');
      $newResponse->getBody()->write($file_content);
      return $newResponse;
      break;
      case "javascript":
      $path = "/../../public/javascript/";
      $complete_path = __DIR__  . $path . $data . ".js";
      $file_content = file_get_contents($complete_path);
      $newResponse = $this->response->withHeader('Content-type', 'text/script');
      $newResponse->getBody()->write($file_content);
      return $newResponse;
      break;
      default:
  // this would be images
      $path = "/../../public/images/";
      $pdo = $GLOBALS["container"]->db;
      $stmt = $pdo->prepare("SELECT type FROM images WHERE filename = :fn");
      $stmt->execute(array(':fn' => $data));
      $type = $stmt->fetch(PDO::FETCH_OBJ)->type;
      $stmt->closeCursor();
      $complete_path = __DIR__ . $path . $data . "." . $type;
      $file_content = file_get_contents($complete_path);
      $newResponse = $this->response->withHeader('Content-type', $type);
      $newResponse->getBody()->write($file_content);
      return $newResponse;
      break;        
    }
  }

  function serve() {
    return $this->static_render($this->args["type"], $this->args["filename"]);
  }
}
?>
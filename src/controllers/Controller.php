<?php 
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Controller {

  protected $request;
  protected $response;
  protected $args;

  function __construct(Request $request, Response $response, $args) {
    $this->request = $request;
    $this->response = $response;
    $this->args = $args;

  }

  protected function render(String $type, $data) {
    switch ($type) {
      case "json":
        // $data should either be an index array or a json string
      if (gettype($data) === "string") {
          return $data; // expect the data in correct json format
        } else {
          return $this->response->withJSON($data, 200);
        }
        break;
        case "html":
        default:
        // expect to send html,  $data should be the html filename
        $path = "/../views/";
        $complete_path = __DIR__  . $path . $data;
        $html_content = file_get_contents($complete_path);

        $this->response->getBody()->write($html_content);
        return $this->response;
        break;
      }
    }

    protected function static_render(String $type, $data) {
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
      }
    } 
  }

  ?>
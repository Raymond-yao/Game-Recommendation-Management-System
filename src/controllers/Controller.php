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

  public function render(String $type, $data) {
    switch ($type) {
      case "json":
        // $data should either be an index array or a json string
      if (gettype($data) === "string") {
          $newResponse = $this->response->withHeader('Content-type', 'text/json');
          $newResponse->getBody()->write($data);
          return $newResponse; // expect the data in correct json format
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
  }

  ?>
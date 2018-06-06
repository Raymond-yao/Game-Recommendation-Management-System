<?php 

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class ListController extends Controller {

  function __construct(Request $request, Response $response, $args) {
    parent::__construct($request, $response, $args);
  }

  function getList() {
    if ($this->request->isXhr()) {
    } else {
      return $this->render("html", "list.html");
    }    
  }
}
?>
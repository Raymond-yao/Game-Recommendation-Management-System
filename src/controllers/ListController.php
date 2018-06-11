<?php 

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class ListController extends Controller {

  function __construct(Request $request, Response $response, $args) {
    parent::__construct($request, $response, $args);
  }

  function getList() {
    if ($this->request->isXhr()) {
      $list = RecommendationList::get($this->args["id"]);
      $creator = User::get($list->creatorID());
      $data = [
        "list_info" => [
          "created_date" => $list->createdDate(),
          "description" => $list->description(),
          "cover" => $list->cover(),
          "title" => $list->title(),
          "creator" => [
            "id" => $creator->id(),
            "username" => $creator->username(),
            "email" => $creator->email(),
            "avatar" => $creator->avatar()
          ]
        ],
        "games" => $list->getGames()
      ];
      return $this->render("json", $data);
    } else {

      $replacement = "var list_id = " . $this->args["id"] . ";";
      return $this->render("html", "list.html", $replacement);
    }    
  }

  function createList() {
    return $this->render("html", "create.html");
  }// send page to browser

  function create() {
    $params = $this->request->getParsedBody(); 
    $title = $params["title"];
    $desc = $params["desc"];
    $file = $this->request->getUploadedFiles();
    $file["cover"]->moveTo(__DIR__ . "/../../public/images/1.jpg");
    $key = ["title"=>$title, "filename"=>"1", "type"=>"jpg", "desc"=>$desc];
    
    $rec = RecommendationList::creatRecList($key);
    return $this->render("json", ["status" => "success", "id" => $rec->id()]);
  }

  function sendGameList() {
    $GL = RecommendationList::getAllGames();
    return $this->render("json", $GL);
  }
}
?>
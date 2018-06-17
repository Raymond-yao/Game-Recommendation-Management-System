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
      $list->viewCount($list->viewCount() + 1);
      $list->save();
      return $this->render("json", $data);
    } else {

      $replacement = "var list_id = " . $this->args["id"] . ";";
      return $this->render("html", "list.html", $replacement);
    }    
  }
  function getTopLists() {
    if ($this->request->isXhr()) {
      $topList = [];
      for($i = 0 ; $i <= 2 ; $i++){
        $list = RecommendationList::get($this->args["id"]+$i);
        $creator = User::get($list->creatorID());
        $data = [
          "list_info" => [
            "id" => $list->id(),
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
        array_push($topList,$data);
      }
      return $this->render("json", $topList);
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
    $random_name = uniqid();
    $gameID = json_decode($params["gameID"]);
    $recReasons = json_decode($params["recReasons"]);
    if(isset($file["cover"])) {
      $this->log($file["cover"]->getClientMediaType());
      $this->log(__DIR__ . "/../../public/images/" . $random_name .".jpg");
      $file["cover"]->moveTo(__DIR__ . "/../../public/images/" . $random_name . ".jpg");
      $key = ["title"=>$title, "filename"=>$random_name, "type"=>"jpg", "desc"=>$desc, "gameID"=>$gameID, "recReasons"=>$recReasons];
    } else {
      $key = ["title"=>$title, "filename"=>null, "type"=>null, "desc"=>$desc, "gameID"=>$gameID, "recReasons"=>$recReasons];
    }
    
    
    $rec = RecommendationList::creatRecList($key);
    return $this->render("json", ["status" => "success", "id" => $rec->id()]);
  }

  function sendGameList() {
    $GL = RecommendationList::getAllGames();
    return $this->render("json", $GL);
  }

  function delete() {
    if (!isset($_COOKIE["account"])) {
      return $this->render("json", ["status" => "unauthorized"]);
    }
    $params = $this->request->getQueryParams(); 
    $listid = $params["listid"];
    $pdo = $GLOBALS["container"]->db;
    // check if this email already exist
    $stmt = $pdo->prepare("DELETE FROM RecommendationLists WHERE id = :listID");
    $stmt->execute(array(':listID' => $listid));
    $stmt->closeCursor();

    $user = User::get($_COOKIE["account"]);
    $user->listCount($user->listCount() - 1);
    $user->save();
    return $this->render("json", ["status" => "success", "id" => $listid]);
  }
}
?>
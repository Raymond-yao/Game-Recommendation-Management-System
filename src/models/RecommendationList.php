<?php 

class RecommendationList extends Model {

  private function __construct($data) {
    parent::__construct();
    foreach ($data as $key => $value) {
      $this->attributes[$key] = ["current" => $value, "origin" => $value];
    }
    $pdo = $GLOBALS["container"]->db;
    $picQuery = $pdo->prepare("SELECT * FROM listcovers JOIN images ON (listcovers.id = images.id) WHERE(listcovers.listid = :id);");
    $picQuery->execute([":id" => $data["id"]]);
    $res = $picQuery->fetch(PDO::FETCH_ASSOC);
    if (empty($res)) {
      $this->attributes["cover"] = ["current" => NULL, "origin" => NULL];
    } else {
      $url = "/assets/image/" . $res["filename"];
      $this->attributes["cover"] = ["current" => $url, "origin" => $url];
    }
  }

  public function save() {
    $sql_head = "UPDATE recommendationlists SET ";
    $modified = [];
    $sql_tail = "WHERE id = :id";
    $count = 0;
    foreach ($this->attributes as $key => $value) {
      if ($key === "cover") continue;
      if ($value["current"] !== $value["origin"]) {
        $count += 1;
        if ($count != 1) {
          $sql_head = $sql_head . ",";
        }
        $modified[":" . $key] = $value["current"];
        $sql_head = $sql_head . $key . "=" . ":" . $key . " ";
      }
    }

    if (!empty($modified)) {
      $modified[":id"] = $this->attributes["id"]["current"];
      $pdo = $GLOBALS["container"]->db;
      $stmt = $pdo->prepare($sql_head . $sql_tail);
      $stmt->execute($modified);
      $stmt->closeCursor();
    }

    // Update list cover
    $cover_chaged = $this->attributes["cover"]["current"] !== $this->attributes["cover"]["origin"];

  }

  public static function get($id) {
    $pdo = $GLOBALS["container"]->db;
    $stmt = $pdo->prepare("SELECT * FROM recommendationlists WHERE id = :id");
    $stmt->execute(array(':id' => $id));


    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    if (empty($result)) {
      return FALSE;
    } else {
      $user = new RecommendationList($result);
      return $user;
    }
  }

  function createdDate() {
    $months = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"];
    $date = $this->attributes["createdDate"]["current"];
    $date_arr = explode("-", $date);
    $m = $months[intval($date_arr[1]) - 1];

    return $date_arr[0] . " " . $m . " " . $date_arr[2];
  }

  function getGames() {
    $pdo = $GLOBALS["container"]->db;
    $stmt = $pdo->prepare("SELECT * FROM games JOIN (SELECT listID, gameID,content FROM recommendationlists JOIN recommendationreasons ON listID = recommendationlists.id) AS data ON games.id = data.gameid WHERE listID = :listID;");
    $stmt->execute([":listID" => $this->id()]);
    $result = $stmt->fetchAll();
    $stmt->closeCursor();
    $game_data = [];
    foreach ($result as $t) {
      $picQuery = $pdo->prepare("SELECT * FROM gamecovers JOIN images ON (gamecovers.id = images.id) WHERE(gamecovers.gameID = :id);");
      $picQuery->execute([":id" => $t["id"]]);
      $res = $picQuery->fetch(PDO::FETCH_ASSOC);
      $stmt->closeCursor();
      $cover = NULL;
      if (!empty($res)) {
        $cover = "/assets/image/" . $res["filename"];
      }

      $game = [
        "name" => $t["name"],
        "date" => $t["salesDate"],
        "company" => $t["company"],
        "cover" => $cover,
        "reason" => $t["content"]
      ];

      array_push($game_data, $game);
    }

    return $game_data;
  }

  static function creatRecList(Array $keys) {
    // get max list id:maxId
    $pdo = $GLOBALS["container"]->db;
    $stmt = $pdo->prepare("SELECT MAX(id) FROM RecommendationLists");
    $stmt->execute();
    $maxId = $stmt->fetch(PDO::FETCH_ASSOC)["MAX(id)"];
    $maxId += 1;
    //get date
    $creatorID = $_COOKIE["account"];
    $date = date("Y-m-d");
    //insert in to RecommendationLists
    $pdo = $GLOBALS["container"]->db;
    $stmt = $pdo->prepare('INSERT INTO RecommendationLists (id, title, description, createdDate, creatorID) VALUES (:id, :title, :description, :createdDate, :creatorID)');
    $stmt->execute(array(':id'=>$maxId, ':title'=>$keys["title"], ':description'=>$keys["desc"], 'createdDate'=>$date, 'creatorID'=>$creatorID));
    //get max image id:maxIId
    $pdo = $GLOBALS["container"]->db;
    $stmt = $pdo->prepare("SELECT MAX(id) FROM Images");
    $stmt->execute();
    $maxIId = $stmt->fetch(PDO::FETCH_ASSOC)["MAX(id)"];
    $maxIId += 1;
    //insert into image
    $pdo = $GLOBALS["container"]->db;
    $stmt = $pdo->prepare('INSERT INTO Images (id, filename, type) VALUES (:id, :filename, :type)');
    $stmt->execute(array(':id'=>$maxIId, ':filename'=>$keys["filename"], ':type'=>$keys["type"]));
    //insert into listCovers
    $pdo = $GLOBALS["container"]->db;
    $stmt = $pdo->prepare('INSERT INTO ListCovers (id, listID) VALUES (:id, :listID)');
    $stmt->execute(array(':id'=>$maxIId, 'listID'=>$maxId));
    //insert into recommend
    foreach ($keys["gameID"] as $chosenGameID) {
      $pdo = $GLOBALS["container"]->db;
      $stmt = $pdo->prepare('INSERT INTO Recommend (listID, gameID) VALUES (:listID, :gameID)');
      $stmt->execute(array(':listID'=>$maxId, ':gameID'=>$chosenGameID));
    }
    foreach ($keys["recReasons"] as $index => $recReasonsTest) {
      $pdo = $GLOBALS["container"]->db;
      $stmt = $pdo->prepare("SELECT MAX(id) FROM RecommendationReasons");
      $stmt->execute();
      $maxRId = $stmt->fetch(PDO::FETCH_ASSOC)["MAX(id)"];
      $maxRId += 1;

      $pdo = $GLOBALS["container"]->db;
      $stmt = $pdo->prepare('INSERT INTO RecommendationReasons (id, content, listID, gameID) VALUES (:id, :content, :listID, :gameID)');
      $stmt->execute(array(':id'=>$maxRId, ':content'=>$recReasonsTest, ':listID'=>$maxId, ':gameID'=>$keys["gameID"][$index]));
    }

    return RecommendationList::get($maxId);

  }

  static function getAllGames() {
    $pdo = $GLOBALS["container"]->db;
    $stmt = $pdo->prepare("SELECT *, SUBSTRING(name, 1, 1) AS init FROM games ORDER BY name ASC");
    $stmt->execute();
    $result = $stmt->fetchAll();
    $stmt->closeCursor();
    $game_data = [];

    foreach ($result as $t) {
      $picQuery = $pdo->prepare("SELECT * FROM gamecovers JOIN images ON (gamecovers.id = images.id) WHERE(gamecovers.gameID = :id);");
      $picQuery->execute([":id" => $t["id"]]);
      $res = $picQuery->fetch(PDO::FETCH_ASSOC);
      $stmt->closeCursor();
      $cover = NULL;
      if (!empty($res)) {
        $cover = "/assets/image/" . $res["filename"];
      }

      $game = [
        "name" => $t["name"],
        "date" => $t["salesDate"],
        "company" => $t["company"],
        "cover" => $cover,
        "id" => $t["id"]
      ];

      $init = $t["init"];
      if (!isset($game_data[$init])) {
        $game_data[$init] = [];
      }
      array_push($game_data[$init], $game);
    }
    return $game_data;
  }
}
?>
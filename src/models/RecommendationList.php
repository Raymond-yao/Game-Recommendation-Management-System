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
}
?>
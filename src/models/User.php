<?php 

class User extends Model {


  private function __construct($data) {
    parent::__construct();
    foreach ($data as $key => $value) {
      $this->attributes[$key] = ["current" => $value, "origin" => $value];
    }
    $pdo = $GLOBALS["container"]->db;
    $picQuery = $pdo->prepare("SELECT * FROM userimages JOIN images ON (userimages.id = images.id) WHERE(userimages.userid = :id AND userimages.imageType = 'avatar')");
    $picQuery->execute([":id" => $data["id"]]);
    $res = $picQuery->fetch(PDO::FETCH_ASSOC);
    if (empty($res)) {
      $this->attributes["avatar"] = ["current" => NULL, "origin" => NULL];
    } else {
      $url = "/assets/image/" . $res["filename"];
      $this->attributes["avatar"] = ["current" => $url, "origin" => $url];
    }

    $picQuery = $pdo->prepare("SELECT * FROM userimages JOIN images ON (userimages.id = images.id) WHERE(userimages.userid = :id AND userimages.imageType = 'background')");
    $picQuery->execute([":id" => $data["id"]]);
    $res = $picQuery->fetch(PDO::FETCH_ASSOC);
    if (empty($res)) {
      $this->attributes["cover"] = ["current" => NULL, "origin" => NULL];
    } else {
      $url = "/assets/image/" . $res["filename"];
      $this->attributes["cover"] = ["current" => $url, "origin" => $url];
    }
  }


  /*
    construct a user object with full attributes based on its id and email
    any attribute of a user can be accessed by calling its name with "()"
    to set a new value, simply add new value to the "()", like user->attribute(newValue);

    i.e. 
      $raymond = User::get("raymond@god.com");
      $raymond->username();    // this will return raymond's username
      $raymond->username("Dante");  // this will change raymond's username to Dante

    p.s. any modified attribute will only be saved after you call $raymond->save();

    @params: User id or email
    @return a User object
  */
    public static function get($key) {
      $pdo = $GLOBALS["container"]->db;
      if (gettype($key) === "string" && strpos($key, '@') !== FALSE) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(array(':email' => $key));
      } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(array(':id' => $key));
      }

      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      $stmt->closeCursor();
      if (empty($result)) {
        return FALSE;
      } else {
        $user = new User($result);
        return $user;
      }
    }
    public static function create(array $key) {

    // checkless!!!
      $newValues = [];
      foreach ($key as $k => $v) { 
        $nk = ":" . $k;
        $newValues[$nk] = $v; 
      }
      $pdo = $GLOBALS["container"]->db;
      $stmt = $pdo->prepare('INSERT INTO users (id, username, password, listCount, friendCount, email, landingPage) VALUES (:id, :username, :password, 0, 0, :email, "overview");');
      $stmt->execute($newValues);
    }

    public function friends() {

      return User::getFriends($this->id);
    }

    function isFriend($id) {
      $pdo = $GLOBALS["container"]->db;
      $stmt = $pdo->prepare("SELECT * FROM friends WHERE followerId = :followerId AND followeeId = :followeeId");
      $stmt->execute(array(':followerId' => $this->id(), ':followeeId' => $id));
      $rowCount = $stmt->rowCount();
      $stmt->closeCursor();
      return $rowCount > 0;
    }

    function addFriend($id) {
      $pdo = $GLOBALS["container"]->db;
      $stmt = $pdo->prepare("INSERT INTO friends(followerId, followeeId) VALUES (:followerId, :followeeId);");
      $stmt->execute(array(':followerId' => $this->id(), ':followeeId' => $id));
      $stmt->closeCursor();
    }

    function deleteFriend($id) {
      $pdo = $GLOBALS["container"]->db;
      $stmt = $pdo->prepare("DELETE FROM friends WHERE followerId = :followerId AND followeeId = :followeeId");
      $stmt->execute(array(':followerId' => $this->id(), ':followeeId' => $id));
      $stmt->closeCursor();
    }

    static function getFriends($id) {
      $pdo = $GLOBALS["container"]->db;
      $stmt = $pdo->prepare("SELECT users.* FROM users, friends WHERE(friends.followerId = :followerId AND friends.followeeId = users.id)");
      $stmt->execute(array(':followerId' => $id));
      $result = $stmt->fetchAll();
      $stmt->closeCursor();
      $res = [];
      foreach ($result as $tuple) {
        array_push($res, new User($tuple));
      }

      return $res;
    }

    public function save() {
      $sql_head = "UPDATE users SET ";
      $modified = [];
      $sql_tail = "WHERE id = :id";
      $count = 0;
      foreach ($this->attributes as $key => $value) {
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
    }

    public static function verify(string $email, string $password) {
      $pdo = $GLOBALS["container"]->db;
      $stmt = $pdo->prepare("SELECT id FROM users WHERE (email = :email AND password = :password)");
      $stmt->execute(array(':email' => $email, ':password' => $password));
      $res = $stmt->fetch(PDO::FETCH_ASSOC);
      $stmt->closeCursor();
      return (empty($res)) ? FALSE : $res["id"];
    }

  }

  ?>
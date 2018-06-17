<?php 

class User extends Model {


  private function __construct($data) {
    parent::__construct();
    foreach ($data as $key => $value) {
      $this->attributes[$key] = ["current" => $value, "origin" => $value];
    }
    $pdo = $GLOBALS["container"]->db;
    $picQuery = $pdo->prepare("SELECT userimages.userID, images.filename FROM userimages,images WHERE(userimages.id = images.id AND userimages.userid = :id AND userimages.imageType = 'avatar');");
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
      $pdo = $GLOBALS["container"]->db;
      // find max id in user table
      $pdo->beginTransaction();
      $stmt = $pdo->prepare("SELECT MAX(id) FROM users");
      $stmt->execute();
      $maxId = $stmt->fetch(PDO::FETCH_ASSOC)["MAX(id)"];
      $maxId += 1;
      $key["id"] = $maxId;

    // checkless!!!
      $newValues = [];
      foreach ($key as $k => $v) { 
        $nk = ":" . $k;
        $newValues[$nk] = $v; 
      }
      try {
        $stmt = $pdo->prepare('INSERT INTO users (id, username, password, listCount, friendCount, email, landingPage) VALUES (:id, :username, :password, 0, 0, :email, "overview");');
        $stmt->execute($newValues);
        $pdo->commit();
        return ['status' => "success register"];
      } catch (PDOException $e) {
        $pdo->rollBack();
        $GLOBALS["container"]->logger->warn($e->getMessage());
        return ["status" => "failed", "reason" => "username or password is too short, at least 3 characters each"];
      }
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
      $stmt = $pdo->prepare("SELECT users.* FROM users, friends WHERE(friends.followerId = :followerId AND friends.followeeId = users.id) ORDER BY username ASC");
      $stmt->execute(array(':followerId' => $id));
      $result = $stmt->fetchAll();
      $stmt->closeCursor();
      $res = [];
      foreach ($result as $tuple) {
        array_push($res, new User($tuple));
      }

      return $res;
    }

    function recommendationLists() {
      return getRecommendationLists($this->id);
    }

    function getStat($args) {
      $pdo = $GLOBALS["container"]->db;
      $type = $args["type"];
      $extreme_query = NULL;
      switch ($type) {
        case 'count':
        $stmt = $pdo->prepare("SELECT comp.username, comp.listcount FROM (SELECT data.username, COUNT(creatorID) AS listcount FROM (SELECT viewCount,title,creatorID,username,email FROM recommendationlists RIGHT JOIN (SELECT DISTINCT id, username,listcount,friendcount,email FROM users JOIN friends ON(friends.followeeID = users.id) WHERE friends.followerid = :id1 OR users.id  = :id2) AS fri ON creatorID = fri.id) AS data GROUP BY data.username) AS comp;
");
        $stmt->execute([":id1" => $this->id(), ":id2" => $this->id()]);
        $data = $stmt->fetchAll();
        return ["users" => $data];
        break;
        case 'average':
        $stmt = $pdo->prepare("SELECT comp.username,comp.id ,comp.avgViewCount FROM (SELECT data.username,data.id, AVG(viewCount) AS avgViewCount FROM (SELECT viewCount,title,creatorID,username,fri.id,email FROM recommendationlists RIGHT JOIN (SELECT DISTINCT id, username,listcount,friendcount,email FROM users JOIN friends ON(friends.followeeID = users.id) WHERE friends.followerid = :id1 OR users.id  = :id2) AS fri ON creatorID = fri.id) AS data GROUP BY data.username) AS comp;");
        if ($args["extreme"] === "max"){
          $extreme_query = $pdo->prepare("SELECT MAX(comp.avgViewCount) as ext FROM (SELECT data.username, AVG(viewCount) AS avgViewCount FROM (SELECT viewCount,title,creatorID,username,email FROM recommendationlists RIGHT JOIN (SELECT DISTINCT id, username,listcount,friendcount,email FROM users JOIN friends ON(friends.followeeID = users.id) WHERE friends.followerid = :id1 OR users.id  = :id2) AS fri ON creatorID = fri.id) AS data GROUP BY data.username) AS comp;");
        } else {
          $extreme_query = $pdo->prepare("SELECT MIN(comp.avgViewCount) as ext FROM (SELECT data.username, AVG(viewCount) AS avgViewCount FROM (SELECT viewCount,title,creatorID,username,email FROM recommendationlists RIGHT JOIN (SELECT DISTINCT id, username,listcount,friendcount,email FROM users JOIN friends ON(friends.followeeID = users.id) WHERE friends.followerid = :id1 OR users.id  = :id2) AS fri ON creatorID = fri.id) AS data GROUP BY data.username) AS comp;");
        }
        $stmt->execute([":id1" => $this->id(), ":id2" => $this->id()]);
        $extreme_query->execute([":id1" => $this->id(), ":id2" => $this->id()]);
        $data = $stmt->fetchAll();
        $ext = $extreme_query->fetch(PDO::FETCH_ASSOC)["ext"];
        return [
          "extreme" => [
            "type" => strtoupper($args["extreme"]),
            "value" => $ext 
          ],
          "users"=> $data
        ];
        break;
      }
    }

    static function getRecommendationLists($id) {
      $pdo = $GLOBALS["container"]->db;
      $stmt = $pdo->prepare("SELECT * FROM recommendationlists LEFT JOIN (SELECT filename, listcovers.listID FROM images JOIN listcovers ON images.id = listcovers.id) AS cover ON (cover.listID = recommendationlists.id) WHERE (creatorID = :id);");
      $stmt->execute([":id" => $id]);
      $res = $stmt->fetchAll();
      $stmt->closeCursor();

      $result = [];
      foreach ($res as $tuple) {
        $tuple_info = [
          "id" => $tuple["id"],
          "cover" => $tuple["filename"] === NULL ? NULL : "/assets/images/" . $tuple["filename"],
          "title" => $tuple["title"],
          "desc" => $tuple["description"],
          "created_date" => $tuple["createdDate"]
        ];

        array_push($result, $tuple_info);
      }

      return ["recommendations" => $result];
    }

    public function save() {
      $sql_head = "UPDATE users SET ";
      $modified = [];
      $sql_tail = "WHERE id = :id";
      $count = 0;

      try{
        foreach ($this->attributes as $key => $value) {
          if ($key === "avatar" || $key === "cover") continue;
          if ($value["current"] !== $value["origin"]) {
            $count += 1;
            if ($count != 1) {
              $sql_head = $sql_head . ",";
            }
            $modified[":" . $key] = $value["current"];
            $sql_head = $sql_head . $key . "=" . ":" . $key . " ";
          }
        }
        $pdo = $GLOBALS["container"]->db;
        if (!empty($modified)) {
          $modified[":id"] = $this->attributes["id"]["current"];
          $stmt = $pdo->prepare($sql_head . $sql_tail);
          $stmt->execute($modified);
          $stmt->closeCursor();
        }
      }catch (PDOException $e) {
        $GLOBALS["container"]->logger->warn($e->getMessage());
        return ["status" => "failed", "reason" => "username or password is too short, at least 3 characters each"];
      }

      // update avatar and background
      $avatar_changed = $this->attributes["avatar"]["current"] !== $this->attributes["avatar"]["origin"];
      $background_changed = $this->attributes["cover"]["current"] !== $this->attributes["cover"]["origin"];
      if ($avatar_changed || $background_changed){
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("SELECT MAX(id) FROM images");
        $stmt->execute();
        $maxId = $stmt->fetch(PDO::FETCH_ASSOC)["MAX(id)"];
        $stmt = $pdo->prepare("INSERT INTO images (id, filename ,Type) VALUES (:id, :fn, :type);");

        if ($avatar_changed) {
          $filename = explode(".", $this->attributes["avatar"]["current"]);
          $maxId += 1;
          $stmt->execute([
            ":id" => $maxId, 
            ":fn" => $filename[0],
            ":type" => $filename[1]
          ]);
          $this->setupHelper($maxId, "avatar");
        }

        if ($background_changed) {
          $filename = explode(".", $this->attributes["cover"]["current"]);
          $maxId += 1;
          $stmt->execute([
            ":id" => $maxId, 
            ":fn" => $filename[0],
            ":type" => $filename[1]
          ]);
          $this->setupHelper($maxId, "background");
        }
        $pdo->commit();
      }
      return ["status" => "success"];
    }

    private function setupHelper($image_id, $type) {
      /*
       * this function assumes a picture has already been added to /public/images and IMAGES table. 
       * what this function does is to simply remove the exisiting user images (if there is) and replace a 
       * new one
      */
      $attr = $type === "background" ? "cover" : $type;  // A mistake dated back from my crappy php design, forgive me :P
      $pdo = $GLOBALS["container"]->db;
      if ($this->attributes[$attr]["origin"] !== NULL) {
        $stmt = $pdo->prepare('SELECT id FROM userimages WHERE userID = :id AND imageType = "' . $type . '"');
        $stmt->execute([
          ":id" => $this->id()
        ]);
        $id = $stmt->fetch(PDO::FETCH_ASSOC)["id"];
        $stmt = $pdo->prepare('DELETE FROM userimages WHERE userID = :id AND imageType = "' . $type . '"');
        $stmt->execute([
          ":id" => $this->id()
        ]);
        $stmt = $pdo->prepare("DELETE FROM images WHERE id = :id ;");
        $stmt->execute([
          ":id" => $id
        ]);
        
      }

      $stmt = $pdo->prepare('INSERT INTO userimages (id, userID, imageType) VALUES (:id, :userID, "' . $type . '")');
      $stmt->execute([
        ":id" => $image_id,
        ":userID" => $this->id()
      ]);
    }

    public static function verify(string $email, string $password) {
      $pdo = $GLOBALS["container"]->db;
      $stmt = $pdo->prepare("SELECT id FROM users WHERE (email = :email AND password = :password)");
      $stmt->execute(array(':email' => $email, ':password' => $password));
      $res = $stmt->fetch(PDO::FETCH_ASSOC);
      $stmt->closeCursor();
      return (empty($res)) ? FALSE : $res["id"];
    }

    public static function search(string $name, $type, $id = NULL) {
      $pdo = $GLOBALS["container"]->db;
      switch ($type) {
        case 1:
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username LIKE :name");
        $name .= "%";
        $stmt->execute(array(':name' => $name));
        $count_query = $pdo->prepare("SELECT COUNT(*) as count FROM (SELECT * FROM users WHERE username LIKE :name) AS result");
        $count_query->execute(array(':name' => $name));
        break;
        case 2:
        if ($id){
          $stmt = $pdo->prepare("SELECT * FROM users WHERE username LIKE :name AND users.id NOT IN (SELECT id FROM users JOIN friends ON users.id = friends.followeeid AND followerid = :id1 UNION SELECT id FROM users WHERE id = :id2);");
          $name .= "%";
          $stmt->execute(array(':name' => $name, ':id1' => $id, ':id2' => $id));
          $count_query = $pdo->prepare("SELECT COUNT(*) as count FROM (SELECT * FROM users WHERE username LIKE :name AND users.id NOT IN (SELECT id FROM users JOIN friends ON users.id = friends.followeeid AND followerid = :id1 UNION SELECT id FROM users WHERE id = :id2)) AS result");
          $count_query->execute(array(':name' => $name, ':id1' => $id, ':id2' => $id));
        }
        break;
        case 3:
        if ($name === "") {
          $stmt = $pdo->prepare("SELECT * FROM users res WHERE NOT EXISTS( SELECT C.id FROM users C WHERE C.id <> res.id AND C.id NOT IN (SELECT followeeID FROM friends WHERE followerID = res.id));");
          $stmt->execute();
          $count_query = $pdo->prepare("SELECT COUNT(*) as count FROM (SELECT * FROM users res WHERE NOT EXISTS( SELECT C.id FROM users C WHERE C.id <> res.id AND C.id NOT IN (SELECT followeeID FROM friends WHERE followerID = res.id))) AS result");
          $count_query->execute();
        } else {
          $stmt = $pdo->prepare("SELECT * FROM users res WHERE res.username LIKE :name AND NOT EXISTS( SELECT C.id FROM users C WHERE C.id <> res.id AND C.id NOT IN (SELECT followeeID FROM friends WHERE followerID = res.id));");
          $name .= "%";
          $stmt->execute(array(':name' => $name));
          $count_query = $pdo->prepare("SELECT COUNT(*) as count FROM (SELECT * FROM users res WHERE res.username LIKE :name AND NOT EXISTS( SELECT C.id FROM users C WHERE C.id <> res.id AND C.id NOT IN (SELECT followeeID FROM friends WHERE followerID = res.id))) AS result");
          $count_query->execute(array(':name' => $name));
        }
        break;
      }
      $result = $stmt->fetchAll();
      $count = $count_query->fetch(PDO::FETCH_ASSOC)["count"];
      $count_query->closeCursor();
      $stmt->closeCursor();
      $res = [];
      foreach ($result as $tuple) {
        array_push($res, new User($tuple));
      }
      return ["count" => $count, "result" => $res];
    }

  }

  ?>
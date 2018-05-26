<?php 

class User extends Model {


  private function __construct($data) {
    parent::__construct();
    foreach ($data as $key => $value) {
      $this->attributes[$key] = ["current" => $value, "origin" => $value];
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
    $id = $stmt->fetch(PDO::FETCH_OBJ)->id;
    $stmt->closeCursor();
    return ($id === NULL) ? FALSE : $id;
  }

}

?>
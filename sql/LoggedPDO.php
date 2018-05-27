<?php 

class LoggedStatement {
  private $stmt;

  function __construct($stm) {
    $this->stmt = $stm;
  }

  function __call(string $name , array $arguments) {
    if ($name === "execute") {
      $sql = $this->stmt->queryString;
      if (isset($arguments[0])) {
        $params = $arguments[0];
        $keys = [];
        $values = [];
        foreach ($params as $key => $value) {
          array_push($keys, $key);
          array_push($values, $value);
        }
        $complete_sql = str_replace($keys, $values, $sql);
        $GLOBALS["container"]->logger->warn($complete_sql . "\r\n");
        return $this->stmt->$name($params);
      } else {
        $sql = $this->stmt->queryString;
        $GLOBALS["container"]->logger->warn($sql . "\r\n");
        return $this->stmt->$name();
      }
    }
    if (isset($arguments[0])){
      return $this->stmt->$name($arguments[0]);
    } else {
      return $this->stmt->$name();
    }
  }
}

class LoggedPDO {

  private $pdo;

  function __construct($db) {
    $this->pdo = new PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'],
      $db['user'], $db['pass']);
    $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES , FALSE);
    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  }

  public function __call(string $name , array $arguments){
    if ($name === "prepare") {
      $original_stmt = $this->pdo->$name($arguments[0]);
      return new LoggedStatement($original_stmt);
    } 
    return $this->pdo->$name($arguments[0]);
  }
};
?>
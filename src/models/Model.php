<?php 

abstract class Model {

  protected static $pdo;
  protected $attributes;

  protected function __construct() {
    $this->attributes = [];
  }

  public function __call(string $name, array $argument) {
    if (isset($this->attributes[$name])) {
      if(sizeof($argument) === 0) {
        return $this->attributes[$name]["current"];
      } else {
        $this->attributes[$name]["current"] = $argument[0];
        return TRUE;
      }
    } else {
      return NULL;
    }
  }

  abstract public function save();

  abstract public static function get($key);
}
?>
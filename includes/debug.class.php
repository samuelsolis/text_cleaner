<?php 

/**
 * @class debug. For debug things
 */
abstract class debug{
  var $debug;
  const ERROR = 1;
  const WARNING = 2;
  const MESSAGE = 3;

  public function __construct(){
    $debug = false;
  }

  /**
   * Set $debug to $state
   */
  public function set_debug($state = false){
    $this->debug = $state;
  }

  /**
   * Print $string if $debug is true
   */
  public function debug_message($string, $type = self::MESSAGE){
    if ($this->debug){

      switch($type){
      case self::ERROR:
          $color = "\033[41m ";
          break;
      case self::WARNING: 
          $color = "\033[43m ";
          break;
      case self::MESSAGE:
          $color = "\033[42m ";
      }

      echo $color . $string . "\033[0m" . PHP_EOL;
    }
  }
}

?>

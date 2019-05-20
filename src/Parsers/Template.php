<?php 

namespace Parsers;

abstract class Template {
  protected $filePath;

  public function setFile($filePath) {
    if (!file_exists($filePath)) {
      return false;
    }
    $this->filePath = $filePath;
    return true;
  } 
}

?>

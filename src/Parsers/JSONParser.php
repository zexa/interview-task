<?php

namespace Parsers;

class JSONParser extends Template {

  public function parse() {
    if (!isset($this->filePath)) {
      return null;
    }
    return json_decode(file_get_contents($this->filePath), true);
  }

}

?>

<?php

namespace Smoqadam;

class Trigger {
  
  public
    $pattern,
    $callback;

  public function __construct($pattern, $callback) {
    $this->pattern  = $pattern;
    $this->callback = $callback;
  }

}

<?php

namespace Smoqadam\InlineQuery\Result;

class Article {

  public
    $type = 'article',
    $id,
    $title,
    $input_message_content;

  public function __construct($title, $content) {
    $this->title = $title;
    $this->input_message_content = $content;
    $this->id = '' . rand();
  }

}

<?php

namespace Smoqadam\Input;
use Smoqadam\Telegram;

class TextMessageContent {

  public
    $message_text,
    $parse_mode,
    $disable_web_page_preview;

  public function __construct($message_text, $parse_mode = Telegram::PARSE_MARKDOWN, $disable_web_page_preview = false) {
    $this->message_text             = $message_text;
    $this->parse_mode               = $parse_mode;
    $this->disable_web_page_preview = $disable_web_page_preview;
  }

}

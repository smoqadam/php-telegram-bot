<?php

namespace Smoqadam\Keyboard;

class Inline {

  public
    $inline_keyboard = [];
    
  private
    $current_row = 0;
  
  /**
  * Create new row.
  *
  * @return object
  */
  public function addRow(){
    $this->current_row++;
    
    return $this;
  }
  
  /**
  * Add new button to current row.
  *
  * @param string $text Label text on the button
  * @param string $url Optional. HTTP url to be opened when button is pressed
  * @param string $callback_data Optional. Data to be sent in a callback query to the bot when button is pressed, 1-64 bytes
  * @param string $switch_inline_query Optional. If set, pressing the button will prompt the user to select one of their chats, open that chat and insert the bot‘s username and the specified inline query in the input field. Can be empty, in which case just the bot’s username will be inserted.
  * @param string $switch_inline_query_current_chat Optional. If set, pressing the button will insert the bot‘s username and the specified inline query in the current chat's input field. Can be empty, in which case only the bot’s username will be inserted.
  * @param string $callback_game Optional. Description of the game that will be launched when the user presses the button. NOTE: This type of button must always be the first button in the first row.
  * @return object
  */
  public function addButton(string $text, string $url = '', string $callback_data = '', string $switch_inline_query = '', string $switch_inline_query_current_chat = '', string $callback_game = ''){
    $btn['text'] = $text;
    if($url != '')
      $btn['url'] = $url;
    elseif($callback_data != '')
      $btn['callback_data'] = $callback_data;
    elseif($switch_inline_query != '')
     $btn['switch_inline_query'] = $switch_inline_query;
    elseif($switch_inline_query_current_chat != '')
      $btn['switch_inline_query_current_chat'] = $switch_inline_query_current_chat;
    elseif($callback_game != '')
      $btn['callback_game'] = $callback_game;
    else
      $btn['callback_data'] = $text;
      
    $this->inline_keyboard[$this->current_row][] = $btn;
    
    return $this;
  }

}

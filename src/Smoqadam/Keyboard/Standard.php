<?php

namespace Smoqadam\Keyboard;

class Standard {

  public
    $keyboard = [],
    $resize_keyboard,
    $one_time_keyboard,
    $selective;
    
  private
    $current_row = 0;

  public function __construct($resize_keyboard = false, $one_time_keyboard = false, $selective = false) {
    $this->resize_keyboard    = $resize_keyboard;
    $this->one_time_keyboard  = $one_time_keyboard;
    $this->selective          = $selective;
  }
  
  /**
  * Create new row.
  *
  * @return object Standard
  */
  public function addRow(){
    $this->current_row++;
    
    return $this;
  }
  
  /**
  * Add new button to current row.
  *
  * @param string $text 	Text of the button. If none of the optional fields are used, it will be sent to the bot as a message when the button is pressed
  * @param boolean $request_contact Optional. If True, the user's phone number will be sent as a contact when the button is pressed. Available in private chats only
  * @param boolean $request_location Optional. If True, the user's current location will be sent when the button is pressed. Available in private chats only
  * @return object
  */
  public function addButton(string $text, boolean $request_contact = null, boolean $request_location = null){
    $this->keyboard[$this->current_row][] = [
      'text' => $text,
      'request_contact' => ($request_contact != null) ? $request_contact : false,
      'request_location' => ($request_location != null) ? $request_location : false
    ];
    
    return $this;
  }

}

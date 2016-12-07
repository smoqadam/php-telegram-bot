<?php

namespace Smoqadam\Keyboard;

class Remove {
    
    public
        $remove_keyboard = true,
        $selective;
        
    /*
    * Telegram clients will remove the current custom keyboard and display the default letter-keyboard.
    *
    * @param boolean $selective Optional. Use this parameter if you want to remove the keyboard for specific users only. Targets: 1) users that are @mentioned in the text of the Message object; 2) if the bot's message is a reply (has reply_to_message_id), sender of the original message.
    */
    public function __construct($selective = false){
        $this->selective        = $selective;
    }
}
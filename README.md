# php-telegram-bot
###a wrapper class for telegram bot api

##this class has not been completed yet!!

## install 
create composer.json :
```
{
    "require": {
        "smoqadam/php-telegram-bot": "dev-master"
    }
}
```

`$ composer install`


## how to use :

```php
<?php

require 'vendor/autoload.php';

use Smoqadam\Telegram;

$tg = new Telegram('API_TOKEN');

$tg->cmd('\/name:<<[a-zA-Z]{0,}>>', function ($args) use ($tg){
		$tg->sendMessage("my username is @".$args , $tg->getChatId());
});


$tg->cmd('\/number: <<:num>>' , function($args) use($tg){
	$tg->sendMessage("your number is : ".$args , $tg->getChatId()); 
});


$tg->cmd('Hello',function () use ($tg){
	$tg->sendChatAction(Telegram::ACTION_TYPING);
	$image = 'urltoqrcode.png';
	$tg->sendPhoto($image);
});

$tg->run();

```

now when you send `/number 123` in telegram bot page , bot will answer to you `your number is 123`

you can set argument in regex between << and >> 

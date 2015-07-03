# php-telegram-bot
a wrapper class for telegram bot api

#this class has not been compeleted yet!!

how to use :

## install 
create composer.json :
```
{
    "require": {
        "smoqadam/php-telegram-bot": "dev-master"
    }
}
```
then `composer install`

```php
<?php

require 'vendor/autoload.php';

use Smoqadam\Telegram;
$tg = new Telegram('TELEGRAM_API_TOKEN');

$tg->cmd('\/number: <<:num>>' , function($args) use($tg){
	$tg->sendMessage("your number is : ".$args); 
});


$tg->cmd('Hello',function ($args) use ($tg){
	$tg->sendMessage("Hello World!");
});

$tg->run();

```

now when you send `/number 123` in telegram bot page , bot will answer to you `your number is 123`

you can set argument in regex between << and >> 

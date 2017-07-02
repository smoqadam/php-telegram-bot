[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/smoqadam/php-telegram-bot/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/smoqadam/php-telegram-bot/?branch=master)
[![Codacy Badge](https://www.codacy.com/project/badge/7008dc0d211c4bba95e5b31537702050)](https://www.codacy.com/app/phpro-ir/php-telegram-bot)
# php-telegram-bot
A wrapper class for Telegram Bot API

## Install
create composer.json :
```
{
    "require": {
        "smoqadam/php-telegram-bot": "dev-master"
    }
}
```

`$ composer install`

## How to use:
`Smoqadam\Telegram` is a wrapper around Telegram bot API. After you instantiate `Telegram` object, you can register callbacks on the updates you receive and then respond accordingly.

**Register callbacks:** Use following functions:
-  `cmd()`, all normal messages
-  `inlineQuery()`


**API Methods:**
The available methods are almost same of official Telegram API (for now the wrapper does not natively handles games and messages update):
-   `sendMessage()`, `getMe()`, `forwardMessage()`, `sendPhoto()`, `sendVideo()`, `sendSticker()`, `sendLocation()`, `sendDocument()`, `sendAudio()`, `sendChatAction()`, `getUserProfilePhotos()`, `answerInlineQuery()`

**Getting current update:**
The current update is stored in the property `Telegram::result` (that is an object).

**Inline Result helper:**
To facilitate the creation of Inline Bot there are some helper classes under namespace `InlineQuery\Result`:
-   `Article`

**Keyboards helper:**
Also the same with keyboards; namespace is `Keyboard`:
-   `Standard`, classic keyboard
-   `Remove`, remove custom keyboard and show letter-keyboard
-   `Inline`, inline keyboard

**Use Keyboard:**
Use the keyboard is pretty simple with helpers, after you instantiate keyboard (`Standard` or `Inline`) you use:
-  `addButton()`
-  `addRow()`

_Note:_ These methods are chainable.

## Example
**Using long polling:**
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


**Using webHooks:**

If you want to use webhooks you have to first call `setWebhook` method or open the following URL with your own data:

`https://api.telegram.org/API_TOKEN/setWebhook?url=https://yourdomain.com/index.php`
>please change API_TOKEN and url parameter

```php
<?php
require 'vendor/autoload.php';

$message = file_get_contents('php://input');

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

$tg->process(json_decode($message, true));

```
Now when you send `/number 123` in telegram bot page , bot will answer to you `your number is 123`

You can set argument in regex between << and >>

## Running bot
`$ php index.php`

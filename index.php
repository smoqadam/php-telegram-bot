<?php


require 'vendor/autoload.php';

use Smoqadam\Telegram;

$tg = new Telegram('68777318:AAFxwqwuIrW9xZ4pi1bM3QJwrHpf1wOYbgQ');

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

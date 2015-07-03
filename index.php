<?php


require 'vendor/autoload.php';


$tg = new Smoqadam\Telegram('68777318:AAFxwqwuIrW9xZ4pi1bM3QJwrHpf1wOYbgQ');

$tg->cmd('\/name:<<[a-zA-Z]{0,}>>', function ($args) use ($tg){
		$tg->sendMessage($tg->result->message->chat->id , "my username is @".$args);
});


$tg->cmd('\/number: <<:num>>' , function($args) use($tg){
	$tg->sendMessage($tg->result->message->chat->id , "your number is : ".$args); 
});


$tg->cmd('Hello',function ($args) use ($tg){
	$image = 'logo.php.png';
	$tg->sendSticker($tg->result->message->chat->id , $image);
});

$tg->run();

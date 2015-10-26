<?php


require 'vendor/autoload.php';

use Smoqadam\Telegram;

$tg = new Telegram('API_TOKEN');

$tg->cmd('name:<<[a-zA-Z]{0,}>>', function ($args) use ($tg){
	
		$tg->sendMessage("my username is @".$args , $tg->getChatId());
});


$tg->cmd('<<:any>>',function ($arg) use ($tg){
	$tg->sendMessage("You Message is : " . $arg, $tg->getChatId()); 
});

$tg->run();

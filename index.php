<?php
require_once 'loader.php';
$tg = new Telegram('68777318:AAFxwqwuIrW9xZ4pi1bM3QJwrHpf1wOYbgQ');

$tg->cmd('/name' , function ($args) use ($tg){
		$tg->forwardMessage();
		$tg->sendMessage("my username is @".$tg->getMe());
});


$tg->cmd('/help' , function($args) use($tg){

	if(!$args){
		$tg->sendMessage("/name 	get my name \r\n/help 	[command]");
	}

	if($args == 'name'){
		$tg->sendMessage("get my name");
	}

	if($args == 'help'){
		$tg->sendMessage("show this menu");
	}
});


$tg->run();

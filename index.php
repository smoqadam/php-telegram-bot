<?php
require_once 'loader.php';
$tg = new Telegram('68777318:AAFxwqwuIrW9xZ4pi1bM3QJwrHpf1wOYbgQ');

$tg->cmd('\/name:<<[a-zA-Z]{0,}>>', function ($args) use ($tg){
		$tg->sendMessage("my username is @".$args);
});


$tg->cmd('\/number: <<:num>>' , function($args) use($tg){

		$tg->sendMessage("your number is : ".$args); 

});


$tg->cmd('Hello',function ($args) use ($tg){
			$tg->sendMessage("Hello World!");
});

$tg->run();

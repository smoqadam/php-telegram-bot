<?php
//require_once 'lib/Telegram.php';

require 'vendor/autoload.php';


$tg = new Telegram('68777318:AAFxwqwuIrW9xZ4pi1bM3QJwrHpf1wOYbgQ');

$tg->cmd('\/name:<<[a-zA-Z]{0,}>>', function ($args) use ($tg){
		$tg->sendMessage("my username is @".$args);
});


$tg->cmd('\/number: <<:num>>' , function($args) use($tg){
	$tg->sendMessage("your number is : ".$args); 
});


$tg->cmd('Hello',function ($args) use ($tg){
	$image = fopen('e:\\logo.php.png', 'r');
	// $image = file_get_contents('e:\\logo.php.png');
    $images = json_decode(file_get_contents('http://ajax.googleapis.com/ajax/services/search/images?v=1.0&q=' . urlencode($description) . '&rsz=8'), true)['responseData']['results'];
    $image = fopen($images[array_rand($images)]['unescapedUrl'], 'r');
	
	$tg->sendPhoto($tg->result->message->chat->id , '@'.$image);
});

$tg->run();

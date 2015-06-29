<?php
require_once 'loader.php';
$tg = new Telegram('68777318:AAFxwqwuIrW9xZ4pi1bM3QJwrHpf1wOYbgQ');


while(true){
	$tg->cmd('test' , function ($args) use ($tg){
			if($args){
				$tg->sendMessage( 'سلام علی !! ');
			}
		}
	);


	$tg->cmd('/help' , function($args) use($tg){
		var_dump(($args==true));
		if(!$args){
			$tg->sendMessage("test [args]\r\nhelp [command]");
		}
echo $args;
		if($args == 'test'){
			$tg->sendMessage("http://digiato.com/wp-content/uploads/2015/06/screen-shot-2015-06-28-at-10.12.44-am.jpg");
		}
	});
}
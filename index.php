<?php

$tg = new Telegram('68777318:AAFxwqwuIrW9xZ4pi1bM3QJwrHpf1wOYbgQ');

function __autoload($class)
{
		$class = 'lib/'.str_replace('\\', '/', $class).'.php';
		require $class;
}


while(true){
	echo '1';
	$tg->cmd('test' , function () use ($tg){
			$tg->sendMessage($tg->chat_id , 'Test Command emmited!!');
		}
	);

}
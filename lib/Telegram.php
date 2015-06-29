<?php


class Telegram{

	public $api = 'https://api.telegram.org/bot';

	public $chat_id ; 

	public $update_id ; 

	private $commands;

	public function __construct($token){
		$this->api .= $token;

		// $this->request = new Request($this->api);
		$this->commands = new Commands($this->api);
	}


	public function cmd($cmd , $func)
	{
		$result = $this->commands->exec('getUpdates' , ['offset'=>$this->update_id+1,'limit'=>1,'timeout'=>1]);
		if($result){
			echo "\r\nresult OK\r\n";

			if($result['result'] != null){
				
				$result = $result['result'][0];
				$this->update_id = $result['update_id'];
				$this->chat_id   = $result['message']['chat']['id'];
				$reciveMessage = $result['message']['text'];

				echo "\r\n".$reciveMessage.'==========='.$cmd."\r\n";
				if(is_callable($func) AND $cmd == $reciveMessage){
					echo "Command OK\r\n";
					return $func($this);
				}
			}else{
				echo "no new message\r\n";
			}
		}
	}


	public function sendMessage($chat_id , $text)	
	{
		$this->commands->exec('sendMessage',['chat_id'=>$this->chat_id,'text'=>$text]);
	}


}
<?php


class Telegram{

	public  $api = 'https://api.telegram.org/bot';

	private  $chat_id ; 

	private  $update_id ; 

	private $available_commands = [
		    'getMe',
		    'sendMessage',
		    'forwardMessage',
		    'sendPhoto',
		    'sendAudio',
		    'sendDocument',
		    'sendSticker',
		    'sendVideo',
		    'sendLocation',
		    'sendChatAction',
		    'getUserProfilePhotos',
		    'getUpdates',
		    'setWebhook',
	];


	public function __construct($token){
		$this->api .= $token;
	}


	public function cmd($cmd , $func)
	{
		$result = $this->exec('getUpdates' , ['offset'=>$this->update_id+1,'limit'=>1,'timeout'=>1]);
		if($result){

			if($result['result'] != null){
				
				$result = $result['result'][0];
				$this->update_id = $result['update_id'];
				$this->chat_id   = $result['message']['chat']['id'];
				$reciveMessage   = $result['message']['text'];
				$args = $this->proccessArgs($reciveMessage);
				if(is_callable($func) AND $cmd == $args['cmd']){
					return $func($args['args']);
				}
			}else{
				echo "no new message\r\n";
			}
		}
	}


	/**
	* @param msg reviced message 
	*/
	private function proccessArgs($msg )
	{
		if(!$msg)
			return null;

		$args = null;
		$msg = explode(' ',$msg);
		$cmd = array_shift($msg);
		if(!empty($msg))
			$args = implode(' ',$msg);

		return ['cmd'=>trim($cmd),'args'=>trim($args)];

	}



	public function sendMessage( $text)	
	{
		$this->exec('sendMessage',['chat_id'=>$this->chat_id,'text'=>$text]);
	}

	public function sendImage($test)
	{
				$this->exec('sendMessage',['chat_id'=>$this->chat_id,'text'=>'https://www.google.com/images/nav_logo195.png']);

	}

	public function exec($command , $params = [])
	{
		if(in_array($command, $this->available_commands)	){
				$params = http_build_query($params);
				return json_decode($this->curl_get_contents($this->api.'/'.$command.'?'.$params),true);
		}else{
			echo 'command not found';
		}
	}


	function curl_get_contents($url)
	{
		  $ch = curl_init($url);
		  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		  $data = curl_exec($ch);
		  curl_close($ch);
		  return $data;
	}
}
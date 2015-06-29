<?php


class Telegram{

	public  $api = 'https://api.telegram.org/bot';

	private $chat_id ; 

	private $update_id ; 

	private $result ;

	private $commands = [];

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
		$this->message = new stdClass();
	}


	/**
	* add new command to the bot
	* @param $cmd String
	* @param $func Closure 
	*/
	public function cmd($cmd , $func)
	{
		$this->commands[$cmd] = $func;
	}


	/**
	* this method check for recived message(command) and then execute the 
	* command function
	*/
	public function run()
	{

		$this->result = $this->exec('getUpdates') ;
		while(true){
			$update_id = isset($this->result->update_id) ? $this->result->update_id : 1;
			$result = $this->exec('getUpdates' , ['offset'=> $update_id + 1,'limit'=>1,'timeout'=>1]) ;

			if($result){
				print_r($result);
				$this->result = $result;
				list($cmd , $args) = $this->processMessage($this->result->message->text);

				if(isset($this->commands[$cmd])){
					if(is_callable($this->commands[$cmd])){
						echo 		$this->log("command : $cmd ");
						$func = $this->commands[$cmd];
						$func($args);
					}	
				}
			}else{
				echo $this->log("no new message");
			}
			// sleep(1);
		}
	}


   /**
	* @param String $msg 
	*/
	private function processMessage ($msg )
	{
		if(!$msg)
			return null;
		$args = null;
		$msg = explode(' ',$msg);
		$cmd = strtolower(array_shift($msg));
		if(!empty($msg))
			$args = strtolower(implode(' ',$msg));

		return [trim($cmd),trim($args)];
	}


	/**
	* execute Telegram api commands
	* @para $command String
	* @param $params Array
	*/
	private function exec($command , $params = [])
	{
		if(in_array($command, $this->available_commands)	){
			$params = http_build_query($params);
			// convert json to array then get the last messages info 
			$output = json_decode($this->curl_get_contents($this->api.'/'.$command.'?'.$params),true);

			$result = [] ;
			// we need to last message information
			if($output) 
				$result = @end(@end($output));

			if(!empty($result))
			{
				// convert to object 
				return json_decode(json_encode($result));
			}
		}else{
			echo 'command not found';
		}
	}


   /**
    *  get telegram api content with curl
    */
   private function curl_get_contents($url)
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


   private function log($log)
   {
   	return "\r\n$log";
   }


	/**
	* send message
	* @param String 
	*/
	public function sendMessage( $text)	
	{
		$this->exec('sendMessage',[
			'chat_id'=>$this->result->message->chat->id,
			'text'=>$text
			]);
	}

	public function sendImage($test)
	{
		$this->exec('sendMessage',['chat_id'=>$this->result->message->chat->id,'text'=>'']);
	}


	public function getMe()
	{
		return $this->exec('getMe');
	}


	public function getMe2()
	{
		return $this->exec('getMe');
	}


	public function forwardMessage()
	{
		return $this->exec('forwardMessage',[
			'chat_id'     =>$this->result->message->chat->id,
			'from_chat_id'=>$this->result->message->from->id ,
			'message_id'  =>$this->result->message->message_id,
		 ]);
	}




}
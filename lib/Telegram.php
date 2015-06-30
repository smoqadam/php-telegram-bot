<?php


class Telegram{

	public  $api = 'https://api.telegram.org/bot';

	private $result ;

	private $commands  = [];

	private $callbacks = [];

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


	private $patterns = [
		':any'=>'.*',
		':num'=>'[0-9]{0,}',
		':str'=>'[a-zA-z]{0,}',
	];

	public function __construct($token){
	
		$this->api .= $token;

	}


	/**
	* add new command to the bot
	* @param $cmd String
	* @param $func Closure 
	*/
	public function cmd($cmd , $func)
	{
		$this->commands[] = $cmd;
		$this->callbacks[] = $func;
	}


	/**
	* this method check for recived message(command) and then execute the 
	* command function
	*/
	public function run()
	{

		$result = $this->getUpdates();
		while(true){
			$update_id = isset($result->update_id) ? $result->update_id : 1;
			$result = $this->getUpdates($update_id+1);
			
			if($result){

				$this->result = $result;
				
				// message recived by user
				$recived_command  = $this->result->message->text ;

				$args = null;
	
				$pos = 0;
				foreach ($this->commands as $pattern) {
					
					// replace public patterns to regex pattern					
					$searchs  = array_keys($this->patterns);
					$replaces = array_values($this->patterns);
					$pattern  = str_replace($searchs, $replaces, $pattern);

					//find args pattern
					preg_match('/<<.*>>/', $pattern , $matches);

					// if command has argument
					if(isset($matches[0]) AND !empty($matches[0])){
						$args_pattern = $matches[0];
						//remove << and >> from patterns
						
						$tmp_args_pattern = str_replace(['<<','>>'], ['(',')'], $pattern);

						//if args set
						if(preg_match('/'.$tmp_args_pattern.'/i', $recived_command,$matches)){
							//remove first element 
							array_shift($matches);

							if(isset($matches[0])){

								//set args						
								$args = array_shift($matches);
				
								//remove args pattern from main pattern
								$pattern = str_replace($args_pattern,''	,$pattern);

							}
						}
					}


					$pattern = '/^'.$pattern.'/i';

					preg_match($pattern, $recived_command , $matches);
					
					if(isset($matches[0])){

						$func = $this->callbacks[$pos];

						call_user_func($func, $args);

					}

					$pos++;
				}

			}else{
				echo "\r\nNo new message\r\n";
			}
			// sleep(1);
		}
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

			if($output){ 

				// remove unwanted array elements
				$output = end($output);
				$result = end($output);
			
				if(!empty($result))
				{

					// convert to object 
					return json_decode(json_encode($result));

				}
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


	/**
	* get updates
	* @param String 
	*/
	public function getUpdates($offset = null, $limit = 1 , $timeout = 1 )	
	{
		return $this->exec('getUpdates',[
			'offset'=>$offset,
			'limit'=>$limit , 
			'timeout'=>$timeout
			]);
	}


	/**
	* send message
	* @param String 
	*/
	public function sendMessage( $text)	
	{
		return $this->exec('sendMessage',[
			'chat_id'=>$this->result->message->chat->id,
			'text'=>$text
			]);
	}


	/**
	* get bot username
	*/
	public function getMe()
	{
		return $this->exec('getMe');
	}



	/**
	* forward message [$message_id] from [$from_id] to [$chat_id]
	*/
	public function forwardMessage($chat_id , $from_id , $message_id)
	{
		return $this->exec('forwardMessage',[
			'chat_id'     =>$chat_id,
			'from_chat_id'=>$from_id,
			'message_id'  =>$message_id,
			]);
	}


	public function sendPhoto($test)
	{
		// as soons as possible
	}



	public function sendVideo($test)
	{
		// as soons as possible
	}



	public function sendSticker($test)
	{
		// as soons as possible
	}



	public function sendLocation($test)
	{
		// as soons as possible
	}


	public function sendDocument($test)
	{
		// as soons as possible
	}


	public function sendAudio($test)
	{
		// as soons as possible
	}

	public function sendChatAction($test)
	{
		// as soons as possible
	}


	public function getUserProfilePhotos($test)
	{
		// as soons as possible
	}

	public function setWebhook($test)
	{
		// as soons as possible
	}


}
<?php

namespace Smoqadam;


class Telegram{



	const ACTION_TYPING = 'typing';
	const ACTION_UPLOAD_PHOTO = 'upload_photo';
	const ACTION_RECORD_VIDEO = 'record_video';
	const ACTION_UPLOAD_VIDEO = 'upload_video';
	const ACTION_RECORD_AUDIO = 'record_audio';
	const ACTION_UPLOAD_AUDIO = 'upload_audio';
	const ACTION_UPLOAD_DOC = 'upload_document';
	const ACTION_FIND_LOCATION = 'find_location';


	public  $api = 'https://api.telegram.org/bot';

	public $result ;

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
	public function run($sleep = false)
	{

		$result = $this->getUpdates();
		while(true){
			
			$update_id = isset($result->update_id) ? $result->update_id : 1;
			$result = $this->getUpdates($update_id+1);

			if($result){
				try{
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
						$args = $this->getArgs($pattern , $recived_command);

						$pattern = '/^'.$pattern.'/i';

						preg_match($pattern, $recived_command , $matches);

						if(isset($matches[0])){

							$func = $this->callbacks[$pos];

							call_user_func($func, $args);

						}

						$pos++;

					}
				}catch(\Exception $e){
					echo "\r\n Exception :: ".$e->getMessage();
				}
			}else{
				echo "\r\nNo new message\r\n";
			}
			if($sleep !== false)
				sleep($sleep);
		}
	}


	private function getArgs(&$pattern , $recived_command )
	{
		
		$args = null;
		// if command has argument
		if(preg_match('/<<.*>>/', $pattern , $matches) && isset($matches[0]) && !empty($matches[0])){
		
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
		return $args;
	}

	
	/**
	* execute Telegram api commands
	* @para $command String
	* @param $params Array
	*/
	private function exec($command , $params = [])
	{
		if(in_array($command, $this->available_commands)	){


			// convert json to array then get the last messages info 
			$output = json_decode($this->curl_get_contents($this->api.'/'.$command , $params),true);

			if($output['ok']){ 

				// remove unwanted array elements
				$output = end($output);

				$result = is_array($output) ? end($output) : $output ;

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
   private function curl_get_contents($url , $params )
   {

   	$ch = curl_init();

   	curl_setopt($ch, CURLOPT_URL, $url);

   	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

   	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

   	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);	

   	curl_setopt($ch, CURLOPT_POST, count($params));

   	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

   	$result = curl_exec($ch);

   	curl_close($ch);

   	return $result;

   }

   public function getChatId($chat_id = null){
   	if($chat_id)
   		return $chat_id;

   	return $this->result->message->chat->id;	
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
	public function sendMessage($text  , $chat_id , $disable_web_page_preview = false , $reply_to_message_id = null , $reply_markup = null)	
	{
		return $this->exec('sendMessage',[
			'chat_id'=>$this->getChatId($chat_id) , 		
			'text'=>$text , 
			'disable_web_page_preview'=>$disable_web_page_preview,
			'reply_to_message_id' =>$reply_to_message_id,
			'reply_markup' =>$reply_markup
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
	public function forwardMessage( $from_id ,  $message_id , $chat_id = null)
	{
		return $this->exec('forwardMessage',[
			'chat_id'     =>$this->getChatId($chat_id) , 
			'from_chat_id'=>$from_id,
			'message_id'  =>$message_id,
			]);
	}


	public function sendPhoto($photo , $chat_id = null , $caption = null , $reply_to_message_id = null , $reply_markup = null)
	{

		$res =  $this->exec('sendPhoto',[
			'chat_id'			 =>$this->getChatId($chat_id) , 
			'photo'  			 =>'@'.$photo ,
			'caption'	=>$caption ,
			'reply_to_message_id'=>$reply_to_message_id,
			'reply_markup' 		 =>$reply_markup
			] );

		return $res;

	}



	public function sendVideo($video , $chat_id=null , $reply_to_message_id = null , $reply_markup = null )
	{
		$res =  $this->exec('sendVideo',[
			'chat_id'=>$this->getChatId($chat_id) , 
			'video'  =>'@'.$video,
			'reply_to_message_id'=>$reply_to_message_id,
			'reply_markup' 		 =>$reply_markup
			] );

		return $res;	
	}


	public function sendSticker( $sticker ,$chat_id = null , $reply_to_message_id = null , $reply_markup = null )
	{		
		$res =  $this->exec('sendSticker',[
			'chat_id'=> $this->getChatId($chat_id) ,
			'sticker'  =>'@'.$sticker,
			'reply_to_message_id'=>$reply_to_message_id,
			'reply_markup' 		 =>$reply_markup
			] );

		return $res;	
		// as soons as possible
	}



	public function sendLocation($latitude , $longitude , $chat_id = null ,  $reply_to_message_id = null , $reply_markup = null )
	{
		$res =  $this->exec('sendLocation',[
			'chat_id'=>$this->getChatId($chat_id) , 
			'latitude'  => $latitude,
			'longitude' =>$longitude,
			'reply_to_message_id'=>$reply_to_message_id,
			'reply_markup' 		 =>$reply_markup
			] );

		return $res;	
	}


	public function sendDocument( $document ,$chat_id = null , $reply_to_message_id = null , $reply_markup = null )
	{		
		$res =  $this->exec('sendDocument',[
			'chat_id'=>$this->getChatId($chat_id) , 
			'document'  =>'@'.$document,
			'reply_to_message_id'=>$reply_to_message_id,
			'reply_markup' 		 =>$reply_markup
			] );

		return $res;	
	}

	public function sendAudio( $audio , $chat_id = null ,$reply_to_message_id = null , $reply_markup = null )
	{		
		$res =  $this->exec('sendAudio',[
			'chat_id'=>$this->getChatId($chat_id) ,  
			'audio'  =>'@'.$audio,
			'reply_to_message_id'=>$reply_to_message_id,
			'reply_markup' 		 =>$reply_markup
			] );

		return $res;	
	}

	public function sendChatAction( $action , $chat_id = null )
	{
		$res =  $this->exec('sendChatAction',[
			'chat_id' => $this->getChatId($chat_id) , 
			'action'  => $action
			] );

		return $res;	
	}


	public function getUserProfilePhotos($user_id , $offset = null , $limit = null )
	{
		$res =  $this->exec('getUserProfilePhotos',[
			'user_id' => $user_id , 
			'offset'  => $offset  ,
			'limit'   => $limit   
			] );

		return $res;
	}

	public function setWebhook($url)
	{
		$res =  $this->exec('setWebhook',[
			'url' => $url 
			]);

		return $res;
	}


}

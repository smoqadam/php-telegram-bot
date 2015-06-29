<?php



class Commands{


	private $api;

	private $avalable_commands = [
		'getUpdates','sendMessage'
	];

	function __construct($api)
	{
		$this->api = $api;
	}

	public function exec($command , $params = [])
	{
		if(in_array($command, $this->avalable_commands)	){
				$params = http_build_query($params);
				print_r($params);
				return json_decode($this->curl_get_contents($this->api.'/'.$command.'?'.$params),true);
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
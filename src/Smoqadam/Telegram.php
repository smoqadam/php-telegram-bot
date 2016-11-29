<?php
/**
 * A wrapper class for telegram bot api
 *
 * @home page : https://github.com/smoqadam/php-telegram-bot
 * @author : Saeed Moqadam <phpro.ir@gmail.com>
 * @licence : MIT Licence
 *
 */
namespace Smoqadam;

class Telegram {

  const PARSE_MARKDOWN = 'Markdown';
  const PARSE_HTML     = 'HTML';

  const ACTION_TYPING         = 'typing';
  const ACTION_UPLOAD_PHOTO   = 'upload_photo';
  const ACTION_RECORD_VIDEO   = 'record_video';
  const ACTION_UPLOAD_VIDEO   = 'upload_video';
  const ACTION_RECORD_AUDIO   = 'record_audio';
  const ACTION_UPLOAD_AUDIO   = 'upload_audio';
  const ACTION_UPLOAD_DOC     = 'upload_document';
  const ACTION_FIND_LOCATION  = 'find_location';

    public $api = 'https://api.telegram.org/bot';

    /**
     * returned json from telegram api parse to object and save to result
     * @var
     */
    public $result;

     /**
      * @name State of bot
     * state of bot
     * @var object
     */
    public $state;

    /**
     * commands in regex and callback
     * @var array
     */
    private $commands = [];

    /**
     * InlineQuery in regex and callback
     * @var array
     */
    private $inlines = [];

    /**
     * callbacks
     * @var array
     */
    private $callbacks = [];

    /**
     * available telegram bot commands
     * @var array
     */
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
        'answerInlineQuery',
        'getUpdates',
        'setWebhook',
    ];

    /**
     * pre patterns you can use in regex
     * @var array
     */
    private $patterns = [
        ':any' => '.*',
        ':num' => '[0-9]{0,}',
        ':str' => '[a-zA-z]{0,}',
    ];

    /**
     *
     * @param String $token Telegram api token , taken by botfather
     */
    public function __construct($token) {
        $this->api .= $token;
    }

    /**
     * add new command to the bot
     * @param String $cmd
     * @param \Closure $func
     */
    public function cmd($cmd, $func) {
      $this->commands[] = new Trigger($cmd, $func);
    }

    /**
     * add new InlineQuery to the bot
     * @param String $cmd
     * @param \Closure $func
     */
    public function inlineQuery($cmd, $func) {
      $this->inlines[] = new Trigger($cmd, $func);
    }

    /**
     * this method check for recived payload(command, inlinequery and so on) and
     * then execute the correct function
     *
     * @param bool $sleep
     */
    public function run($sleep = false) {
        $result = $this->getUpdates();
        while (true) {
            $update_id = isset($result->update_id) ? $result->update_id : 1;
            $result = $this->getUpdates($update_id + 1);

            $this->processPayload($result);

            if ($sleep !== false)
                sleep($sleep);
        }
    }

	/**
	* this method used for setWebhook sended payload
	*/
    public function process($payload) {
    	$result = $this->convertToObject($payload, true);

      return $this->processPayload($result);
    }

    private function processPayload($result) {
    	if ($result) {
            try {
                $this->result = $result;

                // now i select the right triggers for payload received by user
                if( isset($this->result->message) ) {
                  $payload = $this->result->message->text;
                  $triggers = $this->commands;
                } elseif ( isset($this->result->inline_query) ) {
                  $payload = $this->result->inline_query->query;
                  $triggers = $this->inlines;
                } else {
                  throw new \Exception("Error Processing Request", 1);
                }

                $args = null;

                foreach ($triggers as &$trigger) {
                    // replace public patterns to regex pattern
                    $searchs = array_keys($this->patterns);
                    $replaces = array_values($this->patterns);
                    $pattern = str_replace($searchs, $replaces, $trigger->pattern);

                    //find args pattern
                    $args = $this->getArgs($pattern, $payload);

                    $pattern = '/^' . $pattern . '/i';

                    preg_match($pattern, $payload, $matches);

                    if (isset($matches[0])) {
                        $func = $trigger->callback;
                        call_user_func($func, $args);
                    }
                }
            } catch (\Exception $e) {
              error_log($e->getMessage());
              echo "\r\n Exception :: " . $e->getMessage();
            }
        } else {
            echo "\r\nNo new message\r\n";
        }
    }

    /**
     * get arguments part in regex
     * @param $pattern
     * @param $payload
     * @return mixed|null
     */
    private function getArgs(&$pattern, $payload) {
        $args = null;
        // if command has argument
        if (preg_match('/<<.*>>/', $pattern, $matches)) {

            $args_pattern = $matches[0];
            //remove << and >> from patterns
            $tmp_args_pattern = str_replace(['<<', '>>'], ['(', ')'], $pattern);

            //if args set
            if (preg_match('/' . $tmp_args_pattern . '/i', $payload, $matches)) {
                //remove first element
                array_shift($matches);
                if (isset($matches[0])) {
                    //set args
                    $args = array_shift($matches);

                    //remove args pattern from main pattern
                    $pattern = str_replace($args_pattern, '', $pattern);
                }
            }
        }
        return $args;
    }

    /**
     * execute telegram api commands
     * @param $command
     * @param array $params
     */
    private function exec($command, $params = []) {
        if (in_array($command, $this->available_commands)) {
          // convert json to array then get the last messages info
          $output = json_decode($this->curl_get_contents($this->api . '/' . $command, $params), true);

        	return $this->convertToObject($output);
        } else {
            echo 'command not found';
        }
    }

    private function convertToObject($jsonObject , $webhook = false) {
      if( ! $webhook) {
        if ($jsonObject['ok']) {
          //error_log(print_r($jsonObject, true));

          // remove unwanted array elements
          $output = end($jsonObject);

          $result = is_array($output) ? end($output) : $output;
          if ( ! empty($result)) {
              // convert to object
              return json_decode(json_encode($result));
          }
        }
      } else {
        return json_decode(json_encode($jsonObject));
      }
    }

    /**
     * get the $url content with CURL
     * @param $url
     * @param $params
     * @return mixed
     */
    private function curl_get_contents($url, $params) {
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

    /**
     * Get current chat id
     * @param null $chat_id
     * @return int
     */
    public function getChatId($chat_id = null) {
      try {
        if ($chat_id)
          return $chat_id;

        if( isset($this->result->message) ) {
          return $this->result->message->chat->id;
        } elseif ( isset($this->result->inline_query) ) {
          return $this->result->inline_query->from->id;
        } else {
          throw new \Exception("Error Processing Request", 1);
        }
      } catch (\Exception $e) {
        error_log($e->getMessage());
      }
    }

    /**
     * @param null $offset
     * @param int $limit
     * @param int $timeout
     */
    public function getUpdates($offset = null, $limit = 1, $timeout = 1) {
        return $this->exec('getUpdates', [
            'offset' => $offset,
            'limit' => $limit,
            'timeout' => $timeout
        ]);
    }

    /**
     * send message
     * @param $text
     * @param $chat_id
     * @param bool $disable_web_page_preview
     * @param null $reply_to_message_id
     * @param null $reply_markup
     */
    public function sendMessage($text, $chat_id = null, $disable_web_page_preview = false, $reply_to_message_id = null, $reply_markup = null) {
        $this->sendChatAction(self::ACTION_TYPING, $chat_id);
        return $this->exec('sendMessage', [
            'chat_id' => $this->getChatId($chat_id),
            'text' => $text,
            'disable_web_page_preview' => $disable_web_page_preview,
            'reply_to_message_id' => $reply_to_message_id,
            'reply_markup' => $reply_markup
        ]);
    }

    /**
     * Get me
     */
    public function getMe() {
        return $this->exec('getMe');
    }

    /**
     * @param $from_id
     * @param $message_id
     * @param null $chat_id
     */
    public function forwardMessage($from_id, $message_id, $chat_id = null) {
        return $this->exec('forwardMessage', [
            'chat_id' => $this->getChatId($chat_id),
            'from_chat_id' => $from_id,
            'message_id' => $message_id,
        ]);
    }

    /**
     * @param $photo photo file patch
     * @param null $chat_id
     * @param null $caption
     * @param null $reply_to_message_id
     * @param null $reply_markup
     */
    public function sendPhoto($photo, $chat_id = null, $caption = null, $reply_to_message_id = null, $reply_markup = null) {
        $res = $this->exec('sendPhoto', [
            'chat_id' => $this->getChatId($chat_id),
            'photo' => $photo,
            'caption' => $caption,
            'reply_to_message_id' => $reply_to_message_id,
            'reply_markup' => $reply_markup
        ]);

        return $res;
    }

    /**
     * @param $video video file path
     * @param null $chat_id
     * @param null $reply_to_message_id
     * @param null $reply_markup
     */
    public function sendVideo($video, $chat_id = null, $reply_to_message_id = null, $reply_markup = null) {
        $res = $this->exec('sendVideo', [
            'chat_id' => $this->getChatId($chat_id),
            'video' => $video,
            'reply_to_message_id' => $reply_to_message_id,
            'reply_markup' => $reply_markup
        ]);

        return $res;
    }

    /**
     *
     * @param $sticker
     * @param null $chat_id
     * @param null $reply_to_message_id
     * @param null $reply_markup
     */
    public function sendSticker($sticker, $chat_id = null, $reply_to_message_id = null, $reply_markup = null) {
        $res = $this->exec('sendSticker', [
            'chat_id' => $this->getChatId($chat_id),
            'sticker' => $sticker,
            'reply_to_message_id' => $reply_to_message_id,
            'reply_markup' => $reply_markup
        ]);

        return $res;
        // as soons as possible
    }

    /**
     * @param $latitude
     * @param $longitude
     * @param null $chat_id
     * @param null $reply_to_message_id
     * @param null $reply_markup
     */
    public function sendLocation($latitude, $longitude, $chat_id = null, $reply_to_message_id = null, $reply_markup = null) {
        $res = $this->exec('sendLocation', [
            'chat_id' => $this->getChatId($chat_id),
            'latitude' => $latitude,
            'longitude' => $longitude,
            'reply_to_message_id' => $reply_to_message_id,
            'reply_markup' => $reply_markup
        ]);

        return $res;
    }

    /**
     * @param $document
     * @param null $chat_id
     * @param null $reply_to_message_id
     * @param null $reply_markup
     */
    public function sendDocument($document, $chat_id = null, $reply_to_message_id = null, $reply_markup = null) {
        $res = $this->exec('sendDocument', [
            'chat_id' => $this->getChatId($chat_id),
            'document' => $document,
            'reply_to_message_id' => $reply_to_message_id,
            'reply_markup' => $reply_markup
        ]);

        return $res;
    }

    public function sendAudio($audio, $chat_id = null, $reply_to_message_id = null, $reply_markup = null) {
        $res = $this->exec('sendAudio', [
            'chat_id' => $this->getChatId($chat_id),
            'audio' => $audio,
            'reply_to_message_id' => $reply_to_message_id,
            'reply_markup' => $reply_markup
        ]);

        return $res;
    }

    /**
     * send chat action : Telegram::ACTION_TYPING , ...
     * @param $action
     * @param null $chat_id
     */
    public function sendChatAction($action, $chat_id = null) {
        $res = $this->exec('sendChatAction', [
            'chat_id' => $this->getChatId($chat_id),
            'action' => $action
        ]);

        return $res;
    }

    /**
     * @param $user_id
     * @param null $offset
     * @param null $limit
     */
    public function getUserProfilePhotos($user_id, $offset = null, $limit = null) {
        $res = $this->exec('getUserProfilePhotos', [
            'user_id' => $user_id,
            'offset' => $offset,
            'limit' => $limit
        ]);

        return $res;
    }


    public function answerInlineQuery($inline_query_id, $results, $cache_time = 0, $is_personal = false, $next_offset = '', $switch_pm_text = '', $switch_pm_parameter = '') {
      $res = $this->exec('answerInlineQuery', [
        'inline_query_id' => $inline_query_id,
        'results' => json_encode($results),
        'cache_time' => $cache_time,
        'is_personal' => $is_personal,
        'next_offset' => $next_offset,
        'switch_pm_text' => $switch_pm_text,
        'switch_pm_parameter' => $switch_pm_parameter
      ]);

      return $res;
    }

    /**
     * @param $url
     */
    public function setWebhook($url) {
        $res = $this->exec('setWebhook', [
            'url' => $url
        ]);

        return $res;
    }

}

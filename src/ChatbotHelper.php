<?php

namespace DonMarkus;


use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use pimax\FbBotApp;
use pimax\Messages\Message;
use pimax\Messages\ImageMessage;

#include_once "DataBot.php";

class ChatbotHelper
{    

    public $config;
    protected $chatbotAI;
    protected $facebookSend;
    protected $log;
    private $input;
    private $resPATH;

    public function __construct()
    {
        $dotenv = new Dotenv(__DIR__ . '/../');
        $dotenv->load();
        $this->config = require __DIR__ . '/config.php';
        $this->chatbotAI = new ChatbotAI($this->config);
        $this->facebookSend = new FbBotApp($this->config['access_token']);
        $this->log = new Logger('general');
        //$this->log->pushHandler(new StreamHandler('debug.log'));
        $this->log->pushHandler(new StreamHandler('php://stderr'));
        $this->input = $this->getInput();
        $this->resPATH = "https://blooming-spire-13615.herokuapp.com/resources/";
    }

    /**
     * @return mixed
     */
    public function getInputData()
    {
        return $input;
    }

    private function getInput()
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    /**
     * Get the sender id of the message
     * @return int
     * @internal param $input
     */
    public function getSenderId()
    {
        return $this->input['entry'][0]['messaging'][0]['sender']['id'];
    }

    /**
     * Get the user's message from input
     * @return mixed
     * @internal param $input
     */
    public function getMessage()
    {
        return $this->input['entry'][0]['messaging'][0]['message']['text'];
    }

    /**
     * Check if the callback is a user message
     * @return bool
     * @internal param $input
     */
    public function isMessage()
    {
        return isset($this->input['entry'][0]['messaging'][0]['message']['text']) && !isset
            ($this->input['entry'][0]['messaging'][0]['message']['is_echo']);

    }

     /**
     * Check if the callback is a POSTBACK
     * @return bool
     * @internal param $input
     */
    public function isPostback()
    {
        return isset($this->input['entry'][0]['messaging'][0]['postback']["payload"]);

    }

    /**
     * Get the user's message from input
     * @return mixed
     * @internal param $input
     */
    public function getPayload()
    {
        return $this->input['entry'][0]['messaging'][0]['postback']["payload"];
    }

    /**
     * Get the answer to a given user's message
     * @param null $api
     * @param string $message
     * @return string
     */
    public function getAnswer($message, $api = null)
    {

        if ($api === 'apiai') {
            return $this->chatbotAI->getApiAIAnswer($message);
        } elseif ($api === 'witai') {
            $key= $this->chatbotAI->getWitAIAnswer($message);
            switch ($key) {
                case 'hello':
                        return $this->SayHello();
                    break;
                case 'weather':
                        return $this->SayWeather();
                    break;
                case 'time':
                        return $this->SayTime();
                    break;
                case 'get_from':
                        return $this->SayFrom();
                    break;
                case 'yourname':
                        return $this->SayName();
                    break;
                case "Where_place":
                        $Data = new DataBot();
                        $place = $this->chatbotAI->getLocalsearchquery();
                        $resData = $Data->GetLocation($place);
                        return $resData;
                case "events":
                        return "There is no events yet!!!";

                default:
                    return "Hmmm, I'm not sure I understand. Can you ask again? Try \"What's the weather like?\" or \"What time is it?\"";                    
                    break;
            }
        } elseif ($api === 'rates') {
            return $this->chatbotAI->getForeignExchangeRateAnswer($message);
        } else {
            return $this->chatbotAI->getAnswer($message);
        }

    }

    /**
     * Send a reply back to Facebook chat
     * @param $senderId
     * @param $replyMessage
     * @return array
     */
    public function send($senderId, string $replyMessage)
    {
        return $this->facebookSend->send(new Message($senderId, $replyMessage));
    }


    public function sendImg($senderId, string $ImageURL)
    {
        return $this->facebookSend->send( new ImageMessage($senderId, $ImageURL));    
    }


    /**
     * Verify Facebook webhook
     * This is only needed when you setup or change the webhook
     * @param $request
     * @return mixed
     */
    public function verifyWebhook($request)
    {
        if (!isset($request['hub_challenge'])) {
            return false;
        };

        $hubVerifyToken = null;
        $hubVerifyToken = $request['hub_verify_token'];
        $hubChallenge = $request['hub_challenge'];

        if (null !== $hubChallenge && $hubVerifyToken === $this->config['webhook_verify_token']) {

            echo $hubChallenge;
        }


    }

    public function SayHello()
    {
        $UsrData=$this->facebookSend->userProfile($this->getSenderId());
        return "Hello ".$UsrData->getFirstName().", My name is City Bot and I am here to show you all the cool things you can do at your San Leandro Library, Museum and Historic Places.";
    }

    public function SayWeather()
    {
        $key = "394790e3cc664b5a83345444171506";
        $city = 'San Leandro';
        $url =  "http://api.apixu.com/v1/current.json?key=$key&q=".urlencode($city) ;        

        $this->log->warning($url);
        $ch = curl_init();  
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        
        $json_output=curl_exec($ch);
        $weather = json_decode($json_output);
        return "The weather now, is ".$weather->current->condition->text." at ".$weather->current->temp_f." F";        
    }

    public function SayTime()
    {
        $now = new \DateTime();
        $now->setTimezone(new \DateTimezone('America/Los_Angeles'));
        return "The local time is ".$now->format('H:i:s')." in 24Hrs Format" ;
    }

     public function SayFrom()
    {        
        return "I'm From San Leandro..." ;
    }

    public function SayName()
    {        
        return "My Name is CityBot" ;
    }
}
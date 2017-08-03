<?php

namespace DonMarkus;
require 'Facebook/autoload.php';

use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use pimax\FbBotApp;
use pimax\Messages\Message;
use pimax\Messages\ImageMessage;
use pimax\Messages\StructuredMessage;
use pimax\Messages\MessageButton;
use pimax\Messages\Attachment;
use pimax\Messages\MessageElement;
use Bexi\DataBot;




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
            file_put_contents("php://stderr", "KEY=".$key."\n");  
            switch ($key) {
                case 'hello':
                        $this->SayHello();
                        return "";
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
                case "where_place":
                        file_put_contents("php://stderr", "where_place");                        
                        $place = $this->chatbotAI->getLocalsearchquery();
                        file_put_contents("php://stderr", "place=".$place);
                        $Data = new Databot();
                        $resData = $Data->GetLocation($place);
                        file_put_contents("php://stderr", $resData);
                        return $resData;
                    break;
                case "events":
                        $fecha = strtotime($this->chatbotAI->getDatetime());
                        file_put_contents("php://stderr", "Events=".$fecha);    

                        $this->GetEvents($fecha);
                        return "";
                        break;
                case "Manuel":
                        return "Manuel is the best programmer in history for ever, and very handsome too. He  developed me. ;) (y)";
                        break;
                case "computer_classes":
                        return "No. We do not have formal computer classes at the library. You may sign up for a one-on-one free tutoring session with an adult volunteer, however, by calling 510-577-3971 to set up an appointment. For a formal class on computers, you may call the Senior Center at 510-577-3462 or the San Leandro Adult School at 510-667-6287.";
                        break;
                case "esl_classes":
                        return "No. We do not have ESL Classes.  You may call the San Leandro Adult School at 510-667-6287 for information on ESL Classes. You may call Chabot College (Hayward, CA) at 510-723-6600 or Laney College (Oakland, CA) at 510- 834-5740 for more information about ESL Classes.";
                        break;
                case "story_time":
                        file_put_contents("php://stderr", "<<<story_time>>>");    
                        $this->send($this->getSenderId(),"Ages 0- 12 months: Baby Time (English) Tuesdays at 9:30 AM. Babies Pre-walkers ages 0 to 12 months and caregivers bond during this short session with books, songs, and plays.");
                        $this->send($this->getSenderId(),"Cuentacuentos: Canciones y cuentos para ninos pequenos Martes 10:30 AM Main Library.");
                        $this->send($this->getSenderId()," Disfruta libros, cuentos, y canciones en Espanol en la biblioteca con tus ninos pequenos. Main Library");
                        $this->send($this->getSenderId(),"Toddler Story time:  Wednesday 9:30 AM and 10:30 AM. Stories, rhymes, and romps for ages 1-3. Just the right pace for you and your totally nonstop toddler up to age 3.");
                        $this->send($this->getSenderId()," Preschool Story time: Stories, songs, and play for preschoolers. Wednesdays at 1:30 PM. Main Library Pattycakes Story Time: Thursdays at 10:30 AM. For children ages 2-5. All children must be accompanied by an adult. (Manor Library)");

                        return ;
                        break;
                case "family_story":
                        $this->send($this->getSenderId(),"Family Story time: Wednesday 7:00 PM");
                        $this->send($this->getSenderId(),"Stories, songs, and play for families. Our evening program offers terrific books, songs and rhymes for kids of all ages to enjoy. Wear your pajamas! Main Library.");
                        return ;
                        break;
                case "lawyers":
                        return "Lawyers in the Library takes place on the Third Thursday of the Month- therefore: Thursday, July 20. Thursday, August 17, September 21, October 19, November 16, and December 21. Sign ups begin at 5:30 PM. The Volunteer Lawyers give 15 minute consultations from 6:00 PM – 8:00 PM.";
                        break;
                case "sl_museum":
                        return "Entry to the SL Museums is free to the public. On the first Saturdays of the month, come to free film showings throughout the day. During the week, the San Leandro Museum hosts a Living History Tour to youths in 3rd and 4th Grades to supplement their California History curriculum. The Museum is also open during the week on Wednesdays with its LEGO activities.";
                        break;
                case "tutor":
                        return "Although many tutors work with students at the library, the library is not affiliated with professional tutors.";
                        break;

                default:
                   /* $Data2 = new Databot();
                    $resData = $Data2->GetLocation($message);
                    if ($resData != "")
                    {
                        return $resData;
                    }else{*/
                        return "Hmmm, I'm not sure I understand. Can you ask again? Try \"What's the weather like?\" or \"What time is it?\"";                    
                    #}
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

    public function sendMsj( Message $Msg)
    {
        return $this->facebookSend->send($Msg);    
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
        $this->send($this->getSenderId(), "Hello ".$UsrData->getFirstName().", My name is City Bot and I am here to show you all the cool things you can do at your San Leandro Library, Museum and Historic Places.");
        //$this->send($this->getSenderId(), "Are you ready ?");
        $this->facebookSend->send(new StructuredMessage($this->getSenderId(),
          StructuredMessage::TYPE_BUTTON,
          [
              'text' => 'Are you ready ?',
              'buttons' => [
                  new MessageButton(MessageButton::TYPE_POSTBACK, 'OK',"CMD_OK")
              ]
          ]
      )); 
        
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

    public function GetEvents($fechaev){
        file_put_contents("php://stderr", "GetEvents");         
        $fb = new \Facebook\Facebook([
          'app_id' => '1347080372047215',
          'app_secret' => '97d6f4ebe503098fb7cfb45577b7c1f9',
          'default_graph_version' => 'v2.10',
          'default_access_token' => '1347080372047215|q-RxSd7MDCZtXP_UOvcP5Bk5Lqw', // optional
        ]);
        try {
            file_put_contents("php://stderr", "Request"); 
            $Data = new Databot();
            $Pages = $Data->GetPagesId();
            foreach ($Pages as &$page)
            {
                $response = $fb->get('/'.$page.'/events?time_filter=upcoming');  
                file_put_contents("php://stderr", '/'.$page.'/events?time_filter=upcoming');          
                $resev=$response->getDecodedBody();
             #   file_put_contents("php://stderr", print_r($resev["data"],true)); 
                foreach ($resev["data"] as &$ev) {
                    $tmp["id"]=$ev["id"];
                    $tmp["description"]=$ev["description"];
                    $tmp["name"]=$ev["name"];
                    $tmp["date"]=$ev["start_time"];
                    $eventos[]=$tmp;
                }
            }
            
            $noev=1;

            $eventos = array_filter($eventos,function ($element) use ($fechaev) { return ($fechaev <= strtotime($element["date"]));});
            
            usort($eventos, array("DonMarkus\ChatbotHelper","sortFunction"));

            foreach ($eventos as &$ev2) {
                if ($noev>=10)
                {
                    break;
                }                
                $response2 = $fb->get('/'.$ev2["id"].'/picture?redirect=false&type=large'); 
                $resimg=$response2->getDecodedBody();
                $fecha = $ev2["date"];
                $fecha = str_replace("T"," ",$fecha);
                $fecha = substr ($fecha,0,16);
                #https://www.facebook.com/events/1082000648599128
                $respuesta []= new MessageElement($ev2["name"],"[".$fecha."] ".$ev2["description"], $resimg["data"]["url"], [
                                            new MessageButton(MessageButton::TYPE_WEB, 'View',"https://www.facebook.com/events/".$ev2["id"],"compact")                                         
                            ]);
                $noev=$noev + 1;
            }
            
            #$chatbotHelper->send($senderId,"Great!!!");
            $this->send($this->getSenderId(),"I Find de next events:");
            $this->sendMsj(new StructuredMessage($this->getSenderId(),
                    StructuredMessage::TYPE_GENERIC,
                    [
                        'elements' => $respuesta
                    ]                                
            ));                  

        } catch(Facebook\Exceptions\FacebookResponseException $e) {
          file_put_contents("php://stderr", 'Graph returned an error: ' . $e->getMessage());
          exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
          file_put_contents("php://stderr", 'Facebook SDK returned an error: ' . $e->getMessage());
          exit;
        }    
        return $response;
    }

   static  function sortFunction( $a, $b ) {
        #file_put_contents("php://stderr", "sortFunction: ".(strtotime($a["date"])-strtotime($b["date"])));
        return  strtotime($a["date"])-strtotime($b["date"]);
    }
}
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
    public $chatbotAI;
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
                case "show_places":
                        file_put_contents("php://stderr", "show_places");                        
                        $resData = $this->GetPlaces();
                        file_put_contents("php://stderr", $resData);
                        return "";
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
                case "volunteering":
                        return "https://www.sanleandro.org/depts/library/teens/volunteer/default.asp";

                        break;
                case "audiobooks":
                        return "https://sanleandrolibrary.overdrive.com/";

                        break;
                default:
                   /* $Data2 = new Databot();
                    $resData = $Data2->GetLocation($message);
                    if ($resData != "")
                    {
                        return $resData;
                    }else{*/
                    if (!$this->GetPlaces($message))
                    {
                        return "Hmmm, I'm not sure I understand. Can you ask again? Try \"show me events?\" or \"where is the library?\"";                    
                    }
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
        $this->send($this->getSenderId(), "Hi ".$UsrData->getFirstName()."!");
        /*$this->send($this->getSenderId(), "I’m CityBot and I am here to show you all the cool Places, Activities and Events in San Leandro.");
        //$this->send($this->getSenderId(), "Are you ready ?");*/
        $this->facebookSend->send(new StructuredMessage($this->getSenderId(),
          StructuredMessage::TYPE_BUTTON,
          [
              'text' => "I’m BexiCity and I am here to show you all the cool Places, Activities and Events in San Leandro.",
              'buttons' => [
                  new MessageButton(MessageButton::TYPE_POSTBACK, "Continue","CMD_OK")
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
        return "My Name is BexiCity" ;
    }

    public function GetEvents($fechaev, $noquerie = false, $noevent=0){
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
            if (!$noquerie)
            {
                $bus = $this->chatbotAI->getLocalsearchquery();    
            }else{
                $bus = "%";
            }
            $wrong_bus=["go","event","I","events","to do","can","wha can","what","it","no local search query recognized"];
            if (in_array($bus, $wrong_bus))
            {
                $bus="%";
            }
            
            $Pages = $Data->GetPagesId($bus);
            file_put_contents("php://stderr", "Fecha==".$fechaev);   
            if (count($Pages)>0)                
            {
                foreach ($Pages as &$page)
                {
                    $response = $fb->get('/'.$page["fb_id"].'/events?time_filter=upcoming');  
                    file_put_contents("php://stderr", '/'.$page["fb_id"].'/events?time_filter=upcoming');          
                    $resev=$response->getDecodedBody();
                 #   file_put_contents("php://stderr", print_r($resev["data"],true)); 
                    foreach ($resev["data"] as &$ev) {
                        $tmp["id"]=$ev["id"];
                        $tmp["description"]=$ev["description"];
                        $tmp["name"]=$ev["name"];
                        $tmp["lugar"]=$page["nombre"];
                        $tmp["date"]=$ev["start_time"];
                        $eventos[]=$tmp;                                                
                    }
                }
                
                $noev=1;

                $eventos = array_filter($eventos,function ($element) use ($fechaev) { return ($fechaev <= strtotime(substr($element["date"],0,19)));});
                
                usort($eventos, array("DonMarkus\ChatbotHelper","sortFunction"));

                #foreach ($eventos as &$ev2) {
                file_put_contents("php://stderr", "Count == ".count($eventos)." n==".$noevent);
                $evshow=4;
                if ((count($eventos)-($noevent+4))==1)
                {
                    $evshow=3;
                }
                for ($n=$noevent; $n < $noevent + $evshow; $n++)
                {
                    if (count($eventos)<= $n)
                    {
                        file_put_contents("php://stderr", "BREAK FOR");
                        break;
                    }
                    /*if ($noev>=5)
                    {
                        break;
                    }*/
                    $ev2=$eventos[$n];
                    file_put_contents("php://stderr", $ev2["id"].'/picture?redirect=false&type=large');
                    $response2 = $fb->get('/'.$ev2["id"].'/picture?redirect=false&type=large'); 
                    $resimg=$response2->getDecodedBody();
                    $fecha = $ev2["date"];
                    $fecha = str_replace("T"," ",$fecha);
                    $fecha = substr ($fecha,0,16);
                    $fecha2 = substr($fecha,5,2);
                    $fecha2 .= "-".substr($fecha,8,2);
                    $fecha2 .= "-".substr($fecha,0,4);
                    $fecha2 .= " ".substr($fecha,11);
                    #https://www.facebook.com/events/1082000648599128
                 /* $respuesta []= new MessageElement($ev2["name"],"[".$fecha."] ". $ev2["lugar"], $resimg["data"]["url"], [
                                                new MessageButton(MessageButton::TYPE_WEB, 'View',"https://www.facebook.com/events/".$ev2["id"],"compact")                                         
                                ], "https://www.facebook.com/events/".$ev2["id"]);
                    */
                    $respuesta [] = new MessageElement(
                                        $ev2["name"], // title
                                        $ev2["lugar"]." => ".$fecha2." ", // subtitle
                                        $resimg["data"]["url"], // image_url
                                        [ // buttons
                                           new MessageButton(MessageButton::TYPE_WEB, 
                                                'View',
                                                "https://www.facebook.com/events/".$ev2["id"]
                                            )
                                        ]
                                    );
                    $noev=$noev + 1;
                }                                
                $noevent = $n;
                
                #$chatbotHelper->send($senderId,"Great!!!");
                file_put_contents("php://stderr", "Show Events ".$n. " de ".count($eventos));
                $this->send($this->getSenderId(),"I found these events:");
                               
                if (count($eventos)> $noevent)
                {
                    file_put_contents("php://stderr", "Has Mores cmd_more_events".$noevent);
                    $Evpostback = "cmd_more_events|".$noevent."|".$noquerie."|".$fechaev;
                    $this->sendMsj(new StructuredMessage($this->getSenderId(),
                        StructuredMessage::TYPE_LIST,
                        [
                                'elements' => $respuesta,
                                'buttons' => [
                                    new MessageButton(MessageButton::TYPE_POSTBACK, 'View More', $Evpostback)
                                ]
                            ]                               
                    )); 
                }else{
                    $this->sendMsj(new StructuredMessage($this->getSenderId(),
                        StructuredMessage::TYPE_LIST,
                        [
                                'elements' => $respuesta
                        ]                               
                    )); 
                }               
                
            }else{
                $this->send($this->getSenderId(),"No Events found");
            }
                             

        } catch(Facebook\Exceptions\FacebookResponseException $e) {
          file_put_contents("php://stderr", 'Graph returned an error: ' . $e->getMessage());
          exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
          file_put_contents("php://stderr", 'Facebook SDK returned an error: ' . $e->getMessage());
          exit;
        }    
        return $response;
    }

    public function GetActivities()
    {
        
        file_put_contents("php://stderr", "GetActivities");         
        $fb = new \Facebook\Facebook([
          'app_id' => '1347080372047215',
          'app_secret' => '97d6f4ebe503098fb7cfb45577b7c1f9',
          'default_graph_version' => 'v2.10',
          'default_access_token' => '1347080372047215|q-RxSd7MDCZtXP_UOvcP5Bk5Lqw', // optional
        ]);
        try {
            file_put_contents("php://stderr", "Request"); 
            $Data = new Databot();
            $Activities = $Data->GetActivities();
            foreach ($Activities as &$act)
            {
                $response = $fb->get('/'.$act["fb_id"].'/picture?redirect=false&type=large');  
                file_put_contents("php://stderr", '/'.$act["fb_id"].'/picture?redirect=false&type=large');          
                $resev=$response->getDecodedBody();
                file_put_contents("php://stderr", print_r($act,true));                 
                $tmp["id"]=$act["iactd"];                    
                $tmp["name"]=$act["title"];
                $tmp["desc"]=$act["descripcion"];
                $tmp["fb_id"]=$act["fb_id"];
                $tmp["url"]=$resev["data"]["url"];
                $actividades[]=$tmp;                
            }
            
            $noev=1;
            
            foreach ($actividades as &$act2) {
                if ($noev>=10)
                {
                    break;
                }            
                file_put_contents("php://stderr", $act2["name"]."\n".$act2["url"]."\n".$act2["fb_id"]);                            
                $respuesta []= new MessageElement($act2["name"],$act2["desc"], $act2["url"], [
                                            new MessageButton(MessageButton::TYPE_WEB, 'View',"https://www.facebook.com/".$act2["fb_id"],"compact")                                         
                            ],"https://www.facebook.com/".$act2["fb_id"]);
                $noev=$noev + 1;
            }
            
            #$chatbotHelper->send($senderId,"Great!!!");
            $this->send($this->getSenderId(),"I found these Activities:");
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


     public function GetPlaces($busqueda = "%", $noindex=0, $categoria="%"){
        file_put_contents("php://stderr", "GetPlaces");         
        $fb = new \Facebook\Facebook([
          'app_id' => '1347080372047215',
          'app_secret' => '97d6f4ebe503098fb7cfb45577b7c1f9',
          'default_graph_version' => 'v2.10',
          'default_access_token' => '1347080372047215|q-RxSd7MDCZtXP_UOvcP5Bk5Lqw', // optional
        ]);    

        try {
            file_put_contents("php://stderr", "Request"); 
            $Data = new Databot();
            $Pages = $Data->GetPlaces($busqueda,$categoria);
            foreach ($Pages as &$page)
            {
                $response = $fb->get('/'.$page["fb_id"].'/picture?redirect=false&type=large');  
                file_put_contents("php://stderr", '/'.$page["fb_id"].'/picture?redirect=false&type=large');          
                $resev=$response->getDecodedBody();
                //file_put_contents("php://stderr", print_r($page,true));                 
                $tmp["id"]=$page["id"];                    
                //$tmp["name"]=$page["Nombre"];
                $tmp["fb_id"]=$page["fb_id"];
                //$tmp["tel"]=$page["tel"];
                $tmp["url"]=$resev["data"]["url"];

                $response2 = $fb->get('/'.$page["fb_id"].'/?fields=id,name,about,description,hours,location,phone,picture');  
                file_put_contents("php://stderr", '/'.$page["fb_id"].'/?fields=id,name,about,description,hours,location,phone,picture');          
                $pageData=$response2->getDecodedBody();

                $tmp["name"]=$pageData["name"];
                $tmp["tel"]=$pageData["phone"];

                if ($pageData["description"]!="")
                {
                    $tmp["desc"]=$pageData["description"];
                }else{
                    $tmp["desc"]=$pageData["about"];
                }


                $paginas[]=$tmp;                
            }
            
            $noev=1;

            file_put_contents("php://stderr", "Count == ".count($paginas)." n==".$noindex);
            $plshow=4;
            if ((count($paginas)-($noindex+4))==1)
            {
                $plshow=3;
            }
            for ($n=$noindex; $n < $noindex + $plshow; $n++)
            {                
                if (count($paginas)<= $n)
                {
                    file_put_contents("php://stderr", "BREAK FOR");
                    break;
                }
                $pag = $paginas[$n];
            #foreach ($paginas as &$pag) {
                file_put_contents("php://stderr",print_r($pag,true));
                file_put_contents("php://stderr", $pag["name"]."\n".$pag["url"]."\n".$pag["fb_id"]); 
                $botones= [];
                $botones[] = new MessageButton(MessageButton::TYPE_WEB, 'View',"https://www.facebook.com/".$pag["fb_id"]) ;
                if ($pag["tel"]!="")
                {
                    //$botones[] = new MessageButton(MessageButton::TYPE_CALL, 'Call',$pag["tel"],"compact") ;
                }
                file_put_contents("php://stderr", print_r($botones)); 
               # $respuesta []= new MessageElement($pag["name"],"  ", $pag["url"], $botones, "https://www.facebook.com/".$pag["fb_id"]);
                $respuesta [] = new MessageElement(
                                        $pag["name"], // title
                                        $pag["desc"], // subtitle
                                        $pag["url"], // image_url
                                         // buttons
                                        $botones
                                        
                                    );
                $noev=$noev + 1;
            }
            
            $noindex = $n;
            #$chatbotHelper->send($senderId,"Great!!!");
            if (count($paginas)>1)
            {                 
                $this->send($this->getSenderId(),"I found these Places:");
                if (count($paginas)> $noindex)
                {                   
                    $ViewMore='cmd_more_places|'.$noindex."|".$busqueda."|".$categoria;
                    file_put_contents("php://stderr", print_r($respuesta,true));
                    $this->sendMsj(new StructuredMessage($this->getSenderId(),
                            StructuredMessage::TYPE_LIST,
                            [
                                'elements' => $respuesta,
                                'buttons' => [
                                    new MessageButton(MessageButton::TYPE_POSTBACK, 'View More', $ViewMore)
                                ]
                            ]                               
                    ));                 
                }else{
                    file_put_contents("php://stderr", print_r($respuesta,true));
                    $this->sendMsj(new StructuredMessage($this->getSenderId(),
                            StructuredMessage::TYPE_LIST,
                            [
                                'elements' => $respuesta
                            ]                               
                    ));                 
                }
                return true;    
            }else{
                return false;
            }            
            

        } catch(Facebook\Exceptions\FacebookResponseException $e) {
          file_put_contents("php://stderr", 'Graph returned an error: ' . $e->getMessage());
          exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
          file_put_contents("php://stderr", 'Facebook SDK returned an error: ' . $e->getMessage());
          exit;
        }    
        return false;
    }

   static  function sortFunction( $a, $b ) {
        #file_put_contents("php://stderr", "sortFunction: ".(strtotime($a["date"])-strtotime($b["date"])));
        return  strtotime($a["date"])-strtotime($b["date"]);
    }
}
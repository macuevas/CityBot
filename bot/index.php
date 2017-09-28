<?php

use DonMarkus\ChatbotHelper;
use Bexi\DataBot;
use pimax\Messages\StructuredMessage;
use pimax\Messages\MessageButton;
use pimax\Messages\MessageElement;
use pimax\Messages\QuickReply;
use pimax\Messages\QuickReplyButton;


#require_once ("../src/DataBot.php");

require_once __DIR__ . '/../vendor/autoload.php';

// Create the chatbot helper instance
$chatbotHelper = new ChatbotHelper();

// Facebook webhook verification
$chatbotHelper->verifyWebhook($_REQUEST);

// Get the fb users data

$senderId = $chatbotHelper->getSenderId();

file_put_contents("php://stderr", print_r($chatbotHelper->getInputData(),true));

if ($senderId && $chatbotHelper->isMessage()) 
{

    // Get the user's message
    $message = $chatbotHelper->getMessage();



    // Example 1: Get a static message back
    if (substr($message,0,4)=="cmd:")
    {
        file_put_contents("php://stderr", "Message Is a Command\n");
        $comando=substr($message,4);
        file_put_contents("php://stderr", "Command: " . $comando . "\n");
         switch ($comando) {
            case 'getversion':
                $Data = new DataBot();
                $replyMessage =  $Data->GetVersion();
                break;
            case 'testdb':
                $Data = new DataBot();
                $replyMessage =  $Data->TestConection();
                break;
            case "test2":
                            // Send Structured message
                            $chatbotHelper->send($senderId,"Great!!!");
                            $chatbotHelper->send($senderId,"Here are a few tips on how to use me:");
                            $chatbotHelper->sendMsj(new StructuredMessage($senderId,
                                StructuredMessage::TYPE_GENERIC,
                                [
                                    'elements' => [
                                        new MessageElement("Main Menu", "", "https://blooming-spire-13615.herokuapp.com/resources/01_menu.png", [
                                            #new MessageButton(MessageButton::TYPE_POSTBACK, 'First button')                                         
                                        ]),
                                        new MessageElement("Ask Me Anything", "", "https://blooming-spire-13615.herokuapp.com/resources/02_type.png", [
                                        ])
                                    ]
                                ]                                
                            ));                                                   
                break;    
            default:
                return "Hmmm, I'm not sure I understand. Can you ask again? Try \"show me events?\" or \"where is the library?\"";  
                break;
         }
    }elseif($message=="places"){
        $chatbotHelper->GetPlaces();
    }elseif($message=="events"){
        $fecha = strtotime(date("Y-m-dTH:i:sa"));
        file_put_contents("php://stderr", "Eventos fecha=".$fecha);
        $chatbotHelper->GetEvents($fecha,true);

    }elseif($message == "activities")
    {  
        $chatbotHelper->GetActivities();
    }else{
        file_put_contents("php://stderr", "Mensaje=".$message);
        $replyMessage = $chatbotHelper->getAnswer($message,"witai");    
    }

    // Example 2: Get foreign exchange rates
//     $replyMessage = $chatbotHelper->getAnswer($message, 'rates');

    // Example 3: If you want to use a bot platform like api.ai
    // Don't forget to place your Api.ai Client access token in the .env file
//     $replyMessage = $chatbotHelper->getAnswer($message, 'apiai');

    // Example 4: If you want to use a bot platform like wit.ai
    // Don't forget to place your Wit.ai Client access token in the .env file (WITAI_TOKEN)
    // $replyMessage = $chatbotHelper->getAnswer($message, 'witai');

    // Send the answer back to the Facebook chat
    if ($replyMessage!="")
    {
        $chatbotHelper->send($senderId, $replyMessage);
    }

}elseif ($senderId && $chatbotHelper->isPostback()) {
    $payload= $chatbotHelper->getPayload();
    file_put_contents("php://stderr", "PAYLOAD=".$payload);
    switch ($payload) {
            case 'GET_STARTED_PAYLOAD':
            #https://blooming-spire-13615.herokuapp.com/resources/welcome.png
                $chatbotHelper->sendImg($senderId, "https://blooming-spire-13615.herokuapp.com/resources/welcome.png");
                //$chatbotHelper->send($senderId, $chatbotHelper->SayHello());
                $chatbotHelper->SayHello();
            break;
            case 'CMD_OK':
                $chatbotHelper->send($senderId,"Great!!!");
                $chatbotHelper->send($senderId,"Here’s how you can use me:");
                $chatbotHelper->sendMsj(new StructuredMessage($senderId,
                                StructuredMessage::TYPE_GENERIC,
                                [
                                    'elements' => [
                                        new MessageElement("1 - MENU", "Swipe up the Menu bar and you can tap
on one of the three options.", "https://blooming-spire-13615.herokuapp.com/resources/01_menu.png", [
                                            #new MessageButton(MessageButton::TYPE_POSTBACK, 'First button')                                         
                                        ]),
                                        new MessageElement("2 - TEXT", "Type a question on the text field, for a
more specific search.", "https://blooming-spire-13615.herokuapp.com/resources/02_type.png", [
                                        ])
                                    ]
                                ]                                
                ));  

                $chatbotHelper->sendMsj(new StructuredMessage($senderId,
                  StructuredMessage::TYPE_BUTTON,
                  [
                      'text' => 'Are you ready?',
                      'buttons' => [
                          new MessageButton(MessageButton::TYPE_POSTBACK, "Yes, let's do this!","CMD_READY")
                      ]
                  ]
              ));                  
            break;
            case 'CMD_PLACES':
                #$chatbotHelper->send($senderId,"Menu Places"); 
                #$chatbotHelper->sendImg($senderId, "https://blooming-spire-13615.herokuapp.com/resources/03_places.png");
                $chatbotHelper->GetPlaces();
                
            break;
            case 'CMD_EVENTS':
                $fecha = strtotime($chatbotHelper->chatbotAI->getDatetime());
                file_put_contents("php://stderr", "Events=".$fecha);    

                $chatbotHelper->GetEvents($fecha,true);


                /*
                $chatbotHelper->send($senderId,"Menu Events"); 
                $chatbotHelper->sendImg($senderId, "https://blooming-spire-13615.herokuapp.com/resources/04_events.png");
                */
            break;
            case 'CMD_ACTIVITIES':
                #$chatbotHelper->send($senderId,"Menu Activities"); 
                #$chatbotHelper->sendImg($senderId, "https://blooming-spire-13615.herokuapp.com/resources/05_activities.png");
                $chatbotHelper->GetActivities();
            break;
            case 'CMD_READY':
                #$chatbotHelper->send($senderId,"What are you looking for today?"); 
                $chatbotHelper->sendMsj( new StructuredMessage($senderId,
                      StructuredMessage::TYPE_BUTTON,
                      [
                          'text' => 'What are you looking for today?',
                          'buttons' => [
                              new MessageButton(MessageButton::TYPE_POSTBACK, 'Where to go',"CMD_PLACES"),
                              new MessageButton(MessageButton::TYPE_POSTBACK, 'What to do',"CMD_EVENTS"),                              
                          ]
                      ]
                  )); 
            break;
            case "CMD_CAT_CUL":
                file_put_contents("php://stderr", "Places Cultural");
                $chatbotHelper->GetPlaces("%",0,"%Cultural%");
                break;
            case "CMD_CAT_FAM":
                file_put_contents("php://stderr", "Places Kids & Family");
                $chatbotHelper->GetPlaces("%",0,"%Kids%&%Family%");
                break;
            case "CMD_CAT_RECREA":
                file_put_contents("php://stderr", "Recreational");
                $chatbotHelper->GetPlaces("%",0,"%Recreational%");
                break;
            case "CMD_CAT_RESTAURANT":
                file_put_contents("php://stderr", "Restaurants");
                $chatbotHelper->GetPlaces("%",0,"%Restaurants%");
                break;
            case "CMD_CAT_BARS":
                file_put_contents("php://stderr", "Bars");
                $chatbotHelper->GetPlaces("%",0,"%Bars%");
                break;
            case "CMD_EVT_TODAY":
                $fecha = strtotime("Today");                
                file_put_contents("php://stderr", "CMD_EVT_TODAY NEXTWEEK=".date ("d - m - Y ",strtotime("Next Week")) . " view more ". $noevent);  

                $chatbotHelper->GetEvents($fecha,true,0);
                break;
            case "CMD_EVT_TOMO":
                $fecha = strtotime("Tomorrow");
                file_put_contents("php://stderr", "CMD_EVT_TOMO Events=".$fecha . " view more ". $noevent);    

                $chatbotHelper->GetEvents($fecha,true,0);
                break;
            case "CMD_EVT_TW":
                $fecha = strtotime("This Week");
                file_put_contents("php://stderr", "CMD_EVT_TODAY Events=".date ("d - m - Y ",$fecha) . " view more ". $noevent);
                $chatbotHelper->GetEvents($fecha,true,0);
                break;
             case "CMD_EVT_NW":
                $fecha = strtotime("Next Week");
                file_put_contents("php://stderr", "CMD_EVT_TODAY Events=".date ("d - m - Y ",$fecha) . " view more ". $noevent);
                $chatbotHelper->GetEvents($fecha,true,0);
                break;
            default:
                file_put_contents("php://stderr", "payload=".substr($payload,0,16));
                if (substr($payload,0,16) == "cmd_more_events")
                {
                    $data=explode("|", $payload);                    
                    $noevent = $data[1];
                    $busq = $data[2];
                    $fecha = $data[3];
                    file_put_contents("php://stderr", "Events=".$fecha . " view more ". $noevent);    

                    $chatbotHelper->GetEvents($fecha,$busq,$noevent);

                }elseif (substr($payload,0,15) == "cmd_more_places")
                {
                    $data=explode("|", $payload);

                    $noindex= $data[1];
                    $busq=$data[2];
                    $cat=$data[3];
                    file_put_contents("php://stderr", "Places view more Index=". $noindex." Busqueda=".$busq." Categoria=".$cat);

                    $chatbotHelper->GetPlaces($busq,$noindex,$cat);
                    
                }


            break;

    }
}

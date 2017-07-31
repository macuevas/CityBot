<?php

use DonMarkus\ChatbotHelper;
use Bexi\DataBot;

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
            default:
                return "Hmmm, I'm not sure I understand. Can you ask again? Try \"What's the weather like?\" or \"What time is it?\"";
                break;
         }
    }else{
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
                $chatbotHelper->send($senderId,"Here are a few tips on how to use me:");
                $chatbotHelper->send($senderId,"Main Menu");
                $chatbotHelper->sendImg($senderId, "https://blooming-spire-13615.herokuapp.com/resources/01_menu.png");
                $chatbotHelper->send($senderId,"Ask Me Anything");                
                $chatbotHelper->sendImg($senderId, "https://blooming-spire-13615.herokuapp.com/resources/02_type.png");                
            break;
            case 'CMD_PLACES':
                 $chatbotHelper->sendImg($senderId, "https://blooming-spire-13615.herokuapp.com/resources/03_places.png");
            break;
            case 'CMD_EVENTS':
                 $chatbotHelper->sendImg($senderId, "https://blooming-spire-13615.herokuapp.com/resources/04_events.png");
            break;
            case 'CMD_ACTIVITIES':
                 $chatbotHelper->sendImg($senderId, "https://blooming-spire-13615.herokuapp.com/resources/05_activities.png");
            break;

    }
}

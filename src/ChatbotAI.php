<?php

namespace DonMarkus;


use ApiAi\Client;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;


class ChatbotAI
{

    protected $apiClient;
    protected $config;
    protected $foreignExchangeRate;
    protected $witClient;
    protected $LastResponse;
    /**
     * ChatbotAI constructor.
     * @param $config
     * @throws \Exception
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->log = new Logger('general');
        $this->log->pushHandler(new StreamHandler('debug.log'));
        $this->apiClient = new Client($this->config['apiai_token'], null, $this->config['apiai_bot_language']);
        $this->witClient = new \Tgallice\Wit\Client($this->config['witai_token']);
        $this->foreignExchangeRate = new ForeignExchangeRate();
    }

    /**
     * Get the answer to the user's message
     * @param $message
     * @return string
     */
    public function getAnswer(string $message)
    {
        // Simple example returning the user's message
        return 'Define your own logic to reply to this message: ' . $message;

        // Do whatever you like to analyze the message
        // Example:
        // if(preg_match('[hi|hey|hello]', strtolower($message))) {
        // return 'Hi, nice to meet you!';
        // }
    }

    /**
     * Get the answer to the user's message with help from api.ai
     * @param string $message
     * @return string
     */
    public function getApiAIAnswer($message)
    {
        try {

            $query = $this->apiClient->get('query', [
                'query' => $message,
            ]);

            $response = json_decode((string)$query->getBody(), true);

            return $response['result']['fulfillment']['speech'];
        } catch (\Exception $error) {
            $this->log->warning($error->getMessage());
        }
    }

    /**
     * Get the answer to the user's message with help from wit.ai
     * @param $message
     * @return string
     */
    public function getWitAIAnswer($message)
    {
        $intent = '';
        try {

            $response = $this->witClient->get('/message', [
                'q' => $message,
            ]);

            // Get the decoded body
            $response = json_decode((string)$response->getBody(), true);
            $intent = $response['entities']['intent'][0]['value'] ?? 'no intent recognized';
            $this->LastResponse = $response;
            file_put_contents("php://stderr",print_r($response,true)."\n");
        } catch (\Exception $error) {
            $this->log->warning($error->getMessage());
        }

        return  $intent;
    }


    public function getIntent($message)
    {
        $intent = '';
        try {

            $response = $this->witClient->get('/message', [
                'q' => $message,
            ]);

            // Get the decoded body
            $response = json_decode((string)$response->getBody(), true);
            $this->LastResponse = $response;
            $intent = $response['entities']['intent'][0]['value'] ?? 'no intent recognized';
        } catch (\Exception $error) {
            $this->log->warning($error->getMessage());
        }

        return $intent;
    }

    public function getLocalsearchquery()
    {
        $intent = '';
        try {
            
            $Localsq = $this->LastResponse['entities']['local_search_query'][0]['value'] ?? 'no local search query recognized';
        } catch (\Exception $error) {
            $this->log->warning($error->getMessage());
        }

        return $Localsq;
    }

    /**
     * Get the foreign rates based on the users base (EUR, USD...)
     * @param $message
     * @return string
     */
    public function getForeignExchangeRateAnswer($message)
    {
        return $this->foreignExchangeRate->getRates($message);
    }


}
<?php

namespace App\Service;

use MessageBird\Client;
use MessageBird\Objects\Message;


class MessageService
{
    private string $accessKey;
    public function __construct(string $type, bool $mode)
    {
        $this->init($type, $mode);
    }

    public function init(string $type, bool $isProduction){
        if($type == "SMS" ){
            if($isProduction) {
                $this->accessKey = "BL3tsEI3LlBM7V25Kr5AxwkVJ"; // MESSAGE BIRD ACCESS KEY
            }else{
                $this->accessKey = "cYOrEXgOvgtIA2GEVNIoNNhDU"; // MESSAGE BIRD ACCESS TEST KEY
            }
        }
    }

    public function sendOneSMS(string $message, string $ssid, string $number){
        $MessageBird = new Client($this->accessKey);
        $Message = new Message();
        $Message->originator = $ssid;
        $Message->recipients = $this->transformNumbers($number);
        $Message->body = $message;
        $MessageBird->messages->create($Message);
    }

    public function sendManySMS(string $message, string $ssid, array $listOfNumber){
        $MessageBird = new Client($this->accessKey);
        $Message = new Message();
        $Message->originator = "TestMessage";
        $Message->recipients = $this->transformNumbers($listOfNumber);
        $Message->body = $message;
        return $MessageBird->messages->create($Message);
    }

    public function transformNumbers(string|array $numbers): array
    {
        if(gettype($numbers) == 'string'){
            $phones = [$numbers];
        }else {
            $phones = $numbers;
        }
        foreach ($phones as $index => $phone){
            if(strpos($phones[$index],"+") or strlen($phones[$index]) > 10){
                continue;
            }
            $number = substr($phones[$index], 1); // supprime le 0
            $number  = '+243' . $number;
            $phones[$index] = $number;
        }
        return $phones;
    }

}

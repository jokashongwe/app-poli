<?php

namespace App\Service;

use MessageBird\Client;
use MessageBird\Objects\Message;


class MessageService
{
    private string $accessKey;
    public function __construct($token)
    {
        $this->accessKey = $token;
    }

    public function sendOneWhatsappMesssage(string $phone, $message, $key)
    {
        $post_body = [
            "from" => '14157386102',
            'to' => $phone,
            "message_type" => "text",
            "text" => $message,
            "channel" => "whatsapp"
        ];
        return $this->send_message(json_encode($post_body), 'https://messages-sandbox.nexmo.com/v1/messages', $key);
    }

    public function getCredits()
    {
        $token = $this->accessKey;
        $url = "https://api.bulksms.com/v1/profile?auto-unicode=true&longMessageMaxParts=30";
        $ch = curl_init();
        $headers = array(
            'Content-Type:application/json',
            'Authorization: Basic ' . $token
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $output = array();
        $output['server_response'] = curl_exec($ch);
        $curl_info = curl_getinfo($ch);
        $output['http_status'] = $curl_info['http_code'];
        $output['error'] = curl_error($ch);
        curl_close($ch);
        return $output;
    }

    public function sendManySMS(string $message, array $listOfNumber, string $senderID, string $senderMode)
    {
        $messages = [];
        foreach ($listOfNumber as $phone) {
            array_push($messages, [
                'from' => [
                    'type' => 'ALPHANUMERIC',
                    'address' => $senderID,
                ], 'longMessageMaxParts' => 99, 'to' => $phone, 'body' => $message, 'routingGroup' => $senderMode
            ]);
        }

        return $this->send_message(json_encode($messages), 'https://api.bulksms.com/v1/messages?auto-unicode=true&longMessageMaxParts=30', $this->accessKey);
    }

    function send_message($post_body, $url, $token)
    {
        $ch = curl_init();
        $headers = array(
            'Content-Type:application/json',
            'Authorization: Basic ' . $token
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $output = array();
        $output['server_response'] = curl_exec($ch);
        $curl_info = curl_getinfo($ch);
        $output['http_status'] = $curl_info['http_code'];
        $output['error'] = curl_error($ch);
        curl_close($ch);
        return $output;
    }

    public function transformNumbers(string|array $numbers): array
    {
        $phones =  [];
        if (gettype($numbers) == 'string') {
            $phones = [$numbers];
        } else {
            $phones = $numbers;
        }
        foreach ($phones as $index => $phone) {
            if (strpos($phone, "+") or strlen($phone) > 10) {
                continue;
            }
            $number = substr($phone, 1); // supprime le 0
            $number  = '+243' . $number;
            $phones[$index] = $number;
        }
        return $phones;
    }
}

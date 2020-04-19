<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Alexa\Http\Client;
use Alexa\Service\CoronaDataAggregator;
use Alexa\Controller\IntentController;

$client     = new Client();
$aggregator = new CoronaDataAggregator($client);
$controller = new IntentController($aggregator);

header ('Content-Type: application/json');

$result = $controller->index();
//
//$result2 = json_encode([
//    'version' => '1.0',
//    'response' => [
//        'outputSpeech' => [
//            'type' => 'PlainText',
//            'text' => 'hallo'
//        ]
//    ]
//]);
//
//echo $result2;

//file_put_contents('test.txt', $result . PHP_EOL . PHP_EOL, FILE_APPEND);
//file_put_contents('test.txt', $result2 . PHP_EOL . PHP_EOL, FILE_APPEND);
//
//
//exit(0);


echo $result;

exit(0);
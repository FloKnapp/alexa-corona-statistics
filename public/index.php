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

file_put_contents('test.txt', $result . PHP_EOL . PHP_EOL, FILE_APPEND);

echo json_encode($result);

exit(0);
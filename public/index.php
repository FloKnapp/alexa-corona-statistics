<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../vendor/autoload.php';

use Alexa\Http\Client;
use Alexa\Entity\AlexaRequest;
use Alexa\Service\CoronaDataAggregator;
use Alexa\Controller\IntentController;

$client = new Client();

if ($_SERVER['REQUEST_URI'] === '/collect') {

    $collector = new Alexa\Cronjob\Collector($client);
    $collector->run();

    exit(0);
}

header('Accept: application/json');

$requestBody = file_get_contents('php://input');

$request    = new AlexaRequest($requestBody);
$aggregator = new CoronaDataAggregator($client);
$controller = new IntentController($request, $aggregator);

header ('Content-Type: application/json');

echo $controller->index();

exit(0);

<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Alexa\Http\Client;
use Alexa\Service\CoronaDataAggregator;
use Alexa\Controller\IntentController;

$client     = new Client();
$aggregator = new CoronaDataAggregator($client);
$controller = new IntentController($aggregator);

echo $controller->index();

exit(0);
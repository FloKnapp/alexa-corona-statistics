<?php

namespace Alexa\Controller;

use Alexa\Entity\AlexaOutputSpeech;
use Alexa\Entity\AlexaResponse;
use Alexa\Service\CoronaDataAggregator;

class IntentController
{

    private $aggregator;

    /**
     * IntentController constructor.
     * @param CoronaDataAggregator $aggregator
     */
    public function __construct(CoronaDataAggregator $aggregator)
    {
        $this->aggregator = $aggregator;
    }

    /**
     *
     */
    public function index()
    {
        header('Content-Type: application/json');

        $output = new AlexaOutputSpeech('test');
        $response = new AlexaResponse($output);

        return json_encode($response);
    }

}
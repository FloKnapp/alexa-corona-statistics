<?php

namespace Alexa\Controller;

use Alexa\Entity\AlexaOutputSpeech;
use Alexa\Entity\AlexaResponse;
use Alexa\Service\CoronaDataAggregator;

/**
 * Class IntentController
 * @package Alexa\Controller
 */
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
     * @return AlexaResponse
     */
    public function index()
    {
        $output = new AlexaOutputSpeech('Hallo');
        return new AlexaResponse($output);
    }

}
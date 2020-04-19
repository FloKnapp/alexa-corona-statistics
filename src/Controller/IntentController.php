<?php

namespace Alexa\Controller;

use Alexa\Entity\AlexaOutputSpeech;
use Alexa\Entity\AlexaRequest;
use Alexa\Entity\AlexaResponse;
use Alexa\Service\CoronaDataAggregator;

/**
 * Class IntentController
 * @package Alexa\Controller
 */
class IntentController
{

    /** @var AlexaRequest */
    private $request;

    /** @var CoronaDataAggregator */
    private $aggregator;

    /**
     * IntentController constructor.
     * @param AlexaRequest $request
     * @param CoronaDataAggregator $aggregator
     */
    public function __construct(AlexaRequest $request, CoronaDataAggregator $aggregator)
    {
        $this->request    = $request;
        $this->aggregator = $aggregator;
    }

    /**
     * @return string
     */
    public function index()
    {

        $country = $this->request->getIntent()->getSlots()[0]->getValue();

        $output = new AlexaOutputSpeech($country);
        return json_encode(new AlexaResponse($output));
    }



}
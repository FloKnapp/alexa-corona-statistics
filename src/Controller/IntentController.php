<?php

namespace Alexa\Controller;

use Alexa\Entity\AlexaOutputSpeech;
use Alexa\Entity\AlexaRequest;
use Alexa\Entity\AlexaRequestSlot;
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
        $slots = $this->request->getIntent()->getSlots();

        if (isset($slots[0]) && $slots[0] instanceof AlexaRequestSlot) {
            $result = $this->aggregator->getCurrentCasesByCountry($slots[0]->getValue());
        } else {
            $result = $this->aggregator->getCurrentCases();
        }

        $output = new AlexaOutputSpeech($result);
        return json_encode(new AlexaResponse($output));
    }



}
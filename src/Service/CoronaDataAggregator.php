<?php

namespace Alexa\Service;

use Alexa\Http\Client;

/**
 * Class CoronaDataAggregator
 * @package Alexa\Service
 */
class CoronaDataAggregator
{

    /** @var Client */
    private $client;

    /**
     * CoronaDataAggregator constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getCurrentCases()
    {

    }

    public function getCurrentCasesByCountry($country = 'de')
    {

    }

}
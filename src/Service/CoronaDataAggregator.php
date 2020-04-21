<?php

namespace Alexa\Service;

use Alexa\Http\Client;
use ParseCsv\Csv;

/**
 * Class CoronaDataAggregator
 * @package Alexa\Service
 */
class CoronaDataAggregator
{

    /** @var Client */
    private $client;

    /** @var string */
    private $cacheDir = __DIR__ . '/../../cache';

    /**
     * CoronaDataAggregator constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Returns overall cases worldwide
     *
     * @return string
     */
    public function getCurrentCases()
    {
        $amounts = $this->getAggregatedAmount();

        $germanDate = $this->convertDate($amounts['date']);

        $activeCases = $amounts['confirmed'] - $amounts['recovered'] - $amounts['deaths'];

        $output = 'Am ' . $germanDate . ' gab es weltweit ' . $amounts['confirmed'] . ' bestätigte Infektionen. Davon sind gestorben: ' . $amounts['deaths'] . '. Davon sind geheilt: ' . $amounts['recovered'] . '. Das bedeutet, dass es aktuell noch ' . $activeCases . ' aktive Infektionen gibt.';

        return $output;

    }

    /**
     * Return overall cases by country
     *
     * @param string $country
     * @return string
     */
    public function getCurrentCasesByCountry(string $country = 'de')
    {
        return 'Deutschland';
    }

    /**
     * @param string $date
     * @return array
     *
     * @throws \Exception
     */
    private function getAggregatedAmount(string $date = '')
    {
        $fields = ['confirmed', 'deaths', 'recovered'];

        $stats = $this->getLatestStatistics($date);

        $counts = [
            'date'      => $stats['date'],
            'countries' => 0,
            'confirmed' => 0,
            'deaths'    => 0,
            'recovered' => 0
        ];

        foreach ($stats['csv']->data as $row) {

            foreach ($fields as $field) {
                $counts[$field] = $counts[$field] + (int)$row[ucfirst($field)];
            }

        }

        $counts['countries'] = count($this->getAffectedCountries());

        return $counts;
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    private function getAffectedCountries()
    {
        $stats = $this->getLatestStatistics();

        $result = [];

        foreach ($stats['csv']->data as $data) {
            $column = $data['Country_Region'] ?? $data['Country/Region'];
            $result[$column] = $column;
        }

        return array_values($result);
    }

    /**
     * @param $date
     * @return array
     *
     * @throws \Exception
     */
    private function getLatestStatistics(string $date = '')
    {
        if (empty($date)) {
            $date = (new \DateTime())->format('m-d-Y');
        }

        // In case of non existing statistics try the day before
        if (!file_exists($this->cacheDir . '/' . $date . '.csv')) {
            $reformattedDate = $this->convertDate($date);
            $dayBefore = (new \DateTime($reformattedDate))->modify('-1 days')->format('m-d-Y');
            return $this->getLatestStatistics($dayBefore);
        }

        $csv = new Csv();
        $csv->auto($this->cacheDir . '/' . $date . '.csv');

        return [
            'csv' => $csv,
            'date' => $date
        ];
    }

    /**
     * @param string $date
     * @return string
     */
    private function convertDate(string $date)
    {
        $parts = explode('-', $date);
        return implode('.', [$parts[1], $parts[0], $parts[2]]);
    }

}
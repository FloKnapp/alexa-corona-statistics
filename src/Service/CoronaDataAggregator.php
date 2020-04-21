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
        $amounts    = $this->getAggregatedAmount();
        $germanDate = $this->convertDateFromIsoToGerman($amounts['date']);

        $dayBeforeDate    = (new \DateTime($germanDate))->modify('-1 days')->format('m-d-Y');
        $amountsDayBefore = $this->getAggregatedAmount($dayBeforeDate);

        $activeCases       = $amounts['confirmed'] - $amounts['recovered'] - $amounts['deaths'];
        $activeCasesBefore = $amountsDayBefore['confirmed'] - $amountsDayBefore['recovered'] - $amountsDayBefore['deaths'];
        $activeCasesBefore = $activeCases - $activeCasesBefore;

        $confirmed  = $amounts['confirmed'];
        $deaths     = $amounts['deaths'];
        $recovered  = $amounts['recovered'];

        $confirmedDayBefore = $confirmed - $amountsDayBefore['confirmed'];
        $deathsDayBefore    = $deaths - $amountsDayBefore['deaths'];
        $recoveredDayBefore = $recovered - $amountsDayBefore['recovered'];

        $output = <<<HTML
Am {$germanDate} gab es weltweit {$confirmed} bestätigte Infektionen. Das sind {$confirmedDayBefore} mehr als gestern. 
Davon sind gestorben: {$amounts['deaths']}. Das sind {$deathsDayBefore} mehr als gestern. 
Davon sind geheilt: {$amounts['recovered']}. Das sind {$recoveredDayBefore} mehr als gestern. 
Das bedeutet, dass es aktuell noch {$activeCases} aktive Infektionen gibt, das sind {$activeCasesBefore} mehr als gestern. 
Insgesamt sind derzeit {$amounts['countries']} Länder betroffen.';
HTML;

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
            'recovered' => 0,
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
     * @param $date ISO-Date
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
            $reformattedDate = $this->convertDateFromIsoToGerman($date);
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
    private function convertDateFromIsoToGerman(string $date)
    {
        $parts = explode('-', $date);
        return implode('.', [$parts[1], $parts[0], $parts[2]]);
    }

    /**
     * @param string $date
     * @return string
     */
    private function convertDateFromGermanToIso(string $date)
    {
        $parts = explode('.', $date);
        return implode('-', [$parts[1], $parts[0], $parts[2]]);
    }

}
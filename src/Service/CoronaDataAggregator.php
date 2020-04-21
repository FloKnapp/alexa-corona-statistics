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

    private $finalSentences = [
        'Ich hoffe Du machst Dir heute einen schönen Tag zuhause.',
        'Bitte vergiss nicht Deinen Atemschutz mitzunehmen, wenn Du hinaus musst.',
        'Denke daran mindestens 2 Meter Abstand zu Deinen Mitmenschen zu halten, wenn Du hinaus musst.'
    ];

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

        shuffle($this->finalSentences);

        $output = <<<HTML
Am {$germanDate} gab es weltweit {$confirmed} bestätigte Infektionen. 
Das sind {$this->formatWording($confirmedDayBefore)} als gestern.
Davon sind gestorben: {$amounts['deaths']}. 
Das sind {$this->formatWording($deathsDayBefore)} als gestern.
Davon sind geheilt: {$amounts['recovered']}. 
Das sind {$this->formatWording($recoveredDayBefore)} als gestern.
Das bedeutet, dass es aktuell noch {$activeCases} aktive Infektionen gibt. 
Das sind {$this->formatWording($activeCasesBefore)} als gestern.
Insgesamt sind derzeit {$amounts['countries']} Länder betroffen. {$this->finalSentences[0]}
HTML;

        return $output;

    }

    /**
     * Return overall cases by country
     *
     * @param string $country
     * @return string
     */
    public function getCurrentCasesByCountry(string $country)
    {
        $amounts = $this->getAggregatedAmount('', $country);

        $germanDate = $this->convertDateFromIsoToGerman($amounts['date']);

        $dayBeforeDate    = (new \DateTime($germanDate))->modify('-1 days')->format('m-d-Y');
        $amountsDayBefore = $this->getAggregatedAmount($dayBeforeDate, $country);

        $activeCases       = $amounts['confirmed'] - $amounts['recovered'] - $amounts['deaths'];
        $activeCasesBefore = $amountsDayBefore['confirmed'] - $amountsDayBefore['recovered'] - $amountsDayBefore['deaths'];
        $activeCasesBefore = $activeCases - $activeCasesBefore;

        $confirmed  = $amounts['confirmed'];
        $deaths     = $amounts['deaths'];
        $recovered  = $amounts['recovered'];

        $confirmedDayBefore = $confirmed - $amountsDayBefore['confirmed'];
        $deathsDayBefore    = $deaths - $amountsDayBefore['deaths'];
        $recoveredDayBefore = $recovered - $amountsDayBefore['recovered'];

        shuffle($this->finalSentences);

        $output = <<<HTML
<voice name="Matthew"><say-as interpret-as="digits">Am {$germanDate} gab es in {$country} {$confirmed} bestätigte Infektionen.</say-as></voice>
Das sind {$this->formatWording($confirmedDayBefore)} als gestern. 
Davon sind gestorben: {$amounts['deaths']}. 
Das sind {$this->formatWording($deathsDayBefore)} als gestern. 
Davon sind geheilt: {$amounts['recovered']}. 
Das sind {$this->formatWording($recoveredDayBefore)} als gestern. 
Das bedeutet, dass es aktuell noch {$activeCases} aktive Infektionen gibt. 
Das sind {$this->formatWording($activeCasesBefore)} als gestern. {$this->finalSentences[0]}
HTML;

        return $output;
    }

    private function formatWording($count)
    {
        if ($count < 0) {
            return abs($count) . ' weniger';
        }

        return $count . ' mehr';
    }

    private function calculateCases($counts)
    {

    }

    /**
     * @param string $date
     * @param string $country
     * @return array
     *
     * @throws \Exception
     */
    private function getAggregatedAmount(string $date = '', $country = '')
    {
        $fields = ['confirmed', 'deaths', 'recovered'];

        $stats = $this->getLatestStatistics($date);

        $counts = [
            'date'      => $stats['date'],
            'countries' => 0,
            'confirmed' => 0,
            'deaths'    => 0,
            'recovered' => 0,
            'byCountry' => []
        ];

        $countsByCountry = [
            'date'      => $stats['date'],
            'confirmed' => 0,
            'deaths'    => 0,
            'recovered' => 0
        ];

        foreach ($stats['csv']->data as $row) {

            foreach ($fields as $field) {

                $counts[$field] = $counts[$field] + (int)$row[ucfirst($field)];
                $countryColumnValue = $row['Country_Region'] ?? $row['Country/Region'];

                if (empty($counts['byCountry'][$countryColumnValue])) {
                    $counts['byCountry'][$countryColumnValue] = $countsByCountry;
                }

                $counts['byCountry'][$countryColumnValue][$field] += (int)$row[ucfirst($field)];

            }

        }

        if (!empty($country)) {
            return $counts['byCountry'][$country];
        }

        $counts['countries'] = count($counts['byCountry']);

        return $counts;
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
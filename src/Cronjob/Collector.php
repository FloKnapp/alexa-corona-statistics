<?php

namespace Alexa\Cronjob;

use Alexa\Http\Client;
use Alexa\Http\ClientResponseException;

/**
 * Class Collector
 * @package Alexa\Cronjob
 */
class Collector
{

    /** @var Client */
    private $client;

    /** @var string */
    private $cacheDir = __DIR__ . '/../../cache';

    /** @var string */
    private $dataSourceUrl = 'https://api.github.com/repos/CSSEGISandData/COVID-19/contents/csse_covid_19_data/csse_covid_19_daily_reports';

    /** @var array */
    private $ignoreFiles = [
        '.gitignore',
        'README.md'
    ];

    /**
     * Collector constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @throws ClientResponseException
     */
    public function run()
    {
        $response = $this->client->request('GET', $this->dataSourceUrl);
        $csvFiles = $this->getCsvFiles($response);

        foreach ($csvFiles as $filename => $url) {
            $this->saveFile($filename, $url);
        }

        return true;
    }

    /**
     * @param array $data
     * @return array
     */
    private function getCsvFiles(array $data)
    {
        $result = [];

        foreach ($data as $item) {

            if (in_array($item['name'], $this->ignoreFiles)) {
                continue;
            }

            $result[$item['name']] = $item['download_url'];

        }

        return $result;
    }

    /**
     * @param string $filename
     * @param string $url
     * @return false|int|null
     * @throws ClientResponseException
     */
    private function saveFile(string $filename, string $url)
    {
        if (file_exists($this->cacheDir . '/' . $filename)) {
            return null;
        }

        $response = $this->client->request('GET', $url);

        return file_put_contents($this->cacheDir . '/' . $filename, $response);
    }

}
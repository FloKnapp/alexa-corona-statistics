<?php

namespace Alexa\Http;

/**
 * Class Client
 * @package Alexa\Http
 */
class Client
{

    /** @var array */
    private $responseHeaders = [];

    /**
     * @param string $method
     * @param string $uri
     * @param array  $headers
     * @param array  $data
     *
     * @return array|string
     *
     * @throws ClientResponseException
     */
    public function request(string $method, string $uri, array $headers = [], array $data = [])
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $uri);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, [$this, 'handleResponseHeaders']);

        $headers['User-Agent'] = 'Mozilla/5.0 (X11; Linux i686; rv:75.0) Gecko/20100101 Firefox/75.0';

        $formattedHeaders = $this->formatHeaders($headers);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $formattedHeaders);

        switch ($method) {

            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            default:
                break;

        }

        $response = curl_exec($ch);

        $responseContentType = $this->getResponseHeader('content-type');

        if ((is_array($responseContentType) && in_array('application/json', $responseContentType)) || $responseContentType === 'application/json') {
            $response = json_decode($response, true);
        }

        $responseInfo = curl_getinfo($ch);

        $statusCode = $responseInfo['http_code'];

        if ($statusCode !== 200) {
            throw new ClientResponseException($response, $statusCode);
        }

        // Reset response headers due to the mechanism of collecting them
        $this->responseHeaders = [];

        return $response;

    }

    /**
     * @param $name
     * @return mixed|null
     */
    private function getResponseHeader($name)
    {
        foreach ($this->responseHeaders as $field => $value) {
            if (strtolower($name) === strtolower($field)) {
                return $value;
            }
        }

        return null;
    }

    /**
     * @param $ch
     * @param $headerLine
     * @return int
     */
    private function handleResponseHeaders($ch, $headerLine)
    {
        if (strpos($headerLine, ':') === false) {
            return strlen($headerLine);
        }

        $cleanedHeaderLine = trim($headerLine);

        $segments = explode(':', $cleanedHeaderLine);

        list($field, $value) = $segments;

        $valueSegments = explode(';' , str_replace(' ', '', $value));

        $this->responseHeaders[$field] = $valueSegments;

        return strlen($headerLine);
    }

    /**
     * @param array $headers
     * @return array
     */
    private function formatHeaders(array $headers)
    {
        $result = [];

        foreach ($headers as $field => $value) {
            $result[] = $field . ': ' . $value;
        }

        return $result;
    }

}
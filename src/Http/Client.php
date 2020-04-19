<?php


namespace Alexa\Http;


class Client
{

    /**
     * @param string $method
     * @param string $uri
     * @param array  $headers
     * @param array  $data
     */
    private static function request(string $method, string $uri, array $headers, array $data)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

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

        var_dump($response);

    }

}
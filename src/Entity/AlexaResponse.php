<?php


namespace Alexa\Entity;


class AlexaResponse implements \JsonSerializable
{

    /** @var AlexaOutputSpeech */
    private $output;

    /** @var string */
    private $version;

    /**
     * AlexaResponse constructor.
     * @param $output
     * @param $version
     */
    public function __construct(AlexaOutputSpeech $output, string $version = '1.0')
    {
        $this->output   = $output;
        $this->version  = $version;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'version'  => $this->version,
            'response' => $this->output
        ];
    }

}
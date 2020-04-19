<?php

namespace Alexa\Entity;

/**
 * Class AlexaOutputSpeech
 * @package Alexa\Entity
 */
class AlexaOutputSpeech implements \JsonSerializable
{

    /** @var string */
    private $text;

    /** @var string */
    private $type;

    /**
     * AlexaOutputSpeech constructor.
     * @param string $text
     * @param string $type
     */
    public function __construct(string $text, $type = 'PlainText')
    {
        $this->text = $text;
        $this->type = $type;
    }

    public function jsonSerialize()
    {
        return json_encode(
            [
                'outputSpeech' => [
                    'text' => $this->text,
                    'type' => $this->type
                ]
            ]
        );
    }

}
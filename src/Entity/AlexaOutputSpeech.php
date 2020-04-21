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
    public function __construct(string $text, $type = 'SSML')
    {
        $this->text = $text;
        $this->type = $type;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $textKey = ($this->type === 'SSML') ? 'ssml' : 'text';

        return [
            'outputSpeech' => [
                $textKey => $this->text,
                'type'   => $this->type
            ]
        ];
    }

}
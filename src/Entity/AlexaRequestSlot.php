<?php

namespace Alexa\Entity;

/**
 * Class AlexaRequestSlot
 * @package Alexa\Entity
 */
class AlexaRequestSlot
{

    /** @var string */
    private $name;

    /** @var string */
    private $value;

    /** @var string */
    private $confirmationStatus;

    /** @var string */
    private $source;

    /** @var AlexaResolution[] */
    private $resolutions;

    /**
     * AlexaRequestSlot constructor.
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->fill($data);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getValue():? string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getConfirmationStatus(): string
    {
        return $this->confirmationStatus;
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @return AlexaResolution[]
     */
    public function getResolutions(): array
    {
        return $this->resolutions;
    }

    /**
     * @param $data
     */
    private function fill($data)
    {
        foreach ($data as $key => $value) {
            if (!is_array($value)) {
                $this->$key = $value;
            } else if (array_key_exists('resolutionsPerAuthority', $value)) {
                $this->resolutions = array_map(function($data) {
                    return new AlexaResolution($data);
                }, $value['resolutionsPerAuthority']);
            }

        }
    }

}
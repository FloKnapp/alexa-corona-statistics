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

    private function fill($data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

}
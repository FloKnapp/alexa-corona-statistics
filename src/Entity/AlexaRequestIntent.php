<?php

namespace Alexa\Entity;

/**
 * Class AlexaRequestIntent
 * @package Alexa\Entity
 */
class AlexaRequestIntent
{

    /** @var string */
    private $name;

    /** @var string */
    private $confirmationStatus;

    /** @var AlexaRequestSlot[] */
    private $slots;

    /**
     * AlexaRequestIntent constructor.
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
    public function getConfirmationStatus(): string
    {
        return $this->confirmationStatus;
    }

    /**
     * @return AlexaRequestSlot[]
     */
    public function getSlots(): array
    {
        return $this->slots;
    }

    /**
     * @param $data
     */
    private function fill($data)
    {
        foreach ($data as $key => $value) {

            if (!is_array($value)) {
                $this->$key = $value;
            } else if ($key === 'slots') {
                foreach ($value as $slot) {
                    $this->slots[] = new AlexaRequestSlot($slot);
                }
            }

        }

    }

}
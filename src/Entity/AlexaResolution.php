<?php

namespace Alexa\Entity;

/**
 * Class AlexaResolution
 * @package Alexa\Entity
 */
class AlexaResolution
{

    /** @var string */
    private $authority;

    /** @var string */
    private $status;

    /** @var  */
    private $value;

    /**
     * AlexaResolution constructor.
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->fill($data);
    }

    /**
     * @return string
     */
    public function getAuthority(): string
    {
        return $this->authority;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param $data
     */
    private function fill($data)
    {
        foreach ($data as $key => $value) {

            if (!is_array($value)) {
                $this->$key = $value;
            } else if ($key === 'values') {
                $this->value = $value[0]['value']['name'];
            }

        }

    }

}
<?php


namespace Alexa\Entity;

class AlexaRequest
{

    /** @var string */
    private $type;

    /** @var string */
    private $requestId;

    /** @var string */
    private $timestamp;

    /** @var string */
    private $locale;

    /** @var AlexaRequestIntent */
    private $intent;


    /**
     * AlexaRequest constructor.
     * @param string $data
     */
    public function __construct(string $data = '')
    {
        $this->fill(json_decode($data, true));
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getRequestId(): string
    {
        return $this->requestId;
    }

    /**
     * @return string
     */
    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @return AlexaRequestIntent
     */
    public function getIntent(): AlexaRequestIntent
    {
        return $this->intent;
    }

    private function fill($data)
    {
        foreach ($data['request'] as $key => $value) {

            if (!is_array($value)) {
                $this->$key = $value;
            } else if ($key === 'intent') {
                $this->intent = new AlexaRequestIntent($value);
            }

        }
    }

}
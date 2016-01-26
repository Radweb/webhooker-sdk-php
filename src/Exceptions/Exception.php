<?php

namespace WebHooker\Exceptions;

class Exception extends \Exception
{
    protected $message = 'Unknown Exception';

    /**
     * @var object|null
     */
    private $details;

    /**
     * @param string|null $message
     * @param object|null $details
     */
    public function __construct($message = null, $details = null)
    {
        $this->details = $details;
        parent::__construct($message ?: $this->message);
    }

    public function getDetails()
    {
        return $this->details;
    }
}

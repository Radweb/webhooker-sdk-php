<?php

namespace WebHooker;

class Message
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $tenant;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string[]
     */
    public $formats;

    /**
     * @var int
     */
    public $recipients;

    public function __construct($id, $tenant, $type, $formats, $recipients)
    {
        $this->id = $id;
        $this->tenant = $tenant;
        $this->type = $type;
        $this->formats = $formats;
        $this->recipients = $recipients;
    }
}
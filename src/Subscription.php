<?php

namespace WebHooker;

class Subscription
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $subscriberId;

    /**
     * @var string
     */
    public $tenant;

    /**
     * @var string
     */
    public $format;

    /**
     * @var string
     */
    public $url;

    public function __construct($id, $subscriberId, $tenant, $format, $url)
    {
        $this->id = $id;
        $this->subscriberId = $subscriberId;
        $this->tenant = $tenant;
        $this->format = $format;
        $this->url = $url;
    }
}
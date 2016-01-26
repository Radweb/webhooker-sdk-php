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

    /**
     * @var bool
     */
    public $usesBasicAuth = false;

    /**
     * @var string|null
     */
    public $legacyPayload;

    public function __construct($id, $subscriberId, $tenant, $format, $url)
    {
        $this->id = $id;
        $this->subscriberId = $subscriberId;
        $this->tenant = $tenant;
        $this->format = $format;
        $this->url = $url;
    }

    public function setUsesBasicAuth($usesBasicAuth)
    {
        $this->usesBasicAuth = (bool) $usesBasicAuth;
    }

    public function setLegacyPayload($payloadField)
    {
        $this->legacyPayload = $payloadField;
    }
}

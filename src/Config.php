<?php

namespace WebHooker;

class Config
{
    private $domain = 'https://api.webhooker.io';

    private $apiKey;

    /**
     * @param string|null $apiKey
     * @return $this
     */
    public static function make($apiKey = null)
    {
        return (new self)->setApiKey($apiKey);
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     * @return $this
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     * @return $this
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }
}
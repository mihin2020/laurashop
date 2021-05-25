<?php

class Lokalise_Model_SecretNotice
{
    /**
     * @var string[]
     */
    private $secrets;

    /**
     * @param string[] $secrets
     */
    public function __construct($secrets)
    {
        $this->secrets = $secrets;
    }

    /**
     * @return string[]
     */
    public function getSecrets()
    {
        return $this->secrets;
    }
}

<?php

namespace Console\App\Google;

use Console\App\Productsup\Config;
use Console\App\Google\AccountAuthorizationInterface;
use Google\Client;

class ServiceAccountAuthorization implements AccountAuthorizationInterface
{
    private $client;

    public function authorization()
    {
        $this->client = new Client();
        $this->client->setApplicationName("Productsup XML Data");
        $this->client->setScopes(['https://www.googleapis.com/auth/spreadsheets','https://www.googleapis.com/auth/drive']);
        $this->client->setAuthConfig(Config::getInstance()->getgoogleApiCredentialsFile());
        return 1;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function returnAuthorizedClient()
    {
        $this->authorization();
        return $this->client;
    }
}

<?php

namespace App\Repository;

use Finnhub;
use GuzzleHttp;

class apiRepository
{
    public $config;
    public $client;
    public function __construct()
    {
        $this->config = Finnhub\Configuration::getDefaultConfiguration()->setApiKey('token', 'c1pgp6iad3id1hoq0j40');
        $this->client = new Finnhub\Api\DefaultApi(
            new GuzzleHttp\Client(),
            $this->config
        );
    }
    public function getSymbolPrice($symbol)
    {
        return $this->client->quote($symbol);
    }
    public function getCompanyProfile($symbol)
    {
        return $this->client->companyProfile2($symbol);
    }

}
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
        $this->config = Finnhub\Configuration::getDefaultConfiguration()->setApiKey('token', 'c29b0haad3ifru1mbv80');
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
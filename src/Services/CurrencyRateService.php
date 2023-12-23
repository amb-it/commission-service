<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\CurrencyRateServiceInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class CurrencyRateService implements CurrencyRateServiceInterface
{
    protected const URL = 'https://developers.paysera.com/tasks/api/currency-exchange-rates';

    protected Client $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client();
    }

    /**
     * @throws \Exception
     */
    public function getEuroCurrencyRate(string $currencyCode, array $currencyRates): float
    {
        if (!isset($currencyRates[$currencyCode])) {
            throw new \Exception("Currency rate for $currencyCode is absent");
        }

        return (float)$currencyRates[$currencyCode];
    }

    /**
     * @throws GuzzleException
     * @throws \Exception
     */
    public function getEuroCurrencyRates(): array
    {
        return $this->getEuroCurrencyRatesData()['rates'];
    }

    /**
     * @throws GuzzleException
     * @throws \Exception
     */
    protected function getEuroCurrencyRatesData(): array
    {
        $response = $this->httpClient->get(self::URL);
        $response = $response->getBody()->getContents();

        if (!$response) {
            throw new \Exception('Currency Rate service is not available');
        }

        return json_decode($response, true);
    }
}
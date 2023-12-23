<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\BinServiceInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class BinService implements BinServiceInterface
{
    public const EUROPE_COUNTRY_CODES = [
        'AT',
        'BE',
        'BG',
        'CY',
        'CZ',
        'DE',
        'DK',
        'EE',
        'ES',
        'FI',
        'FR',
        'GR',
        'HR',
        'HU',
        'IE',
        'IT',
        'LT',
        'LU',
        'LV',
        'MT',
        'NL',
        'PO',
        'PT',
        'RO',
        'SE',
        'SI',
        'SK',
    ];
    protected const BASE_URL = 'https://lookup.binlist.net/';

    protected Client $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client();
    }

    /**
     * @throws GuzzleException
     * @throws \Exception
     */
    public function isBinEuropean(string $bin): bool
    {
        return $this->isCodeEuropean($this->getCountryCodeByBin($bin));
    }

    /**
     * @throws GuzzleException
     * @throws \Exception
     */
    public function getCountryCodeByBin(string $bin): string
    {
        $responseData = $this->getBinData($bin);

        if (!isset($responseData['country']['alpha2'])) {
            throw new \Exception('Bin checker service is not working properly');
        }

        return $responseData['country']['alpha2'];
    }

    /**
     * @throws GuzzleException
     * @throws \Exception
     */
    public function getBinData(string $bin): array
    {
        $response = $this->httpClient->get(self::BASE_URL . $bin);
        $response = $response->getBody()->getContents();

        if (!$response) {
            throw new \Exception('Bin checker service is not working properly');
        }

        return json_decode($response, true);
    }

    protected function isCodeEuropean(string $countryCode): bool
    {
        return in_array($countryCode, self::EUROPE_COUNTRY_CODES);
    }
}
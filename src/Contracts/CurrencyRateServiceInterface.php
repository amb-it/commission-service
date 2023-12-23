<?php

namespace App\Contracts;

interface CurrencyRateServiceInterface
{
    public function getEuroCurrencyRates(): array;
    public function getEuroCurrencyRate(string $currencyCode, array $currencyRates): float;
}
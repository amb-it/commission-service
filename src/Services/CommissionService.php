<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\BinServiceInterface;
use App\Contracts\CurrencyRateServiceInterface;
use App\Contracts\FileServiceInterface;
use GuzzleHttp\Exception\GuzzleException;

class CommissionService
{
    protected const COMMISSION_RATE = 0.02;
    protected const COMMISSION_RATE_EUROPE = 0.01;

    private FileServiceInterface $fileService;
    private BinService $binService;
    private CurrencyRateService $currencyRateService;

    public function __construct(
        FileServiceInterface $fileService,
        BinServiceInterface $binService,
        CurrencyRateServiceInterface $currencyRateService
    ) {
        $this->fileService = $fileService;
        $this->binService = $binService;
        $this->currencyRateService = $currencyRateService;
    }

    public function printTransactionsCommissions(): void
    {
        try {
            $commissions = $this->getTransactionsCommissions();

            echo implode("\n", $commissions);
        } catch (\Throwable $exception) {
            echo $exception->getMessage();
        }
    }

    /**
     * @throws GuzzleException
     * @throws \LogicException
     * @throws \Exception
     */
    public function getTransactionsCommissions(): array
    {
        $transactions = $this->fileService->getData();

        $currencyRates = $this->currencyRateService->getEuroCurrencyRates();

        $commissions = [];

        foreach ($transactions as $transaction) {
            $this->validateTransactionData($transaction);

            $isBinEuropean = $this->binService->isBinEuropean($transaction['bin']);

            $commissionRate = $this->getCommissionRate($isBinEuropean);
            $currencyRate = $this->currencyRateService->getEuroCurrencyRate($transaction['currency'], $currencyRates);

            if ($currencyRate <= 0 ) {
                throw new \LogicException('Currency rate must be > 0');
            }

            $commission = $transaction['amount'] * $commissionRate / $currencyRate;
            $commissions[] = round($commission, 2, PHP_ROUND_HALF_UP);;
        }

        return $commissions;
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function validateTransactionData(array $transaction): void
    {
        if (!isset($transaction['bin']) || !isset($transaction['currency']) || !isset($transaction['amount'])) {
            throw new \InvalidArgumentException('Transaction data is invalid');
        }
    }

    protected function getCommissionRate(bool $isEurope): float
    {
        return $isEurope ? self::COMMISSION_RATE_EUROPE : self::COMMISSION_RATE;
    }
}
<?php

use App\Services\BinService;
use App\Services\CommissionService;
use App\Services\CurrencyRateService;
use App\Services\FileService;
use PHPUnit\Framework\TestCase;

class CommissionServiceTest extends TestCase
{
    protected ?CommissionService $commissionService;
    protected ?FileService $fileService;
    protected ?BinService $binService;
    protected ?CurrencyRateService $currencyRateService;

    public function setUp(): void
    {
        parent::setUp();

        $this->fileServiceMock = $this->createMock(FileService::class);
        $this->binServiceMock = $this->createMock(BinService::class);
        $this->currencyRateServiceMock = $this->createMock(CurrencyRateService::class);

        $this->fileServiceMock->method('getData')
            ->willReturn($this->getFakeFileData());

        $this->commissionService = new CommissionService(
            $this->fileServiceMock,
            $this->binServiceMock,
            $this->currencyRateServiceMock
        );
    }

    public function testGetTransactionsCommissionsForEuropeanBin()
    {
        $this->binServiceMock->method('isBinEuropean')
            ->willReturn(true);

        $this->currencyRateServiceMock->method('getEuroCurrencyRate')
            ->willReturn(2.1);

        $commissions = $this->commissionService->getTransactionsCommissions();

        $this->assertEquals($commissions, [0.48, 0.29]);
    }

    public function testGetTransactionsCommissionsForNotEuropeanBin()
    {
        $this->binServiceMock->method('isBinEuropean')
            ->willReturn(false);

        $this->currencyRateServiceMock->method('getEuroCurrencyRate')
            ->willReturn(2.1);

        $commissions = $this->commissionService->getTransactionsCommissions();

        $this->assertEquals($commissions, [0.95, 0.57]);
    }

    public function testGetTransactionsCommissionsForEuro()
    {
        $this->binServiceMock->method('isBinEuropean')
            ->willReturn(false);

        $this->currencyRateServiceMock->method('getEuroCurrencyRate')
            ->willReturn(1.0);

        $commissions = $this->commissionService->getTransactionsCommissions();

        $this->assertEquals($commissions, [2.0, 1.2]);
    }

    public function testGetTransactionsCommissionsForIncorrectRate()
    {
        $this->binServiceMock->method('isBinEuropean')
            ->willReturn(false);

        $this->currencyRateServiceMock->method('getEuroCurrencyRate')
            ->willReturn(0.0);

        $this->expectException(\LogicException::class);

        $this->commissionService->getTransactionsCommissions();
    }

    protected function getFakeFileData(): array
    {
        return [
            [
                'bin' => '45717360',
                'amount' => '100.00',
                'currency' => 'EUR',
            ],
            [
                'bin' => '516793',
                'amount' => '59.99',
                'currency' => 'USD',
            ],
        ];
    }
}
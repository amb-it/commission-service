<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Services\BinService;
use App\Services\CommissionService;
use App\Services\CurrencyRateService;
use App\Services\FileService;

$fileHandler = new FileService();
$binHandler = new BinService();
$currencyRateService = new CurrencyRateService();

$commissionHandler = new CommissionService($fileHandler, $binHandler, $currencyRateService);

$commissionHandler->printTransactionsCommissions();
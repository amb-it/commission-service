<?php

namespace App\Contracts;

interface BinServiceInterface
{
    public function isBinEuropean(string $bin): bool;
}
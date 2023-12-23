<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\FileServiceInterface;

class FileService implements FileServiceInterface
{
    /**
     * @throws \InvalidArgumentException
     */
    public function getData(): array
    {
        $content = file_get_contents($this->getFileName());
        $data = explode("\n", $content);

        foreach ($data as $key => $el) {
            $data[$key] = json_decode($el, true);
        }

        return $data;
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function getFileName(): string
    {
        $scriptArguments = $_SERVER['argv'];

        if (!isset($scriptArguments[1])) {
            throw new \InvalidArgumentException('File name is not set as argument');
        }

        $filePath = 'resources/' . $scriptArguments[1];

        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException('File does not exist');
        }

        return $filePath;
    }
}
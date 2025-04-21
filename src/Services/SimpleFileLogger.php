<?php

namespace DP0\Kohub\Services;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;

class SimpleFileLogger implements LoggerInterface
{
    use LoggerTrait;

    protected string $file;

    public function __construct(string $file)
    {
        $this->file = $file;
    }

    public function log($level, $message, array $context = []): void
    {
        $entry = '[' . strtoupper($level) . '] ' . $message;

        if (!empty($context)) {
            $entry .= ' ' . json_encode($context);
        }

        $entry .= PHP_EOL;

        file_put_contents($this->file, $entry, FILE_APPEND);
    }
}

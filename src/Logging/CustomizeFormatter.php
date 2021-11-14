<?php

namespace Yomo7\Logging\Logging;

use Monolog\Formatter\JsonFormatter;

class CustomizeFormatter
{

    public function __invoke($logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $formatter = new JsonFormatter();
            $handler->setFormatter($formatter);
        }
    }

    private function getIpAddress(): string
    {
        $clientIP = '127.0.0.1';
        // Check for proxies as well.
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $clientIP = $_SERVER['REMOTE_ADDR'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $clientIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $clientIP = $_SERVER['HTTP_CLIENT_IP'];
        }

        return $clientIP;
    }

}
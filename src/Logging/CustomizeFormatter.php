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
}
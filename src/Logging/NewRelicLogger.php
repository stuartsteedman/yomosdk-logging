<?php

namespace Yomo7\Logging\Logging;

use Illuminate\Support\Facades\App;
use Monolog\Handler\NewRelicHandler;
use Monolog\Logger;
use Illuminate\Http\Request;
use Monolog\Handler\BufferHandler;
use NewRelic\Monolog\Enricher\{Handler, Processor};

class NewRelicLogger
{
    protected $request;

    public function __construct(Request $request = null)
    {
        $this->request = $request;
    }

    public function __invoke(array $config)
    {
        # add the new relic logger
        $log = new Logger('newrelic');
        $log->pushProcessor(new Processor);
        $handler = new Handler;

        if (App::isLocal()) {
            # Localhosts won't have the NR agent installed, so get via the logging channels
            $handler->setLicenseKey(config('logging.channels.newrelic.api_key'));
        }

        # using the BufferHandler improves the performance by batching the log messages to the end of the request
        # and then adding the NewRelic handler for NR logging for anything over a WARNING level
        $log->pushHandler(new BufferHandler($handler));
        $log->pushHandler(new NewRelicHandler(Logger::WARNING, true));

        foreach ($log->getHandlers() as $handler) {
            $handler->pushProcessor([$this, 'enrich']);
        }

        return $log;
    }

    public function enrich(array $context): array
    {
        LoggingEnricher::execute($context);
        return $context;
    }

}

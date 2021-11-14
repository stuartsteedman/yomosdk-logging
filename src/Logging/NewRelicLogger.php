<?php

namespace Yomo7\Logging\Logging;

use Auth;
use Illuminate\Support\Facades\App;
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
        // add the new relic logger
        $log = new Logger('newrelic');
        $log->pushProcessor(new Processor);
        $handler = new Handler;

        if (App::isLocal()) {
            # Localhosts won't have the NR agent installed, so get via the logging channels
            $handler->setLicenseKey(config('logging.channels.newrelic.api_key'));
        }

        // using the BufferHandler improves the performance by batching the log
        // messages to the end of the request
        $log->pushHandler(new BufferHandler($handler));

        /*foreach ($log->getHandlers() as $handler) {
            $handler->pushProcessor([$this, 'includeMetaData']);
        }*/

        return $log;
    }

    // lets add some extra metadata to every request
    public function includeMetaData(array $record): array
    {
        $record['yomo_version'] = '2.4.4';

        // set the service or app name to the record
        $record['service'] = 'Laravel-App-Name';

        // set the hostname to record so we know host this was created on
        $record['hostname'] = gethostname();

        // check to see if we have a request
        if ($this->request) {

            $record['extra'] += [
                'ip' => $this->request->getClientIp(),
            ];

            // get the authenticated user
            $user = Auth::user();

            // add the user information
            if ($user) {
                $record['user'] = [
                    'id' => $user->id ?? null,
                    'email' => $user->email ?? 'guest',
                ];
            }
        }
        return $record;
    }
}

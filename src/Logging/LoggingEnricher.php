<?php

namespace Yomo7\Logging\Logging;

abstract class LoggingEnricher
{
    /** @var string */
    public string $type;

    /** @var array|LoggingEnricher[] */
    protected static array $enrichers = [];

    /**
     * Registers a logging enricher
     *
     * @param LoggingEnricher $enricher
     * @return void
     */
    public static function register(LoggingEnricher $enricher): void
    {
        self::$enrichers[$enricher->type] = $enricher;
    }

    /**
     * @param array $context
     */
    public static function execute(array &$context): void
    {
        /** @var LoggingEnricher $enricher */
        foreach(self::$enrichers as $enricher) {
            $enricher->enrich($context);
        }
    }

    /**
     * Inject channel-specific enriched logging values into the context logging array
     *
     * @param array $context
     *
     * @return void
     */
    abstract protected function enrich(array &$context): void;
}
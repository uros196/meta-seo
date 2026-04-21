<?php

namespace MetaSeo;

use MetaSeo\Items\MetaItem;
use MetaSeo\Items\OgMetaItem;
use MetaSeo\Items\TwitterMetaItem;
use MetaSeo\Enums\MetaField;

/**
 * Resolves the appropriate MetaItem class and normalized key based on key prefixes.
 */
class MetaResolver
{
    /**
     * Map of key prefixes to their respective classes.
     */
    protected static array $strategies = [
        'og:' => OgMetaItem::class,
        'twitter:' => TwitterMetaItem::class,
    ];

    /**
     * Register a new strategy.
     */
    public static function registerStrategy(string $prefix, string $className): void
    {
        static::$strategies[$prefix] = $className;
    }

    /**
     * Get all registered strategies.
     */
    public static function getStrategies(): array
    {
        return static::$strategies;
    }

    /**
     * Resolve the class name and normalized key for a given meta-key.
     *
     * @return array [string $className, string $normalizedKey]
     */
    public static function resolve(string|MetaField $key): array
    {
        $className = MetaItem::class;

        foreach (static::getStrategies() as $prefix => $class) {
            $baseKey = is_string($key) ? $key : $key->value;

            if (str_starts_with($baseKey, $prefix)) {
                $key = is_string($key) ? $class::generateKey($key) : $key->value;
                $className = $class;
                break;
            }
        }

        return [$className, $key];
    }
}

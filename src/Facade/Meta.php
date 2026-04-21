<?php

namespace MetaSeo\Facade;

use MetaSeo\MetaManager;
use Illuminate\Support\Facades\Facade;

/**
 * Facade for the MetaManager.
 *
 * @see MetaManager
 * @mixin MetaManager
 */
class Meta extends Facade
{
    /**
     * Create a facade accessor for MetaManager.
     */
    protected static function getFacadeAccessor(): string
    {
        return MetaManager::class;
    }
}

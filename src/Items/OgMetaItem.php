<?php

namespace MetaSeo\Items;

use MetaSeo\AbstractMetaItem;

/**
 * Specialized meta-item for OpenGraph tags.
 *
 * Automatically handles the 'og:' prefix and uses 'property'
 * as the primary attribute name.
 */
class OgMetaItem extends AbstractMetaItem
{
    /**
     * Name of the primary attribute.
     */
    protected string $primaryAttributeName = 'property';

    /**
     * Get the group name this meta-item belongs to (e.g., 'basic', 'og', 'twitter').
     */
    public function getGroup(): string
    {
        return 'og';
    }

    /**
     * Generate a consistent key for OpenGraph tags.
     */
    public static function generateKey(string $key): string
    {
        if (!str_starts_with($key, 'og:')) {
            $key = "og:$key";
        }

        return $key;
    }

    /**
     * Get the identifier key for this meta-item.
     */
    public function getKey(): ?string
    {
        return $this->getAttribute($this->primaryAttributeName);
    }
}

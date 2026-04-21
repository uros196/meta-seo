<?php

namespace MetaSeo\Items;

use MetaSeo\AbstractMetaItem;

/**
 * Specialized meta-item for Twitter tags.
 *
 * Automatically handles the 'twitter:' prefix.
 */
class TwitterMetaItem extends AbstractMetaItem
{
    /**
     * Generate a consistent key for meta-tags.
     */
    public function getGroup(): string
    {
        return 'twitter';
    }

    /**
     * Generate a consistent key for Twitter tags.
     */
    public static function generateKey(string $key): string
    {
        if (!str_starts_with($key, 'twitter:')) {
            $key = "twitter:$key";
        }

        return $key;
    }

    /**
     * Generate a consistent key for meta-tags.
     */
    public function getKey(): ?string
    {
        return $this->getAttribute($this->getPrimaryAttributeName());
    }
}

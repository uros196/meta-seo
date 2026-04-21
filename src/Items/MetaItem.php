<?php

namespace MetaSeo\Items;

use MetaSeo\AbstractMetaItem;

/**
 * Basic implementation of a meta-tag item.
 *
 * Used for standard tags like 'description', 'keywords', etc.
 */
class MetaItem extends AbstractMetaItem
{
    /**
     * Get the identifier key for this meta-item.
     */
    public function getKey(): ?string
    {
        return $this->getAttribute($this->getPrimaryAttributeName());
    }
}

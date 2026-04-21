<?php

namespace MetaSeo\Tests;

use MetaSeo\Items\MetaItem;
use MetaSeo\Items\OgMetaItem;
use MetaSeo\Items\TwitterMetaItem;
use MetaSeo\Enums\MetaField;

class MetaFieldTest extends TestCase
{
    public function test_it_resolves_to_correct_item_class()
    {
        $this->assertEquals(MetaItem::class, MetaField::Description->resolveItem());
        $this->assertEquals(OgMetaItem::class, MetaField::OgTitle->resolveItem());
        $this->assertEquals(TwitterMetaItem::class, MetaField::TwitterTitle->resolveItem());
    }

    public function test_it_builds_correct_item_instance()
    {
        $item = MetaField::OgTitle->buildItem();

        $this->assertInstanceOf(OgMetaItem::class, $item);
        $this->assertEquals('og:title', $item->getKey());
    }
}

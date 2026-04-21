<?php

namespace MetaSeo\Tests;

use MetaSeo\Items\MetaItem;
use MetaSeo\Items\OgMetaItem;
use MetaSeo\Items\TwitterMetaItem;
use MetaSeo\Enums\MetaField;

class MetaFieldTest extends TestCase
{
    /**
     * Test that each MetaField enum correctly resolves to its corresponding MetaItem class.
     */
    public function test_it_resolves_to_correct_item_class()
    {
        $this->assertEquals(MetaItem::class, MetaField::Description->resolveItem(), 'MetaField::Description should resolve to MetaItem.');
        $this->assertEquals(OgMetaItem::class, MetaField::OgTitle->resolveItem(), 'MetaField::OgTitle should resolve to OgMetaItem.');
        $this->assertEquals(TwitterMetaItem::class, MetaField::TwitterTitle->resolveItem(), 'MetaField::TwitterTitle should resolve to TwitterMetaItem.');
    }

    /**
     * Test that each MetaField enum can build a correctly configured item instance.
     */
    public function test_it_builds_correct_item_instance()
    {
        $item = MetaField::OgTitle->buildItem();

        $this->assertInstanceOf(OgMetaItem::class, $item, 'buildItem() should return an instance of OgMetaItem for og tags.');
        $this->assertEquals('og:title', $item->getKey(), 'The built item should have the correct key.');
    }
}

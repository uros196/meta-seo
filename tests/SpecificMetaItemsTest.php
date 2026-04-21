<?php

namespace MetaSeo\Tests;

use MetaSeo\Items\OgMetaItem;
use MetaSeo\Items\TwitterMetaItem;

class SpecificMetaItemsTest extends TestCase
{
    public function test_og_meta_item_uses_property_attribute()
    {
        $item = new OgMetaItem('og:title', 'test');
        $expected = '<meta property="og:title" content="test">';

        $this->assertEquals($expected, $item->render());
        $this->assertEquals('og', $item->getGroup());
    }

    public function test_twitter_meta_item_uses_name_attribute()
    {
        $item = new TwitterMetaItem('twitter:card', 'summary');
        $expected = '<meta name="twitter:card" content="summary">';

        $this->assertEquals($expected, $item->render());
        $this->assertEquals('twitter', $item->getGroup());
    }
}

<?php

namespace MetaSeo\Tests;

use MetaSeo\Items\OgMetaItem;
use MetaSeo\Items\TwitterMetaItem;

class SpecificMetaItemsTest extends TestCase
{
    /**
     * Test that OgMetaItem correctly uses the 'property' attribute instead of 'name'.
     */
    public function test_og_meta_item_uses_property_attribute()
    {
        $item = new OgMetaItem('og:title', 'test');
        $expected = '<meta property="og:title" content="test">';

        $this->assertEquals($expected, $item->render(), 'OgMetaItem should render with property attribute.');
        $this->assertEquals('og', $item->getGroup(), 'OgMetaItem should belong to the og group.');
    }

    /**
     * Test that TwitterMetaItem correctly uses the 'name' attribute.
     */
    public function test_twitter_meta_item_uses_name_attribute()
    {
        $item = new TwitterMetaItem('twitter:card', 'summary');
        $expected = '<meta name="twitter:card" content="summary">';

        $this->assertEquals($expected, $item->render(), 'TwitterMetaItem should render with name attribute.');
        $this->assertEquals('twitter', $item->getGroup(), 'TwitterMetaItem should belong to the twitter group.');
    }
}

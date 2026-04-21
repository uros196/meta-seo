<?php

namespace MetaSeo\Tests;

use MetaSeo\Items\MetaItem;
use MetaSeo\Items\OgMetaItem;
use MetaSeo\Items\TwitterMetaItem;
use MetaSeo\MetaResolver;
use MetaSeo\Enums\MetaField;

class MetaResolverTest extends TestCase
{
    public function test_it_resolves_standard_meta_keys()
    {
        [$className, $key] = MetaResolver::resolve('description');

        $this->assertEquals(MetaItem::class, $className);
        $this->assertEquals('description', $key);
    }

    public function test_it_resolves_og_meta_keys()
    {
        [$className, $key] = MetaResolver::resolve('og:title');

        $this->assertEquals(OgMetaItem::class, $className);
        $this->assertEquals('og:title', $key);
    }

    public function test_it_resolves_twitter_meta_keys()
    {
        [$className, $key] = MetaResolver::resolve('twitter:card');

        $this->assertEquals(TwitterMetaItem::class, $className);
        $this->assertEquals('twitter:card', $key);
    }

    public function test_it_resolves_meta_field_enums()
    {
        [$className, $key] = MetaResolver::resolve(MetaField::OgTitle);

        $this->assertEquals(OgMetaItem::class, $className);
        $this->assertEquals('og:title', $key);
    }

    public function test_it_can_register_new_strategies()
    {
        MetaResolver::registerStrategy('custom:', MetaItem::class);

        $strategies = MetaResolver::getStrategies();
        $this->assertArrayHasKey('custom:', $strategies);
        $this->assertEquals(MetaItem::class, $strategies['custom:']);

        [$className, $key] = MetaResolver::resolve('custom:test');
        $this->assertEquals(MetaItem::class, $className);
        $this->assertEquals('custom:test', $key);
    }
}

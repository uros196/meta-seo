<?php

namespace MetaSeo\Tests;

use MetaSeo\Items\MetaItem;
use MetaSeo\Items\OgMetaItem;
use MetaSeo\Items\TwitterMetaItem;
use MetaSeo\MetaResolver;
use MetaSeo\Enums\MetaField;

class MetaResolverTest extends TestCase
{
    /**
     * Test that it resolves standard meta keys to MetaItem.
     */
    public function test_it_resolves_standard_meta_keys()
    {
        [$className, $key] = MetaResolver::resolve('description');

        $this->assertEquals(MetaItem::class, $className, 'Standard keys should resolve to MetaItem.');
        $this->assertEquals('description', $key, 'The key should remain unchanged for standard meta tags.');
    }

    /**
     * Test that it resolves og: keys to OgMetaItem.
     */
    public function test_it_resolves_og_meta_keys()
    {
        [$className, $key] = MetaResolver::resolve('og:title');

        $this->assertEquals(OgMetaItem::class, $className, 'OpenGraph keys should resolve to OgMetaItem.');
        $this->assertEquals('og:title', $key, 'The OpenGraph key should be correctly resolved.');
    }

    /**
     * Test that it resolves twitter: keys to TwitterMetaItem.
     */
    public function test_it_resolves_twitter_meta_keys()
    {
        [$className, $key] = MetaResolver::resolve('twitter:card');

        $this->assertEquals(TwitterMetaItem::class, $className, 'Twitter keys should resolve to TwitterMetaItem.');
        $this->assertEquals('twitter:card', $key, 'The Twitter key should be correctly resolved.');
    }

    /**
     * Test that it can resolve MetaField enums.
     */
    public function test_it_resolves_meta_field_enums()
    {
        [$className, $key] = MetaResolver::resolve(MetaField::OgTitle);

        $this->assertEquals(OgMetaItem::class, $className, 'MetaField enums should resolve to their corresponding item class.');
        $this->assertEquals('og:title', $key, 'MetaField enum value should be used as the key.');
    }

    /**
     * Test that new strategies can be registered at runtime.
     */
    public function test_it_can_register_new_strategies()
    {
        MetaResolver::registerStrategy('custom:', MetaItem::class);

        $strategies = MetaResolver::getStrategies();
        $this->assertArrayHasKey('custom:', $strategies, 'The new strategy should be present in the strategies array.');
        $this->assertEquals(MetaItem::class, $strategies['custom:'], 'The new strategy should map to the correct class.');

        [$className, $key] = MetaResolver::resolve('custom:test');
        $this->assertEquals(MetaItem::class, $className, 'The custom prefix should resolve using the new strategy.');
        $this->assertEquals('custom:test', $key, 'The custom key should be correctly handled.');
    }
}

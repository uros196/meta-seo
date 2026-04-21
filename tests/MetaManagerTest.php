<?php

namespace MetaSeo\Tests;

use MetaSeo\Items\MetaItem;
use MetaSeo\Items\OgMetaItem;
use MetaSeo\MetaManager;
use MetaSeo\Enums\MetaField;

class MetaManagerTest extends TestCase
{
    protected MetaManager $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = new MetaManager();
    }

    /**
     * Test that items can be added directly to the manager.
     */
    public function test_it_adds_items()
    {
        $item = new MetaItem('description', 'test');
        $this->manager->addItem($item);

        $this->assertCount(1, $this->manager->getItems(), 'Manager should contain exactly one item.');
        $this->assertContains($item, $this->manager->getItems(), 'Manager should contain the added item.');
    }

    /**
     * Test that items can be created using helper methods like item(), ogItem(), etc.
     */
    public function test_it_creates_items_via_helper_methods()
    {
        $this->manager->item('description', ['content' => 'test']);
        $this->manager->ogItem('og:title', 'OG Title');
        $this->manager->twitterItem('twitter:card', 'summary');

        $items = $this->manager->getItems();
        $this->assertCount(3, $items, 'Manager should have created three items via helpers.');
        $this->assertInstanceOf(MetaItem::class, $items['description'], 'Description should be a standard MetaItem.');
        $this->assertInstanceOf(OgMetaItem::class, $items['og:title'], 'og:title should be an OgMetaItem.');
    }

    /**
     * Test synchronization of title and description across OG and Twitter tags.
     */
    public function test_it_sets_title_and_description_with_og_sync()
    {
        $this->manager->setTitle('Page Title');
        $this->manager->setDescription('Page Description');

        $items = $this->manager->getItems();

        $this->assertEquals('Page Title', $items['title']->getValue(), 'Main title value is incorrect.');
        $this->assertEquals('Page Title', $items['og:title']->getValue(), 'og:title sync failed.');
        $this->assertEquals('Page Title', $items['twitter:title']->getValue(), 'twitter:title sync failed.');
        $this->assertEquals('Page Description', $items['description']->getValue(), 'Main description value is incorrect.');
        $this->assertEquals('Page Description', $items['og:description']->getValue(), 'og:description sync failed.');
        $this->assertEquals('Page Description', $items['twitter:description']->getValue(), 'twitter:description sync failed.');
    }

    /**
     * Test that specific keys can be excluded from rendering.
     */
    public function test_it_can_exclude_keys()
    {
        $this->manager->setTitle('Title');
        $this->manager->exclude('og:title');

        $this->assertStringNotContainsString('og:title', $this->manager->renderItems(), 'Excluded key "og:title" should not be rendered.');
    }

    /**
     * Test that previously excluded keys (like the default title) can be included back.
     */
    public function test_it_can_include_keys()
    {
        $this->manager->setTitle('Title');
        // 'title' is excluded by default
        $this->assertStringNotContainsString('name="title"', $this->manager->renderItems(), 'Default excluded key "title" should not be in output.');

        $this->manager->include('title');
        $this->assertStringContainsString('name="title"', $this->manager->renderItems(), 'Included key "title" should be in output.');
    }

    /**
     * Test that the manager renders all registered items correctly.
     */
    public function test_it_renders_all_items()
    {
        $this->manager->setTitle('Title');
        $this->manager->setDescription('Desc');

        $output = $this->manager->renderItems();

        $this->assertStringContainsString('<meta name="description" content="Desc">', $output, 'Rendered output is missing description.');
        $this->assertStringContainsString('<meta property="og:title" content="Title">', $output, 'Rendered output is missing og:title.');
        $this->assertStringContainsString('<meta property="og:description" content="Desc">', $output, 'Rendered output is missing og:description.');
        $this->assertStringContainsString('<meta name="twitter:title" content="Title">', $output, 'Rendered output is missing twitter:title.');
        $this->assertStringContainsString('<meta name="twitter:description" content="Desc">', $output, 'Rendered output is missing twitter:description.');
    }

    /**
     * Test that the manager can be cleared.
     */
    public function test_it_clears_items()
    {
        $this->manager->setTitle('Title');
        $this->manager->clear();

        $this->assertCount(0, $this->manager->getItems(), 'Manager should have zero items after clear().');
    }

    /**
     * Test that accessing an item automatically creates it.
     */
    public function test_it_automatically_creates_item_on_access()
    {
        $this->assertCount(0, $this->manager->getItems(), 'Manager should start with zero items.');

        $item = $this->manager->item('new-item');

        $this->assertCount(1, $this->manager->getItems(), 'Item should be automatically created on access.');
        $this->assertEquals('new-item', $item->getKey(), 'The created item should have the correct key.');
        $this->assertSame($item, $this->manager->getItems()['new-item'], 'The returned item should be same instance as in manager.');
    }

    /**
     * Test the noIndex helper method.
     */
    public function test_no_index_method()
    {
        $this->manager->noIndex(true);
        $this->assertStringContainsString('<meta name="robots" content="noindex,nofollow">', $this->manager->renderItems(), 'noIndex(true) should set noindex,nofollow.');

        $this->manager->noIndex(false);
        $this->assertStringContainsString('<meta name="robots" content="index,follow">', $this->manager->renderItems(), 'noIndex(false) should set index,follow.');
    }

    /**
     * Test synchronization with fallback values.
     */
    public function test_it_handles_sync_with_fallback()
    {
        $this->manager->setTitleWithFallback('', 'Fallback Title');

        $items = $this->manager->getItems();
        $this->assertEquals('Fallback Title', $items['title']->getValue(), 'Fallback sync for title failed.');
        $this->assertEquals('Fallback Title', $items['og:title']->getValue(), 'Fallback sync for og:title failed.');
        $this->assertEquals('Fallback Title', $items['twitter:title']->getValue(), 'Fallback sync for twitter:title failed.');
    }

    /**
     * Test that keys are correctly transformed (e.g. from Enums) when excluding.
     */
    public function test_it_transforms_keys_via_exclude()
    {
        $this->manager->setTitle('Title');
        $this->manager->exclude(MetaField::OgTitle);

        $this->manager->setDescription('Desc');
        $this->manager->exclude(['og:description', MetaField::TwitterDescription]);

        $renderedItems = $this->manager->renderItems();

        $this->assertStringNotContainsString('og:title', $renderedItems, 'og:title (excluded via Enum) should not be in output.');
        $this->assertStringNotContainsString('og:description', $renderedItems, 'og:description (excluded via string) should not be in output.');
        $this->assertStringNotContainsString('twitter:description', $renderedItems, 'twitter:description (excluded via Enum) should not be in output.');
    }
}

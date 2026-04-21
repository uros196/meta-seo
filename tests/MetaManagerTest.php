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

    public function test_it_adds_items()
    {
        $item = new MetaItem('description', 'test');
        $this->manager->addItem($item);

        $this->assertCount(1, $this->manager->getItems());
        $this->assertContains($item, $this->manager->getItems());
    }

    public function test_it_creates_items_via_helper_methods()
    {
        $this->manager->item('description', ['content' => 'test']);
        $this->manager->ogItem('og:title', 'OG Title');
        $this->manager->twitterItem('twitter:card', 'summary');

        $items = $this->manager->getItems();
        $this->assertCount(3, $items);
        $this->assertInstanceOf(MetaItem::class, $items['description']);
        $this->assertInstanceOf(OgMetaItem::class, $items['og:title']);
    }

    public function test_it_sets_title_and_description_with_og_sync()
    {
        $this->manager->setTitle('Page Title');
        $this->manager->setDescription('Page Description');

        $items = $this->manager->getItems();

        $this->assertEquals('Page Title', $items['title']->getValue());
        $this->assertEquals('Page Title', $items['og:title']->getValue());
        $this->assertEquals('Page Title', $items['twitter:title']->getValue());
        $this->assertEquals('Page Description', $items['description']->getValue());
        $this->assertEquals('Page Description', $items['og:description']->getValue());
        $this->assertEquals('Page Description', $items['twitter:description']->getValue());
    }

    public function test_it_can_exclude_keys()
    {
        $this->manager->setTitle('Title');
        $this->manager->exclude('og:title');

        $this->assertStringNotContainsString('og:title', $this->manager->renderItems());
    }

    public function test_it_can_include_keys()
    {
        $this->manager->setTitle('Title');
        // 'title' is excluded by default
        $this->assertStringNotContainsString('name="title"', $this->manager->renderItems());

        $this->manager->include('title');
        $this->assertStringContainsString('name="title"', $this->manager->renderItems());
    }

    public function test_it_renders_all_items()
    {
        $this->manager->setTitle('Title');
        $this->manager->setDescription('Desc');

        $output = $this->manager->renderItems();

        $this->assertStringContainsString('<meta name="description" content="Desc">', $output);
        $this->assertStringContainsString('<meta property="og:title" content="Title">', $output);
        $this->assertStringContainsString('<meta property="og:description" content="Desc">', $output);
        $this->assertStringContainsString('<meta name="twitter:title" content="Title">', $output);
        $this->assertStringContainsString('<meta name="twitter:description" content="Desc">', $output);
    }

    public function test_it_clears_items()
    {
        $this->manager->setTitle('Title');
        $this->manager->clear();

        $this->assertCount(0, $this->manager->getItems());
    }

    public function test_it_automatically_creates_item_on_access()
    {
        $this->assertCount(0, $this->manager->getItems());

        $item = $this->manager->item('new-item');

        $this->assertCount(1, $this->manager->getItems());
        $this->assertEquals('new-item', $item->getKey());
        $this->assertSame($item, $this->manager->getItems()['new-item']);
    }

    public function test_no_index_method()
    {
        $this->manager->noIndex(true);
        $this->assertStringContainsString('<meta name="robots" content="noindex,nofollow">', $this->manager->renderItems());

        $this->manager->noIndex(false);
        $this->assertStringContainsString('<meta name="robots" content="index,follow">', $this->manager->renderItems());
    }

    public function test_it_handles_sync_with_fallback()
    {
        $this->manager->setTitleWithFallback('', 'Fallback Title');

        $items = $this->manager->getItems();
        $this->assertEquals('Fallback Title', $items['title']->getValue());
        $this->assertEquals('Fallback Title', $items['og:title']->getValue());
        $this->assertEquals('Fallback Title', $items['twitter:title']->getValue());
    }

    public function test_it_transforms_keys_via_exclude()
    {
        $this->manager->setTitle('Title');
        $this->manager->exclude(MetaField::OgTitle);

        $this->manager->setDescription('Desc');
        $this->manager->exclude(['og:description', MetaField::TwitterDescription]);

        $renderedItems = $this->manager->renderItems();

        $this->assertStringNotContainsString('og:title', $renderedItems);
        $this->assertStringNotContainsString('og:description', $renderedItems);
        $this->assertStringNotContainsString('twitter:description', $renderedItems);
    }
}

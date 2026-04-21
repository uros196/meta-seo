<?php

namespace MetaSeo\Tests;

use MetaSeo\Items\MetaItem;
use MetaSeo\Enums\MetaField;

class MetaItemTest extends TestCase
{
    public function test_it_sets_initial_key_and_content()
    {
        $item = new MetaItem('description', 'test content');

        $this->assertEquals('description', $item->getKey());
        $this->assertEquals('test content', $item->getValue());
    }

    public function test_it_handles_meta_field_enum_in_constructor()
    {
        $item = new MetaItem(MetaField::Description, 'test content');

        $this->assertEquals('description', $item->getKey());
    }

    public function test_it_can_set_value_manually()
    {
        $item = new MetaItem('description');
        $item->setValue('new content');

        $this->assertEquals('new content', $item->getValue());
    }

    public function test_it_squishes_values()
    {
        $item = new MetaItem('description', "  test   \n content  ");

        $this->assertEquals('test content', $item->getValue());
    }

    public function test_it_handles_fallbacks()
    {
        $item = new MetaItem('description');
        $item->setFallbackValue('fallback content');
        $item->useFallbackValue(true);

        $this->assertEquals('fallback content', $item->getValue());

        $item->setValue('primary content');
        $this->assertEquals('primary content', $item->getValue());
    }

    public function test_it_renders_to_html()
    {
        $item = new MetaItem('description', 'test content');
        $expected = '<meta name="description" content="test content">';

        $this->assertEquals($expected, $item->render());
        $this->assertEquals($expected, (string) $item);
    }

    public function test_it_does_not_render_empty_content_unless_forced()
    {
        $item = new MetaItem('description');

        $this->assertEquals('', $item->render());

        $item->forceRender(true);
        $this->assertEquals('<meta name="description">', $item->render());
    }

    public function test_it_escapes_content_during_render()
    {
        $item = new MetaItem('description', 'test "content" & more');
        $expected = '<meta name="description" content="test &quot;content&quot; &amp; more">';

        $this->assertEquals($expected, $item->render());
    }

    public function test_it_can_set_custom_attributes()
    {
        $item = new MetaItem('description', 'test');
        $item->setAttribute('id', 'meta-id');

        $this->assertStringContainsString('id="meta-id"', $item->render());
    }
}

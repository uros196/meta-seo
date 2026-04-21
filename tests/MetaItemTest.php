<?php

namespace MetaSeo\Tests;

use MetaSeo\Items\MetaItem;
use MetaSeo\Enums\MetaField;

class MetaItemTest extends TestCase
{
    /**
     * Test that the constructor correctly sets the initial key and content.
     */
    public function test_it_sets_initial_key_and_content()
    {
        $item = new MetaItem('description', 'test content');

        $this->assertEquals('description', $item->getKey(), 'Initial key is incorrect.');
        $this->assertEquals('test content', $item->getValue(), 'Initial content value is incorrect.');
    }

    /**
     * Test that the MetaField enum can be used as a key in the constructor.
     */
    public function test_it_handles_meta_field_enum_in_constructor()
    {
        $item = new MetaItem(MetaField::Description, 'test content');

        $this->assertEquals('description', $item->getKey(), 'Key from MetaField enum is incorrect.');
    }

    /**
     * Test that the value can be updated after instantiation.
     */
    public function test_it_can_set_value_manually()
    {
        $item = new MetaItem('description');
        $item->setValue('new content');

        $this->assertEquals('new content', $item->getValue(), 'Updated value is incorrect.');
    }

    /**
     * Test that whitespace and newlines are squished in the content value.
     */
    public function test_it_squishes_values()
    {
        $item = new MetaItem('description', "  test   \n content  ");

        $this->assertEquals('test content', $item->getValue(), 'Whitespace squishing failed.');
    }

    /**
     * Test fallback value logic.
     */
    public function test_it_handles_fallbacks()
    {
        $item = new MetaItem('description');
        $item->setFallbackValue('fallback content');
        $item->useFallbackValue(true);

        $this->assertEquals('fallback content', $item->getValue(), 'Fallback value should be returned when primary is empty.');

        $item->setValue('primary content');
        $this->assertEquals('primary content', $item->getValue(), 'Primary value should be returned even if fallback is set.');
    }

    /**
     * Test HTML rendering of the meta tag.
     */
    public function test_it_renders_to_html()
    {
        $item = new MetaItem('description', 'test content');
        $expected = '<meta name="description" content="test content">';

        $this->assertEquals($expected, $item->render(), 'HTML rendering output is incorrect.');
        $this->assertEquals($expected, (string) $item, 'String casting of the item should match render() output.');
    }

    /**
     * Test that empty meta tags are not rendered unless forceRender is enabled.
     */
    public function test_it_does_not_render_empty_content_unless_forced()
    {
        $item = new MetaItem('description');

        $this->assertEquals('', $item->render(), 'Empty meta item should render as empty string by default.');

        $item->forceRender(true);
        $this->assertEquals('<meta name="description">', $item->render(), 'Forced render of empty item should output the tag without content attribute.');
    }

    /**
     * Test that HTML special characters are escaped in the content attribute.
     */
    public function test_it_escapes_content_during_render()
    {
        $item = new MetaItem('description', 'test "content" & more');
        $expected = '<meta name="description" content="test &quot;content&quot; &amp; more">';

        $this->assertEquals($expected, $item->render(), 'HTML escaping failed.');
    }

    /**
     * Test that custom attributes can be added to the meta tag.
     */
    public function test_it_can_set_custom_attributes()
    {
        $item = new MetaItem('description', 'test');
        $item->setAttribute('id', 'meta-id');

        $this->assertStringContainsString('id="meta-id"', $item->render(), 'Custom attribute "id" is missing from rendered output.');
    }
}

<?php

namespace MetaSeo;

use Illuminate\Support\Arr;
use MetaSeo\Contracts\MetaItemInterface;
use MetaSeo\Items\OgMetaItem;
use MetaSeo\Items\TwitterMetaItem;
use MetaSeo\Enums\MetaField;

/**
 * Manages meta-tags for SEO and social media sharing purposes.
 *
 * The MetaManager provides a centralized way to manage all meta-tags required for proper
 * SEO optimization and social media sharing (OpenGraph, Twitter Cards). It handles:
 *
 * - Basic HTML meta-tags (title, description, robots)
 * - OpenGraph meta-tags for Facebook and other platforms
 * - Twitter Card meta-tags for Twitter sharing
 * - Fallback values when primary content is not available
 * - Grouping and ordered rendering of meta-tags
 * - Exclusion of specific tags from rendering
 *
 * The manager automatically synchronizes related meta-tags (e.g., setting the title
 * will also set og:title and twitter:title) to ensure consistency across all platforms.
 * It supports fallback values for when primary content is empty or not provided.
 *
 * Meta-tags are organized into groups ('basic', 'og', 'twitter') and rendered in a
 * specific order to maintain proper HTML structure and improve readability.
 */
class MetaManager
{
    /**
     * Additional meta items.
     *
     * @var array<int, MetaItemInterface>
     */
    protected array $items = [];

    /**
     * Keys that should not be rendered.
     */
    protected array $excluded_keys = ['title'];

    /**
     * Ordered a list of groups for rendering.
     */
    protected array $group_order = ['basic', 'og', 'twitter'];

    /**
     * Clear all registered meta-items and reset to the initial state.
     */
    public function clear(): self
    {
        $this->items = [];
        $this->excluded_keys = ['title'];

        return $this;
    }

    /**
     * Get the title meta-item.
     */
    public function title(): MetaItemInterface
    {
        return $this->item('title');
    }

    /**
     * Get the description meta-item.
     */
    public function description(): MetaItemInterface
    {
        return $this->item('description');
    }

    /**
     * Get or create a MetaItem by key.
     */
    public function item(string|MetaField $key, array $attributes = []): MetaItemInterface
    {
        [$className, $key] = MetaResolver::resolve($key);

        if (!isset($this->items[$key])) {
            $this->items[$key] = new $className($key);

            foreach ($attributes as $name => $value) {
                $this->items[$key]->setAttribute($name, $value);
            }
        }

        return $this->items[$key];
    }

    /**
     * Get or create an OgMetaItem by key.
     */
    public function ogItem(string|MetaField $key, ?string $content = null): OgMetaItem
    {
        $key = is_string($key) ? OgMetaItem::generateKey($key) : $key->value;

        if (!isset($this->items[$key])) {
            $this->items[$key] = new OgMetaItem($key, $content);
        }

        return $this->items[$key];
    }

    /**
     * Get or create a TwitterMetaItem by key.
     */
    public function twitterItem(string|MetaField $key, ?string $content = null): TwitterMetaItem
    {
        $key = is_string($key) ? TwitterMetaItem::generateKey($key) : $key->value;

        if (!isset($this->items[$key])) {
            $this->items[$key] = new TwitterMetaItem($key, $content);
        }

        return $this->items[$key];
    }

    /**
     * Add a pre-configured Meta item to the manager.
     */
    public function addItem(MetaItemInterface $item): self
    {
        $key = $item->getKey();

        if ($key) {
            $this->items[$key] = $item;
        }

        return $this;
    }

    /**
     * Set keys that should be excluded from rendering.
     */
    public function exclude(array|string|MetaField $keys): self
    {
        $this->excluded_keys = array_unique(array_merge(
            $this->excluded_keys,
            $this->transformKeys(Arr::wrap($keys))
        ));

        return $this;
    }

    /**
     * Remove keys from the excluded list so they can be rendered.
     */
    public function include(array|string|MetaField $keys): self
    {
        $this->excluded_keys = array_diff(
            $this->excluded_keys,
            $this->transformKeys(Arr::wrap($keys))
        );

        return $this;
    }

    /**
     * Transform keys to their normalized form.
     */
    protected function transformKeys(array $keys): array
    {
        return collect($keys)
            ->filter(fn ($key) => is_string($key) || $key instanceof MetaField)
            ->map(fn ($key) => $key instanceof MetaField ? $key->value : $key)
            ->all();
    }

    /**
     * Get all registered meta-items.
     *
     * @return array<int, MetaItemInterface>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Render all registered meta-tags grouped by their defined groups.
     *
     * By default, it follows the group order: basic, og, twitter.
     */
    public function renderItems(): string
    {
        return collect($this->items)
            ->reject(fn (MetaItemInterface $item, string $key) => in_array($key, $this->excluded_keys))
            ->groupBy(fn (MetaItemInterface $item) => $item->getGroup())
            ->sortBy(function ($group, $key) {
                $index = array_search($key, $this->group_order);
                return $index === false ? 999 : $index;
            })
            ->map(fn ($group) => $group->map->render())
            ->flatten()
            ->filter()
            ->implode(PHP_EOL);
    }

    /**
     * Synchronize a value across multiple meta items.
     */
    protected function syncValue(array $keys, ?string $value, ?string $fallback = null, bool $withFallback = false): self
    {
        foreach ($keys as $key) {
            $item = $this->item($key);

            $withFallback
                ? $item->setValueWithFallback($value, $fallback)
                : $item->setValue($value);
        }

        return $this;
    }

    /**
     * Set the primary meta-title and its OpenGraph/Twitter equivalents.
     */
    public function setTitle(string $title): self
    {
        return $this->syncValue(['title', MetaField::OgTitle, MetaField::TwitterTitle], $title);
    }

    /**
     * Set the primary meta-title and its equivalents with a fallback value.
     */
    public function setTitleWithFallback(string $title, ?string $fallback_title = null): self
    {
        return $this->syncValue(['title', MetaField::OgTitle, MetaField::TwitterTitle], $title, $fallback_title, true);
    }

    /**
     * Set a fallback title for when the primary title is empty.
     */
    public function fallbackTitle(string $title): self
    {
        foreach (['title', MetaField::OgTitle, MetaField::TwitterTitle] as $key) {
            $this->item($key)->setFallbackValue($title)->useFallbackValue();
        }

        return $this;
    }

    /**
     * Set the meta-description and its OpenGraph/Twitter equivalents.
     */
    public function setDescription(string $description): self
    {
        return $this->syncValue(['description', MetaField::OgDescription, MetaField::TwitterDescription], $description);
    }

    /**
     * Set the meta-description and its equivalents with a fallback value.
     */
    public function setDescriptionWithFallback(string $description, ?string $fallback_description = null): self
    {
        return $this->syncValue(['description', MetaField::OgDescription, MetaField::TwitterDescription], $description, $fallback_description, true);
    }

    /**
     * Set a fallback description for when the primary description is empty.
     */
    public function fallbackDescription(string $description): self
    {
        foreach (['description', MetaField::OgDescription, MetaField::TwitterDescription] as $key) {
            $this->item($key)->setFallbackValue($description)->useFallbackValue();
        }

        return $this;
    }

    /**
     * Set the canonical URL and its OpenGraph/Twitter equivalents.
     */
    public function setUrl(string $url): self
    {
        return $this->syncValue([MetaField::OgUrl, MetaField::TwitterUrl], $url);
    }

    /**
     * Set the social share image (OpenGraph and Twitter).
     */
    public function setImage(string $url): self
    {
        return $this->syncValue([MetaField::OgImage, MetaField::TwitterImage], $url);
    }

    /**
     * Set the social share image with a fallback value.
     */
    public function setImageWithFallback(string $url, ?string $fallback = null): self
    {
        return $this->syncValue([MetaField::OgImage, MetaField::TwitterImage], $url, $fallback, true);
    }

    /**
     * Set the noindex directive for search engine robots.
     *
     * @see https://developers.google.com/search/docs/crawling-indexing/block-indexing
     */
    public function noIndex(bool $status = true): self
    {
        $this->item('robots')->setValue($status ? 'noindex,nofollow' : 'index,follow');

        return $this;
    }
}

<?php

namespace MetaSeo;

use Illuminate\Support\Str;
use MetaSeo\Contracts\MetaItemInterface;
use MetaSeo\Enums\MetaField;

/**
 * Base class for all meta-tag items.
 *
 * Implements the core logic for managing HTML meta-tag attributes,
 * fallbacks, and rendering.
 */
abstract class AbstractMetaItem implements MetaItemInterface
{
    /**
     * The attributes of the meta tag.
     */
    protected array $attributes = [];

    /**
     * Backup values for attributes.
     */
    protected array $fallbacks = [];

    /**
     * Flags to determine whether to use fallback for specific attributes.
     */
    protected array $use_fallbacks = [];

    /**
     * Name of the primary attribute (e.g., 'name', 'property').
     */
    protected string $primaryAttributeName = 'name';

    /**
     * Name of the secondary attribute (usually 'content').
     */
    protected string $secondaryAttributeName = 'content';

    /**
     * Whether to force rendering even if the content is empty.
     */
    protected bool $force_render = false;

    /**
     * Create a new MetaItem instance.
     */
    public function __construct(string|MetaField $key = '', ?string $content = null)
    {
        if ($key) {
            $this->setAttribute(
                $this->getPrimaryAttributeName(),
                is_string($key) ? static::generateKey($key) : $key->value
            );
        }

        if ($content !== null) {
            $this->setAttribute($this->getSecondaryAttributeName(), $content);
        }
    }

    /**
     * Get the primary attribute name (e.g., 'name' or 'property').
     */
    public function getPrimaryAttributeName(): string
    {
        return $this->primaryAttributeName;
    }

    /**
     * Set the secondary attribute name (usually 'content').
     */
    public function setSecondaryAttributeName(string $name): MetaItemInterface
    {
        $this->secondaryAttributeName = $name;
        return $this;
    }

    /**
     * Get the secondary attribute name.
     */
    public function getSecondaryAttributeName(): string
    {
        return $this->secondaryAttributeName;
    }

    /**
     * Set the value for the secondary attribute (content).
     */
    public function setValue(?string $value): MetaItemInterface
    {
        return $this->setAttribute($this->getSecondaryAttributeName(), $value);
    }

    /**
     * Get the value of the secondary attribute (content).
     */
    public function getValue(): ?string
    {
        return $this->getAttribute($this->getSecondaryAttributeName());
    }

    /**
     * Set fallback for the secondary attribute (content).
     */
    public function setFallbackValue(?string $value): MetaItemInterface
    {
        return $this->setFallback($this->getSecondaryAttributeName(), $value);
    }

    /**
     * Enable or disable fallback for the secondary attribute (content).
     */
    public function useFallbackValue(bool $status = true): MetaItemInterface
    {
        return $this->useFallback($this->getSecondaryAttributeName(), $status);
    }

    /**
     * Set the secondary attribute (content) with fallback.
     */
    public function setValueWithFallback(?string $value, ?string $fallback = null): MetaItemInterface
    {
        return $this->setWithFallback($this->getSecondaryAttributeName(), $value ?? '', $fallback);
    }

    /**
     * Set a meta-tag attribute.
     */
    public function setAttribute(string $name, ?string $value): MetaItemInterface
    {
        $this->attributes[$name] = Str::squish($value);
        return $this;
    }

    /**
     * Set a fallback value for an attribute.
     */
    public function setFallback(string $name, ?string $value): MetaItemInterface
    {
        $this->fallbacks[$name] = Str::squish($value);
        return $this;
    }

    /**
     * Enable or disable fallback for a specific attribute.
     */
    public function useFallback(string $name, bool $status = true): MetaItemInterface
    {
        $this->use_fallbacks[$name] = $status;
        return $this;
    }

    /**
     * Set an attribute and its fallback at once.
     */
    public function setWithFallback(string $name, string $value, ?string $fallback = null): MetaItemInterface
    {
        return $this->setAttribute($name, $value)
            ->setFallback($name, $fallback ?? $value)
            ->useFallback($name);
    }

    /**
     * Get an attribute value, considering fallback if enabled.
     */
    public function getAttribute(string $name): ?string
    {
        $value = $this->attributes[$name] ?? null;

        if (empty($value) && ($this->use_fallbacks[$name] ?? false)) {
            return $this->fallbacks[$name] ?? null;
        }

        return $value;
    }

    /**
     * Get the group name for this meta-item (e.g., 'basic', 'og', 'twitter').
     */
    public function getGroup(): string
    {
        return 'basic';
    }

    /**
     * Get the identifier key for this meta-item.
     */
    abstract public function getKey(): ?string;

    /**
     * Generate a consistent key for meta-tags.
     */
    public static function generateKey(string $key): string
    {
        return $key;
    }

    /**
     * Force the meta-tag to be rendered even if its primary content is empty.
     */
    public function forceRender(bool $status = true): MetaItemInterface
    {
        $this->force_render = $status;
        return $this;
    }

    /**
     * Check if the meta-tag should be rendered even if the content is empty.
     */
    public function isForceRender(): bool
    {
        return $this->force_render;
    }

    /**
     * Render the meta-tag as an HTML string.
     */
    public function render(): string
    {
        if (!$this->shouldRender()) {
            return '';
        }

        $attributes = $this->getRenderableAttributes()
            ->map(fn ($value, $name) => sprintf('%s="%s"', $name, e($value)))
            ->implode(' ');

        return sprintf('<meta %s>', $attributes);
    }

    /**
     * Determine if the meta-tag should be rendered.
     */
    protected function shouldRender(): bool
    {
        return $this->isForceRender() || !empty($this->getValue());
    }

    /**
     * Get all attributes that should be included in the rendered tag.
     */
    protected function getRenderableAttributes(): \Illuminate\Support\Collection
    {
        return collect($this->attributes)
            ->merge($this->fallbacks)
            ->keys()
            ->unique()
            ->mapWithKeys(fn ($name) => [$name => $this->getAttribute($name)])
            ->filter(fn ($value) => $value !== null);
    }

    /**
     * Convert the meta-item to its string representation.
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * Handle the invocation of the meta-item.
     */
    public function __invoke(): string
    {
        return $this->render();
    }
}

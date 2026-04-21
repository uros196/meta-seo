<?php

namespace MetaSeo\Contracts;

/**
 * Interface for meta-tag items.
 *
 * Defines the contract for objects representing a single HTML meta-tag,
 * supporting attributes, fallback values, and conditional rendering.
 */
interface MetaItemInterface
{
    /**
     * Set an attribute value for the meta-tag.
     */
    public function setAttribute(string $name, ?string $value): self;

    /**
     * Get an attribute value, considering defined fallbacks if the primary value is empty.
     */
    public function getAttribute(string $name): ?string;

    /**
     * Set a fallback value for a specific attribute.
     *
     * The fallback is used when the primary attribute value is empty and useFallback() is enabled.
     */
    public function setFallback(string $name, ?string $value): self;

    /**
     * Enable or disable the use of a fallback for a specific attribute.
     */
    public function useFallback(string $name, bool $status = true): self;

    /**
     * Set both an attribute value and its fallback value at once.
     *
     * Automatically enables the fallback for this attribute.
     */
    public function setWithFallback(string $name, string $value, ?string $fallback = null): self;

    /**
     * Set the value for the secondary attribute, typically the 'content' attribute.
     */
    public function setValue(?string $value): self;

    /**
     * Get the value of the secondary attribute (content).
     */
    public function getValue(): ?string;

    /**
     * Set a fallback value for the secondary attribute (content).
     */
    public function setFallbackValue(?string $value): self;

    /**
     * Enable or disable the use of a fallback for the secondary attribute (content).
     */
    public function useFallbackValue(bool $status = true): self;

    /**
     * Set the secondary attribute (content) with a fallback value.
     */
    public function setValueWithFallback(?string $value, ?string $fallback = null): self;

    /**
     * Get the group name this meta-item belongs to (e.g., 'basic', 'og', 'twitter').
     */
    public function getGroup(): string;

    /**
     * Get the identifier key for this meta-item (e.g., the value of 'name' or 'property').
     */
    public function getKey(): ?string;

    /**
     * Set whether the meta-tag should be rendered even if its primary content is empty.
     */
    public function forceRender(bool $status = true): self;

    /**
     * Check if the meta-tag is set to be rendered even if its primary content is empty.
     */
    public function isForceRender(): bool;

    /**
     * Render the meta-item as an HTML string.
     */
    public function render(): string;
}

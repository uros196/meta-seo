<?php

namespace MetaSeo\Enums;

use MetaSeo\Contracts\MetaItemInterface;
use MetaSeo\MetaResolver;

enum MetaField: string
{
    // Name of the base meta-tags
    case Title = 'title';
    case Description = 'description';

    // Properties of meta-tags for Open Graph protocol
    case OgTitle = 'og:title';
    case OgDescription = 'og:description';
    case OgImage = 'og:image';
    case OgUrl = 'og:url';
    case OgType = 'og:type';
    case OgSiteName = 'og:site_name';
    case OgLocale = 'og:locale';
    case OgLatitude = 'og:latitude';
    case OgLongitude = 'og:longitude';
    case OgStreetAddress = 'og:street-address';
    case OgLocality = 'og:locality';
    case OgRegion = 'og:region';
    case OgPostalCode = 'og:postal-code';
    case OgCountryName = 'og:country-name';
    case OgStartTime = 'og:start_time';
    case OgEndTime = 'og:end_time';

    // Names of meta-tags for Twitter cards
    case TwitterCard = 'twitter:card';
    case TwitterSite = 'twitter:site';
    case TwitterCreator = 'twitter:creator';
    case TwitterTitle = 'twitter:title';
    case TwitterDescription = 'twitter:description';
    case TwitterImage = 'twitter:image';
    case TwitterUrl = 'twitter:url';

    /**
     * Resolve the MetaField to its corresponding MetaItem class name.
     */
    public function resolveItem(): string
    {
        [$className] = MetaResolver::resolve($this);
        return $className;
    }

    /**
     * Resolve the MetaField to its corresponding MetaItem instance.
     */
    public function buildItem(): MetaItemInterface
    {
        $className = $this->resolveItem();
        return new $className($this->value);
    }
}

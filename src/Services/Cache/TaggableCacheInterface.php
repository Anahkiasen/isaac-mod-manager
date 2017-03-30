<?php

namespace Isaac\Services\Cache;

use Psr\SimpleCache\CacheInterface;

/**
 * Extension of the PSR CacheInterface with barebones support for tags.
 */
interface TaggableCacheInterface extends CacheInterface
{
    /**
     * Set an item with one or more specific tags.
     *
     * @param string               $key
     * @param mixed                $value
     * @param string|string[]|null $tags
     * @param int|null             $ttl
     *
     * @return bool
     */
    public function setWithTags(string $key, $value, $tags = null, int $ttl = null): bool;

    /**
     * Invalidates cached items using a tag.
     *
     * @param string $tag
     *
     * @return bool
     */
    public function invalidateTag(string $tag): bool;
}

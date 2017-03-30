<?php

namespace Isaac\Services\Cache;

use Cache\Bridge\SimpleCache\SimpleCacheBridge;
use Cache\Taggable\TaggablePSR6PoolAdapter;
use Cache\TagInterop\TaggableCacheItemPoolInterface;
use Psr\Cache\CacheItemPoolInterface;

class TaggableSimpleCacheBridge extends SimpleCacheBridge implements TaggableCacheInterface
{
    /**
     * @var TaggableCacheItemPoolInterface
     */
    protected $cacheItemPool;

    /**
     * {@inheritdoc}
     */
    public function __construct(CacheItemPoolInterface $cacheItemPool)
    {
        parent::__construct(TaggablePSR6PoolAdapter::makeTaggable($cacheItemPool));
    }

    /**
     * {@inheritdoc}
     */
    public function setWithTags(string $key, $value, $tags = null, int $ttl = null): bool
    {
        $item = $this->cacheItemPool->getItem($key);
        $item->expiresAfter($ttl);
        $item->setTags((array) $tags);
        $item->set($value);

        return $this->cacheItemPool->save($item);
    }

    /**
     * {@inheritdoc}
     */
    public function invalidateTag(string $tag): bool
    {
        if (!$this->cacheItemPool instanceof TaggableCacheItemPoolInterface) {
            return true;
        }

        return $this->cacheItemPool->invalidateTag($tag);
    }
}

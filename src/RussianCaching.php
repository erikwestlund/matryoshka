<?php

namespace Laracasts\Matryoshka;

use Illuminate\Contracts\Cache\Repository as Cache;

class RussianCaching
{
    /**
     * The cache repository.
     *
     * @var Cache
     */
    protected $cache;

    /**
     * Create a new class instance.
     *
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Put to the cache.
     *
     * @param string $key
     * @param string $fragment
     */
    public function put($key, $fragment)
    {
        $key = $this->normalizeCacheKey($key);

        return $this->cache
            ->tags('views')
            ->rememberForever($key, function () use ($fragment) {
                return $fragment;
            });
    }

    /**
     * Check if the given key exists in the cache.
     *
     * @param string $key
     */
    public function has($key)
    {
        $key = $this->normalizeCacheKey($key);

        return $this->cache
            ->tags('views')
            ->has($key);
    }

    /**
     * Normalize the cache key.
     *
     * @param string $key
     */
    protected function normalizeCacheKey($key)
    {
        if ($key instanceof \Illuminate\Database\Eloquent\Model) {
            return $key->getCacheKey();
        }

        return $key;
    }
}

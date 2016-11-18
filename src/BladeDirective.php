<?php

namespace Laracasts\Matryoshka;

use Exception;

class BladeDirective
{
    /**
     * The cache instance.
     *
     * @var RussianCaching
     */
    protected $cache;

    /**
     * A list of model cache keys.
     *
     * @param array $keys
     */
    protected $keys = [];

    /**
     * Create a new instance.
     *
     * @param RussianCaching $cache
     */
    public function __construct(RussianCaching $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Handle the @cache setup.
     *
     * @param mixed       $model
     * @param string|null $key
     */
    public function setUp($model, $key = null)
    {
        ob_start();

        $this->keys[] = $key = $this->normalizeKey($model, $key);

        return $this->cache->has($key);
    }

    /**
     * Handle the @endcache teardown.
     */
    public function tearDown()
    {
        return $this->cache->put(
            array_pop($this->keys), ob_get_clean()
        );
    }

    /**
     * Normalize the cache key.
     *
     * @param mixed       $item
     * @param string|null $key
     */
    protected function normalizeKey($item, $key = null)
    {
        // If the user wants to provide their own cache
        // key, we'll opt for that.
        if (is_string($item) || is_string($key)) {
            return is_string($item) ? $item : $key;
        }
        
        // Otherwise we'll try to use the item to calculate
        // the cache key, itself.
        if (is_object($item) && method_exists($item, 'getCacheKey')) {
            return $item->getCacheKey();
        }
    
        // If a paginated object, return a key consisting of the model type, 
        // the page, items per page, and a hash of the items in the page.
        if($item instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $items = collect($item->items());
            $model = get_class($items->first());
            $per_page = 'per:' . $item->perPage();
            $page = 'pg:' . $item->currentPage();
            $hash = md5($items);

            return implode('-', [$model, $page, $per_page, $hash]);
        }

        // If we're dealing with a collection, we'll 
        // use a hashed version of its contents.
        if ($item instanceof \Illuminate\Support\Collection) {
            return md5($item);
        }
    
        throw new Exception('Could not determine an appropriate cache key.');
    }
}   
<?php

namespace Laracasts\Matryoshka;

trait Cacheable
{

    /**
     * Calculate a unique cache key for the model instance.
     * 
     * @param  int $page a page number for pagination
     * @return string
     */
    public function getCacheKey($page = null, $query = null)
    {
        $key = sprintf("%s/%s-%s",
            get_class($this),
            $this->getKey(),
            $this->updated_at->timestamp
        );

        $suffixes = $this->getSuffixes($page, $query);

        return $suffixes ? $key . '-' . $suffixes : $key;
    }

    protected function getSuffixes($page, $query, $suffixes)
    {
        $key_parts = [];

        if($page) {
            $key_parts[] = 'pg:' . $page;
        }

        if($query) {
            $key_parts[] = 'query:' . md5($query);
        }

        return implode('-', $key_parts);
    }
}

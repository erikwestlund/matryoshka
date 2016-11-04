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
    public function getCacheKey($page = null)
    {
        $key = sprintf("%s/%s-%s",
            get_class($this),
            $this->getKey(),
            $this->updated_at->timestamp
        );

        return $page ? $key . '-pg:' . $page : $key;
    }
}

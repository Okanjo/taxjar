<?php
/**
 * Date: 11/21/14 3:09 PM
 *
 * ----
 *  
 * (c) Okanjo Partners Inc
 * https://okanjo.com
 * support@okanjo.com
 * 
 * https://github.com/okanjo/taxjar
 * 
 * ----
 * 
 * TL;DR? see: http://www.tldrlegal.com/license/mit-license
 * 
 * The MIT License (MIT)
 * Copyright (c) 2013 Okanjo Partners Inc.
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace TaxJar\Api\Providers;

use TaxJar\Api\Client;
use TaxJar\Api\Query\QueryBase;

/**
 * Base class for cache providers, must be extended in order to be used.
 * @package TaxJar\Api\Provider
 */
abstract class CacheProvider extends Provider {

    // Cache key options
    protected $key_prefix = null;
    protected $key_suffix = null;

    // Constants for parameters
    const KEY_PREFIX = 'keyPrefix';
    const KEY_SUFFIX = 'keySuffix';

    // Constants for TTL
    const TIME_NEVER_EXPIRE = 0;
    const TIME_MINUTE = 60;
    const TIME_HOUR = 3600;
    const TIME_DAY = 86400;
    const TIME_WEEK = 604800;
    const TIME_MONTH = 2628000;
    const TIME_YEAR = 31536000;

    /**
     * Creates a generic instance of a cache provider. Expects to be overridden.
     * @param Client $api
     * @param array $args
     */
    public function __construct(Client $api, $args = array()) {
        $this->_api = $api;
        $this->key_prefix = $this->getFromArgs(self::KEY_PREFIX, $args, '');
        $this->key_suffix = $this->getFromArgs(self::KEY_SUFFIX, $args, '');
    }


    /**
     * Gets the key from the cache, or it sets they key via the return of the callable.
     *
     * @param $key - Key to fetch
     * @param $ttl - Time in seconds for the key to exist
     * @param callable $whatToCacheIfNotFound - The callback function which must return a string value to cache.
     * @return string - The cached value
     */
    abstract function getOrSet($key, $ttl, Callable $whatToCacheIfNotFound);

    /**
     * Sets the given key/value in the cache
     *
     * @param $key - The unique cache key
     * @param $ttl - Time in seconds for the key to exist
     * @param $string - The string value to cache
     */
    abstract function set($key, $ttl, $string);


    /**
     * Removes a key from the cache
     * @param $key - Key to remove
     */
    abstract function purge($key);


    /**
     * Generate a cache key based on signature given
     * @param string $input - Base cache key
     * @return string - Final cache key (with prefix/suffix)
     */
    protected function generateKey($input) {
        $key = $this->key_prefix . $input . $this->key_suffix;
        return $key;
    }

    /**
     * Compiles a request into a cache key
     * @param $path - URI query path
     * @param QueryBase $query - Instance of a QueryBase / child
     * @return string - Cache key unique to the query given
     */
    protected function compileKey($path, $query) {
        $data = $path;
        if(is_array($query)) {

            // Sort params, then hash it
            ksort($query);
            $data .= '_' . md5(serialize($query));

        }
        return $data;
    }

    /**
     * Determines real Time To Live based on input
     * @param int $ttl - Time To Live (seconds)
     * @return int - Time to live in seconds)
     */
    protected function determineTTL($ttl) {
        $cacheTTL = intval($ttl);

        if ($cacheTTL < 0)
            $cacheTTL = 0;

        return $cacheTTL;
    }

    /**
     * Gets the value of an argument or the default value if not set
     * @param $key - Key of the argument to fetch
     * @param $array - The associative argument pool
     * @param null $defaultValue - The default value to use if the argument is not set
     * @return mixed - The value of the given key
     */
    protected function getFromArgs($key, $array, $defaultValue = null) {
        return array_key_exists($key, $array) ? $array[$key] : $defaultValue;
    }
} 
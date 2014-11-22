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

namespace TaxJar\Api\Query;
use TaxJar\Api\Providers\CacheProvider;
use TaxJar\Api\Response;

/**
 * Query collection routes
 * @package TaxJar\Api\Query
 */
class CollectionQuery extends QueryBase {


    /**
     * Filter the response to include results that match the given criteria
     * @param array $args - Associative array of name => value pairs to limit results with
     * @return $this
     */
    public function where(Array $args) {
        return $this->_where($args);
    }


    /**
     * Sends the request to the API and retrieves the response
     * @param bool $associativeDataMode - Optional, Whether to decode the data as an associative array (True) or as an object (False)
     * @param array $headers - Optional, Associative array of name => value headers to send along to the API
     * @return Response - The API response object
     */
    public function execute($associativeDataMode = true, $headers = array()) {
        return $this->_execute($associativeDataMode, $headers);
    }


	/**
	 * Returns the cached response from the API, and if not cached, performs Execute and caches the response
	 * @param int $ttl - Optional, The number of seconds to hold the response in cache. Defaults to forever.
	 * @param bool $associativeDataMode - Optional, Whether to decode the data as an associative array (True) or as an object (False)
	 * @param array $headers - Optional, Associative array of name => value headers to send along to the API
	 * @return Response - The API response object
	 */
	public function cachedExecute($ttl = CacheProvider::TIME_NEVER_EXPIRE, $associativeDataMode = true, $headers = array()) {
		return $this->_cachedExecute($ttl, $associativeDataMode, $headers);
	}
} 
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

use TaxJar\Api\Client,
    TaxJar\Exceptions\InvalidQueryException,
    TaxJar\Api\Providers\CacheProvider,
    TaxJar\Exceptions\InvalidProviderException;

/**
 * Base query - Translates query into request and facilitates communication with the API
 * @package TaxJar\Api\Query
 */
abstract class QueryBase {

    // Constants for HTTP Response Code categories
    const HTTP_INFO = '1XX';
    const HTTP_SUCCESS = '2XX';
    const HTTP_REDIRECT = '3XX';
    const HTTP_USER_ERROR = '4XX';
    const HTTP_SYS_ERROR = '5XX';

    // Constant for JSON mime-type
    const MIME_JSON = 'application/json';

    // Constants for query arguments
    const QUERY_METHOD = 'method';
    const QUERY_PATH = 'path';
    const QUERY_ENTITY = 'entity';

    /** @var null|Client */
    protected $_api = null;
    protected $_method = null;
    protected $_path = null;
    protected $_query = array();
    protected $_entity = "";

    /**
     * Creates a new instance of a query
     * @param Client $api - The client API handling the request
     * @param array $args - Query arguments
     * @throws \TaxJar\Exceptions\InvalidQueryException - Occurs when an invalid argument is given
     */
    public function __construct(Client $api, Array $args) {
        // Set API instance
        if (empty($api)) {
            throw new InvalidQueryException("Api must be defined");
        } else {
            $this->_api = $api;
        }

        if (!is_array($args)) {
            throw new InvalidQueryException("Arguments must be an array");
        }

        // Set Initial params
        foreach($args as $key => $val) {
            if ($key == self::QUERY_METHOD) {
                $this->_setMethod($val);
            } else if ($key == self::QUERY_PATH) {
                $this->_setPath($val);
            } else if ($key == self::QUERY_ENTITY) {
                $this->_setEntityBody($val);
            } else {
                error_log("[TaxJar] Unknown query argument: $key");
            }
        }
    }


    /**
     * Internal - Sets the query HTTP method (e.g. GET PUT POST DELETE)
     * @param string $val - HTTP Method (use Client::GET/PUT/POST/DELETE)
     * @throws \TaxJar\Exceptions\InvalidQueryException - Occurs when an invalid method was given
     */
    protected function _setMethod($val) {
        if ($val == Client::GET || $val == Client::PUT || $val == Client::POST || $val == Client::DELETE) {
            $this->_method = $val;
        } else {
            throw new InvalidQueryException("Invalid query method: $val");
        }
    }


    /**
     * Internal - Sets the query URI route
     * @param string $val - The URI route to execute - Use \TaxJar\Api\Common\Routes::_NAME_ common constant
     */
    protected function _setPath($val) {
        $this->_path = $val;
    }


    /**
     * Internal - Sets the query-string URI arguments
     * @param array $val - Associative array of arguments
     * @throws \TaxJar\Exceptions\InvalidQueryException - Occurs when the given argument set is not valid
     */
    protected function _setQueryArgs($val) {
        if(is_array($val)) {
            $this->_query = array_merge($this->_query, $val);
        } else {
            throw new InvalidQueryException("Query must be an array.");
        }
    }


    /**
     * Internal - Sets the arguments in the entity body
     * @param array $val - Associative array of arguments
     */
    protected function _setEntityArgs($val) {
        if(is_array($val)) {
            // Initialize to an array if it's not already an array
            if (!is_array($this->_entity)) { $this->_entity = array(); }

            // Merge data params
            $this->_entity = array_merge($this->_entity, $val);
        }
    }


    /**
     * Internal - Sets the request's entity body
     * @param string|array $val - Accepts one of: raw string, associative array of arguments, or instance of a FileUpload class
     * @throws \TaxJar\Exceptions\InvalidQueryException - Occurs when the value given is not valid
     */
    protected function _setEntityBody($val) {
        if(is_string($val) || (is_object($val))) {
            $this->_entity = $val;
        } else if (is_array($val)) {
            $this->_setEntityArgs($val);
        } else {
            throw new InvalidQueryException("Entity must be a string, array.");
        }
    }


    /**
     * Internal - Assigns filter criteria to the URI query-string
     * @param array $args - Associative array of conditions
     * @return $this - Updated query
     */
    protected function _where(Array $args) {
        $this->_setQueryArgs($args);
        return $this;
    }


    /**
     * Internal - Assigns entity data to the request
     * @param string|array $paramsOrBody - Accepts one of: raw string, associative array of arguments, or instance of a FileUpload class
     * @return $this - Updated query
     */
    protected function _data($paramsOrBody) {
        $this->_setEntityBody($paramsOrBody);
        return $this;
    }


    /**
     * Internal - Compiles and sends the request to the TaxJar API
     * @param bool $associativeDataMode - Optional, Whether to decode the response data as an associative array or as an object.
     * @param array $headers - Optional, Associative array of headers to send in the request
     * @return \TaxJar\Api\Response - The response from the TaxJar API
     */
    protected function _execute($associativeDataMode = true, $headers = array()) {

        // Set the api token
        $headers['Authorization'] = sprintf('Token token="%s"', $this->_api->apiToken);

        // Set User Agent
        $headers['User-Agent'] = Client::USER_AGENT;

        // Gather query data
	    $data = array(
		    'associativeDataMode' => $associativeDataMode,
		    'headers' => $headers,
		    'query' => $this->_query,
		    'entity' => $this->_entity,
		    'path' => $this->_path,
		    'method' => $this->_method
	    );

        // Send it to the default communication provider
	    $response = $this->_api->providers[Client::PROVIDER_NAME_DEFAULT]->execute($data);

        // Return the response
	    return $response;
    }


    /**
     * Internal - Returns the response for the request from the cache, or fetches the response from the API, caches and returns the response
     * @param int $ttl - Optional, The time in seconds the response should be cached for. Defaults to forever.
     * @param bool $associativeDataMode - Optional, Whether to decode the response data as an associative array or as an object.
     * @param array $headers - Optional, Associative array of headers to send in the request
     * @return \TaxJar\Api\Response - The response from the TaxJar API
     * @throws \TaxJar\Exceptions\InvalidProviderException - Occurs when the cache provider was not added to the client api
     */
    protected function _cachedExecute($ttl = CacheProvider::TIME_NEVER_EXPIRE, $associativeDataMode = true, $headers = array()) {

        // Check if the cache provider was added to the client api instance
        if(!array_key_exists(Client::PROVIDER_NAME_CACHE, $this->_api->providers)) {
			throw new InvalidProviderException('Cache provider not defined');
		}

        // Gather query data
		$data = array(
			'query' => $this->_query,
			'path' => $this->_path,
			'ttl' => $ttl,
			'callable' => function(&$response = null) use ($associativeDataMode, $headers) {
                $response = $this->_execute($associativeDataMode, $headers);
				return serialize($response);
			}
		);

        // Send it to cache provider
		$response = $this->_api->providers[Client::PROVIDER_NAME_CACHE]->execute($data);

        // Return the cached response
		return $response;
	}
}
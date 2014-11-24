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

namespace TaxJar\Api;

use TaxJar\Api\Common\Fields;
use TaxJar\Api\Common\Routes;
use TaxJar\Api\Providers\Provider;
use TaxJar\Api\Providers\StreamingProvider;
use TaxJar\Api\Query\CollectionQuery;
use TaxJar\Api\Query\QueryBase;
use TaxJar\Api\Query\ResourceQuery;
use TaxJar\Exceptions\MissingArgumentException;

class Client {

    // Constants for HTTP methods
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const DELETE = 'DELETE';

    // Constants for client arguments
    const API_ENDPOINT = 'endpoint';
    const API_TOKEN = 'token';

    const PROVIDER = 'provider';
    const PROVIDER_NAME_CACHE = 'cache';
    const PROVIDER_NAME_DEFAULT = 'default';
    const USER_AGENT = 'OkanjoTaxJarSdk/1.0';

    // Client options
    public $apiToken = null;
    public $apiEndpoint = "https://api.taxjar.com";

    /**
     * @var Provider[]
     */
    public $providers = array();

    /**
     * @var bool - Whether to send your client's IP as an X-FORWARDED-FOR header
     * For example, X-FORWARDED-FOR => [your client's ip], [your proxy ips, ...]
     * It's recommended you leave this enabled, so we can offer better customer support
     */
    public $enable_X_Forwarded_For = true;

    /**
     * Creates a new instance of the API client
     * @param string|array $args - API configuration, requires `key`, `passphrase`, optionally `endpoint`, `usertoken`
     * @throws \TaxJar\Exceptions\MissingArgumentException - Occurs when `key` or `passphrase` is not given
     */
    public function __construct($args) {

        if (is_array($args)) {
            // Process arguments
            foreach($args as $key => $val) {
                if ($key == self::API_ENDPOINT) {
                    $this->apiEndpoint = $val;
                } else if ($key == self::API_TOKEN) {
                    $this->apiToken = $val;
                }
            }
        } else if (is_string($args)) {
            $this->apiToken = $args;
        } else {
            throw new MissingArgumentException("API `".self::API_TOKEN."` argument required.");
        }

        // Verify required arguments
        if (empty($this->apiToken)) {
            throw new MissingArgumentException("API `".self::API_TOKEN."` argument required.");
        }

        // Set the default live data provider
        $this->setDefaultProvider(new StreamingProvider($this));
    }


    /**
     * Sets the default communication provider
     * @param Provider $provider - Instance of a Provider class
     */
    public function setDefaultProvider($provider) {
        $this->providers[self::PROVIDER_NAME_DEFAULT] = $provider;
    }


    /**
     * Adds a provider to the provider to the client
     * @param $name - Name of the provider. Must be unique.
     * @param Provider $provider - The instance of a Provider class to add
     */
    public function addProvider($name, $provider) {
        $this->providers[$name] = $provider;
    }


    /**
     * Removes a provider from the client
     * @param $name - Name of the provider to remove
     */
    public function deleteProvider($name) {
        unset($this->providers[$name]);
    }

    // -----------------------------------------------------------------------------------------------------------------


    public function getSalesTax() {
        return new CollectionQuery($this, array(
            QueryBase::QUERY_METHOD => self::GET,
            QueryBase::QUERY_PATH => Routes::SALES_TAX
        ));
    }

    public function getTaxRate($zip, $city_name = null, $country = null) {
        if (!empty($city_name)) {
            $q = new CollectionQuery($this, array(
                QueryBase::QUERY_METHOD => self::GET,
                QueryBase::QUERY_PATH => sprintf(Routes::LOCATIONS_ZIP_CITY, urlencode($zip), urlencode($city_name))
            ));
        } else {
            $q = new CollectionQuery($this, array(
                QueryBase::QUERY_METHOD => self::GET,
                QueryBase::QUERY_PATH => sprintf(Routes::LOCATIONS_ZIP, urlencode($zip))
            ));
        }

        if (!empty($country)) {
            return $q->where(array(Fields::COUNTRY => $country));
        } else {
            return $q;
        }

    }


}
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
use TaxJar\Api\Response;

/**
 * Query controller routes
 * @package TaxJar\Api\Query
 */
class ControllerQuery extends QueryBase {


    /**
     * Attach an associative array of name => value pairs in the request body
     * @param array $params - Associative array of name => value pairs
     * @return $this
     */
    public function data($params) {
        return $this->_data($params);
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


    // Notice, there's no CachedExecute here!
    // Controller requests should generally not be cached, because they are to perform actions on the API, so with
    // bypassing the api and returning the response, you've got a broken app!
} 
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
use TaxJar\Api\Common\Helpers;
use TaxJar\Api\Query\QueryBase;
use TaxJar\Api\Response;
use TaxJar\Exceptions\ResponseDecodeException;

/**
 * Live-Data Provider, using php streams to communicate directly with the TaxJar API
 * @package TaxJar\Api\Provider
 */
class StreamingProvider extends Provider {

    // API options
    protected $host;
	protected $token;


    /**
     * Creates a new instance of the live data streaming provider
     * @param Client $api - Client api instance handling the request
     * @param array $args - Provider arguments
     * @throws \Exception - Thrown when the API is not valid
     */
    public function __construct(Client $api, $args = array()) {
		if(empty($api)) {
			throw new \Exception('Invalid api.');
		} else {
			$this->_api = $api;
		}
	}


    /**
     * Executes the request
     * @param array $args - Request arguments
     * @return Response - API Response object
     */
    public function execute(Array $args) {

        // Get options and query args
		$associativeDataMode = array_key_exists('associativeDataMode', $args) ? $args['associativeDataMode'] : false;
		$headers = array_key_exists('headers', $args) ? $args['headers'] : array();
		$query = array_key_exists('query', $args) ? $args['query'] : array();
		$entity = array_key_exists('entity', $args) ? $args['entity'] : null;
		$path = array_key_exists('path', $args) ? $args['path'] : '';
		$method = array_key_exists('method', $args) ? $args['method'] : '';

		// Determine the entity body and request content type based on what we were given
		$contentType = null;
		$data = "";
		if (!empty($entity)) {
			if (is_array($entity)) { // NVPs
				$data = http_build_query($entity);
				$contentType = 'application/x-www-form-urlencoded; charset=UTF-8';
			} else if (is_string($entity)) { // JSON
				$contentType = 'application/json';
				$data = $entity;
			}
		}

		// Attach headers
		if ($this->_api->enable_X_Forwarded_For) {
			$headers['X-FORWARDED-FOR'] = Helpers::getClientIp();
		}

		$apiHost = $this->_api->apiEndpoint;
		preg_match('/^.+?\:\/\/(.+)$/', $this->_api->apiEndpoint, $matches);
		if (count($matches) == 2) {
			$apiHost = $matches[1];
		}

		$headers['Host'] = $apiHost;

		// Generate URI Query
        $uri = $path . (!empty($query) ? '?' . http_build_query($query) : "");

		// Finalize URI with endpoint
		$uri = $this->_api->apiEndpoint . $uri;

        // Uncomment this if you want to peek at the final request being hit
		//error_log("$uri data [".(is_array($entity) ? http_build_query($entity) : gettype($entity))."] type [$contentType], assocMode [$associativeDataMode] ");

        // Fire in the hole
		$response = $this->sendStream($apiHost, $uri, $method, $data, $contentType, $associativeDataMode, $headers);

        // Return the response
		return $response;
	}

	/**
	 * Because cURL was not low-level enough, now we're talking to the API with raw streams!
	 *
	 * @param string $host - Hostname, SSL-SNI workaround
	 * @param string $uri - Complete URI to request
	 * @param string $type - HTTP method
	 * @param bool $assocMode - Optional, whether the decode the response data as an associative array or as an object by default
	 * @param string $entity - Optional, HTTP entity message (e.g. key=val... or { json: object } or multi-part message )
	 * @param string|null $contentType - Optional, The type of entity being posted (only used when entity is given)
	 * @param array $headers - Optional, associative array of additional headers to pass along
	 *
	 * @throws \TaxJar\Exceptions\ResponseDecodeException - Thrown when the response could not be decoded
	 * @return Response - Response object
	 */
	protected function sendStream($host, $uri, $type, $entity = null, $contentType = null, $assocMode = true, $headers = array()) {

		// Define our wrapper, tossing in request type
		$http = array('method' => $type, 'ignore_errors' => true);

		// Add POST info if given
		if ($entity != null) {
			$http['content'] = $entity;
			if ($contentType != null) {
				$headers['Content-Type'] = $contentType;
			}
		}

		// Set arcane stream options
		$params = array(
			'verify_peer' => true,
			'SNI_enabled' => true
		);

        // SNI workaround
		if (!empty($host))
			$params['SNI_server_name'] = $host;

		// If we have headers, implode and include them
		if (!empty($headers)) {

			$headerLines = array();
			foreach($headers as $key => $val) {
				$headerLines[] = "$key: $val";
			}

			$http['header'] = implode("\r\n", $headerLines);
		}

		// Pass all these options in and create a stream context
		// NOTE: Must be http wrapper - the URL will determine
		$stream_context = stream_context_create(array('http' => $http), $params);

		// Open a new stream to the URL, with the given context
		$stream_handle = fopen($uri, 'rb', false, $stream_context);

		// Get the metadata for our request (headers are in here somewhere)
		$stream_metadata = stream_get_meta_data($stream_handle);

        // Create the response object
		$response = new Response();

		// Read the contents of the response from the stream
		$stream_response = @stream_get_contents($stream_handle);
		$response->raw = $stream_response;

		// Grab the HTTP response from the stream metadata
		$http_response = $stream_metadata['wrapper_data'][0];

		// Get the HTTP response code
		$http_response_array = explode(' ', $http_response);
		$response->status = intval($http_response_array[1]);

		// Splice off the rest into the header array
		$arrHeaders = array_splice($stream_metadata['wrapper_data'], 1);
		foreach ($arrHeaders as $header) {

			// Add response headers to an associative collection
			$parts = explode(':', $header, 2);
			if (count($parts) == 2) {
				$response->headers[strtoupper(trim($parts[0]))] = trim($parts[1]);

				// Try to grab the content type
				if (trim(strtolower($parts[0])) == 'content-type')
					$response->contentType = trim($parts[1]);
			}
		}

        // Return the response
		try {

			// If response was JSON, then attempt to decode it
			if (strpos($response->contentType, QueryBase::MIME_JSON) === 0) {
				// Decode the JSON object and return it
				$response->data = json_decode($response->raw, $assocMode);
			} else {

                // Try converting it anyway, in-case a naughty proxy screwed with the request or server hard failed
                try {
                    error_log('[TaxJar] WARNING: Response content-type came back '.$response->contentType.' trying to convert response to JSON anyway...');
                    $response->data = json_decode($response->raw, $assocMode);

                    $decodeErrCode = json_last_error();
                    if ($decodeErrCode != JSON_ERROR_NONE) {
                        error_log('[TaxJar] ERROR: JSON decode failed with code: '.$decodeErrCode);
                    }
                } catch (\Exception $e) {
                    error_log('[TaxJar] ERROR: Failed to try to json_decode the API response!');
                }
            }

            // Send the response back as-is
            return $response;
		} catch (\Exception $e) {
			throw new ResponseDecodeException("Failed to decode response", 0, $e);
		}
	}
}
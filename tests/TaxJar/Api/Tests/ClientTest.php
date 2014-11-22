<?php
/**
 * Date: 11/22/14 12:16 AM
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

use TaxJar\Api\Client;
use TaxJar\Exceptions\MissingArgumentException;

class ClientTest extends PHPUnit_Framework_TestCase {

    public static function getInstance() {
        $api = new Client(\TaxJar\Api\Tests\UnitTestConfig::$api);
        self::assertNotNull($api);
        return $api;
    }

    public function testEmptyConfig() {

        // Make sure an empty array is not accepted
        try {
            new Client(array());
            $this->fail('Exception was not thrown. Expected: MissingArgumentException');
        } catch (MissingArgumentException $e) {
            $this->assertNotEmpty($e->getMessage());
        } catch (\Exception $e) {
            $this->fail('Incorrect exception thrown, expected: MissingArgumentException');
        }

        // Make sure an empty string is not accepted
        try {
            new Client("");
            $this->fail('Exception was not thrown. Expected: MissingArgumentException');
        } catch (MissingArgumentException $e) {
            $this->assertNotEmpty($e->getMessage());
        } catch (\Exception $e) {
            $this->fail('Incorrect exception thrown, expected: MissingArgumentException');
        }
    }

    public function testByString() {
        $api = new Client("asdf");
        $this->assertInstanceOf("TaxJar\\Api\\Client", $api);
    }

    public function testByParams() {
        $api = new Client(array(Client::API_TOKEN => "asdf"));
        $this->assertInstanceOf("TaxJar\\Api\\Client", $api);
    }

}
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


class LocationsTest extends PHPUnit_Framework_TestCase {

    public function testZip() {
        $api = ClientTest::getInstance();
        $res = $api->getTaxRate('53132')->execute();

        $this->assertNotEmpty($res);
        $this->assertInstanceOf("TaxJar\\Api\\Response", $res);
        $this->assertEquals(\TaxJar\Api\Response::HTTP_OK, $res->status);

        $this->assertNotEmpty($res->data);
        $this->assertArrayHasKey('location', $res->data);

        $this->assertNotEmpty($res->data);
        $this->assertArrayHasKey('combined_rate', $res->data['location']);
        $this->assertEquals('HALES CORNERS', $res->data['location']['city']);
        $this->assertGreaterThan(0, $res->data['location']['combined_rate']);

    }

    public function testZipCity() {
        $api = ClientTest::getInstance();
        $res = $api->getTaxRate('53132', 'franklin')->execute();

        $this->assertNotEmpty($res);
        $this->assertInstanceOf("TaxJar\\Api\\Response", $res);
        $this->assertEquals(\TaxJar\Api\Response::HTTP_OK, $res->status);

        $this->assertNotEmpty($res->data);
        $this->assertArrayHasKey('location', $res->data);

        $this->assertNotEmpty($res->data);
        $this->assertArrayHasKey('combined_rate', $res->data['location']);
        $this->assertEquals('FRANKLIN', $res->data['location']['city']);
        $this->assertGreaterThan(0, $res->data['location']['combined_rate']);

    }

    public function testZipCityCountry() {
        $api = ClientTest::getInstance();
        $res = $api->getTaxRate('53132', 'franklin', 'us')->execute();

        $this->assertNotEmpty($res);
        $this->assertInstanceOf("TaxJar\\Api\\Response", $res);
        $this->assertEquals(\TaxJar\Api\Response::HTTP_OK, $res->status);

        $this->assertNotEmpty($res->data);
        $this->assertArrayHasKey('location', $res->data);

        $this->assertNotEmpty($res->data);
        $this->assertArrayHasKey('combined_rate', $res->data['location']);
        $this->assertEquals('FRANKLIN', $res->data['location']['city']);
        $this->assertGreaterThan(0, $res->data['location']['combined_rate']);

    }

    public function testZipCityCountryCanada() {
        $api = ClientTest::getInstance();
        $res = $api->getTaxRate("N8W1Y3", "ON", "CA")->execute();

        $this->assertNotEmpty($res);
        $this->assertInstanceOf("TaxJar\\Api\\Response", $res);
        $this->assertEquals(\TaxJar\Api\Response::HTTP_OK, $res->status);

        $this->assertNotEmpty($res->data);
        $this->assertArrayHasKey('location', $res->data);

        $this->assertNotEmpty($res->data);
        $this->assertArrayHasKey('combined_rate', $res->data['location']);
        $this->assertEquals('Windsor', $res->data['location']['city']);
        $this->assertGreaterThan(0, $res->data['location']['combined_rate']);

    }

}
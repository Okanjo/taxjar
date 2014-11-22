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

class SalesTaxTest extends PHPUnit_Framework_TestCase {

    public function testSameOrigin() {
        $api = ClientTest::getInstance();
        $res = $api->getSalesTax()->where(array(
            \TaxJar\Api\Common\Fields::AMOUNT => "10.00",
            \TaxJar\Api\Common\Fields::SHIPPING => "10.00",

            \TaxJar\Api\Common\Fields::FROM_CITY => "FRANKLIN",
            \TaxJar\Api\Common\Fields::FROM_STATE => "WI",
            \TaxJar\Api\Common\Fields::FROM_ZIP => "53132",
            \TaxJar\Api\Common\Fields::FROM_COUNTRY => "US",

            \TaxJar\Api\Common\Fields::TO_CITY => "FRANKLIN",
            \TaxJar\Api\Common\Fields::TO_STATE => "WI",
            \TaxJar\Api\Common\Fields::TO_ZIP => "53132",
            \TaxJar\Api\Common\Fields::TO_COUNTRY => "US",
        ))->execute();

        $this->assertNotEmpty($res);
        $this->assertInstanceOf("TaxJar\\Api\\Response", $res);
        $this->assertEquals(\TaxJar\Api\Response::HTTP_OK, $res->status);

        $this->assertNotEmpty($res->data);
        $this->assertArrayHasKey('amount_to_collect', $res->data);
        $this->assertArrayHasKey('rate', $res->data);
        $this->assertArrayHasKey('has_nexus', $res->data);
        $this->assertArrayHasKey('freight_taxable', $res->data);
        $this->assertArrayHasKey('tax_source', $res->data);

    }

    public function testSameOriginCanada() {
        $api = ClientTest::getInstance();
        $res = $api->getSalesTax()->where(array(
            \TaxJar\Api\Common\Fields::AMOUNT => "10.00",
            \TaxJar\Api\Common\Fields::SHIPPING => "10.00",

            \TaxJar\Api\Common\Fields::FROM_CITY => "Windsor",
            \TaxJar\Api\Common\Fields::FROM_STATE => "ON",
            \TaxJar\Api\Common\Fields::FROM_ZIP => "N8W1Y3",
            \TaxJar\Api\Common\Fields::FROM_COUNTRY => "CA",

            \TaxJar\Api\Common\Fields::TO_CITY => "Windsor",
            \TaxJar\Api\Common\Fields::TO_STATE => "ON",
            \TaxJar\Api\Common\Fields::TO_ZIP => "N8W1Y3",
            \TaxJar\Api\Common\Fields::TO_COUNTRY => "CA",
        ))->execute();

        $this->assertNotEmpty($res);
        $this->assertInstanceOf("TaxJar\\Api\\Response", $res);
        $this->assertEquals(\TaxJar\Api\Response::HTTP_OK, $res->status);

        $this->assertNotEmpty($res->data);
        $this->assertArrayHasKey('amount_to_collect', $res->data);
        $this->assertArrayHasKey('rate', $res->data);
        $this->assertArrayHasKey('has_nexus', $res->data);
        $this->assertArrayHasKey('freight_taxable', $res->data);
        $this->assertArrayHasKey('tax_source', $res->data);

    }

    public function testDocsExample() {
        $api = ClientTest::getInstance();
        $res = $api->getSalesTax()->where(array(
            \TaxJar\Api\Common\Fields::AMOUNT => "10.00",
            \TaxJar\Api\Common\Fields::SHIPPING => "2.00",

            \TaxJar\Api\Common\Fields::FROM_CITY => "Ramsey",
            \TaxJar\Api\Common\Fields::FROM_STATE => "NJ",
            \TaxJar\Api\Common\Fields::FROM_ZIP => "07446",
            \TaxJar\Api\Common\Fields::FROM_COUNTRY => "US",

            \TaxJar\Api\Common\Fields::TO_CITY => "Freehold",
            \TaxJar\Api\Common\Fields::TO_STATE => "NJ",
            \TaxJar\Api\Common\Fields::TO_ZIP => "07728",
            \TaxJar\Api\Common\Fields::TO_COUNTRY => "US",
        ))->execute();

        $this->assertNotEmpty($res);
        $this->assertInstanceOf("TaxJar\\Api\\Response", $res);
        $this->assertEquals(\TaxJar\Api\Response::HTTP_OK, $res->status);

        $this->assertNotEmpty($res->data);
        $this->assertArrayHasKey('amount_to_collect', $res->data);
        $this->assertArrayHasKey('rate', $res->data);
        $this->assertArrayHasKey('has_nexus', $res->data);
        $this->assertArrayHasKey('freight_taxable', $res->data);
        $this->assertArrayHasKey('tax_source', $res->data);

    }

} 
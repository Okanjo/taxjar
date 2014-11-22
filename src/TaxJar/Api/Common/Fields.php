<?php
/**
 * Date: 11/21/14 3:56 PM
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

namespace TaxJar\Api\Common;


class Fields {

    // Get smart tax rate params
    const AMOUNT = 'amount';
    const SHIPPING = 'shipping';

    const FROM_COUNTRY = 'from_country';
    const FROM_STATE = 'from_state';
    const FROM_CITY = 'from_city';
    const FROM_ZIP = 'from_zip';

    const TO_COUNTRY = 'to_country';
    const TO_STATE = 'to_state';
    const TO_CITY = 'to_city';
    const TO_ZIP = 'to_zip';

    // Get Tax Rate query params
    const COUNTRY = 'country';

}
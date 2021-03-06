<?php

/**
 * @copyright Copyright (c) 2020-2021 Afterpay Corporate Services Pty Ltd
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Afterpay\SDK\Helper;

use Afterpay\SDK\Exception\InvalidArgumentException;

final class UrlHelper
{
    /**
     * Generate a Merchant Portal URL for an Order, given its ID, ISO 2-character country code and API environment.
     *
     * @param string $orderId
     * @param string $countryCode
     * @param string $apiEnvironment
     * @return string
     * @throws \Afterpay\SDK\Exception\InvalidArgumentException
     */
    public static function generateMerchantPortalOrderUrl($orderId, $countryCode, $apiEnvironment)
    {
        if (! is_string($orderId)) {
            throw new InvalidArgumentException('String expected for $orderId; ' . gettype($orderId) . ' given');
        }

        if (! is_string($countryCode)) {
            throw new InvalidArgumentException('String expected for $countryCode; ' . gettype($countryCode) . ' given');
        }

        if (! is_string($apiEnvironment)) {
            throw new InvalidArgumentException('String expected for $apiEnvironment; ' . gettype($apiEnvironment) . ' given');
        }

        $uriCountry = strtolower($countryCode);

        if (in_array($countryCode, ['ES', 'FR', 'IT'])) {
            $prefix = 'merchant.';
            $tld = 'clearpay.com';
            $path = "/orders/details/{$orderId}";
        } elseif (in_array($countryCode, ['GB', 'UK'])) {
            $prefix = 'portal.';
            $tld = 'clearpay.co.uk';
            $uriCountry = 'uk';
            $path = "/{$uriCountry}/merchant/order/{$orderId}";
        } else {
            $prefix = 'portal.';
            $tld = 'afterpay.com';
            $path = "/{$uriCountry}/merchant/order/{$orderId}";
        }

        if (strtolower($apiEnvironment) === 'sandbox') {
            $prefix .= 'sandbox.';
        }

        return "https://{$prefix}{$tld}{$path}";
    }
}

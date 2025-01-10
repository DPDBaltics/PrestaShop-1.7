<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */


namespace Invertus\dpdBaltics\Util;

use Invertus\dpdBaltics\Config\Config;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ProductUtility
{
    public static function hasAvailability($productReference) {
        if ($productReference === Config::PRODUCT_TYPE_SATURDAY_DELIVERY ||
            $productReference === Config::PRODUCT_TYPE_SATURDAY_DELIVERY_COD ||
            $productReference === Config::PRODUCT_TYPE_SAME_DAY_DELIVERY) {
            return true;
        }

        return false;
    }

    public static function validateSameDayDelivery($countryIso, $city)
    {
        if ($countryIso !== Config::LATVIA_ISO_CODE) {
            return false;
        }

        if (strtolower($city) === 'riga' || strtolower($city) === 'rīga') {
            return true;
        }

        return false;
    }

}
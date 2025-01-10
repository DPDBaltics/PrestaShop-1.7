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


namespace Invertus\dpdBaltics\Provider;

use Context;
use Address;
use Country;
use Invertus\dpdBaltics\Config\Config;
use Validate;
use Configuration;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CurrentCountryProvider
{
    private $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * @param null|int $cart
     *
     * @return string|null
     */
    public function getCurrentCountryIsoCode($cart = null)
    {
        if ($cartCountryIsoCode = $this->getCountryFromCart($cart)) {

            return $cartCountryIsoCode;
        }

        return $this->getWebServiceCountryCode();
    }

    /**
     * @param null $cart
     *
     * @return string|null
     *
     * Gets country from current cart context
     */
    private function getCountryFromCart($cart = null)
    {
        if (!$cart) {
            $cart = $this->context->cart;
        }

        if (!Validate::isLoadedObject($cart)) {
            return null;
        }

        if (!$cart->id_address_delivery) {
            return null;
        }

        return $this->getCountryIsoCodeByAddress($cart->id_address_delivery);
    }

    /**
     * @return string|null
     *
     * Gets country from api configuration which is set on login
     */
    private function getWebServiceCountryCode()
    {
        return Configuration::get(Config::WEB_SERVICE_COUNTRY) ?: null;
    }

    /**
     * @param int $idAddress
     *
     * @return string|null
     */
    private function getCountryIsoCodeByAddress($idAddress)
    {
        $address = new Address($idAddress);

        return Country::getIsoById($address->id_country) ?: null;

    }
}

<?php

namespace Invertus\dpdBaltics\Provider;

use Context;
use Address;
use Country;
use Invertus\dpdBaltics\Config\Config;
use Validate;
use Configuration;

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

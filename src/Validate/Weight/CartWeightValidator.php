<?php

namespace Invertus\dpdBaltics\Validate\Weight;

use Invertus\dpdBaltics\Config\Config;
use PrestaShop\PrestaShop\Adapter\Validate;

class CartWeightValidator
{
    public function validate($cart, $countryIso, $productReference)
    {
        $parcelDistribution = \Configuration::get(Config::PARCEL_DISTRIBUTION);
        $maxAllowedWeight = Config::getDefaultServiceWeights($countryIso, $productReference);

        if (!$maxAllowedWeight) {
            return true;
        }

        switch ($parcelDistribution) {
            case \DPDParcel::DISTRIBUTION_NONE:
                return $maxAllowedWeight >= $cart->getTotalWeight();
            case \DPDParcel::DISTRIBUTION_PARCEL_QUANTITY:
                return $this->isProductQuantityDistributionWeightValid($cart, $maxAllowedWeight);
            case \DPDParcel::DISTRIBUTION_PARCEL_PRODUCT:
                return $this->isProductDistributionWeightValid($cart, $maxAllowedWeight);
            default :
                return true;
        }


    }

    private function isProductQuantityDistributionWeightValid($cart, $maxAllowedWeight)
    {
        $cartProducts = $cart->getProducts();
        $isValid = false;

        if (!Validate::isLoadedObject($cart)) {
            return false;
        }

        if (!$cartProducts) {
            return false;
        }

        foreach ($cartProducts as $product) {
            if ($product['weight'] && $product['weight'] > 0) {
                $isValid = $maxAllowedWeight >= $product['weight'];
            }

            if ($isValid) {
                break;
            }
        }

        return $isValid;
    }

    private function isProductDistributionWeightValid($cart, $maxAllowedWeight)
    {
        $cartProducts = $cart->getProducts();
        $isValid = false;

        if (!Validate::isLoadedObject($cart)) {
            return false;
        }

        if (!$cartProducts) {
            return false;
        }

        foreach ($cartProducts as $product) {
            if ($product['weight'] && $product['weight'] > 0) {
                $weight = $product['weight'] * $product['quantity'];
                $isValid = $maxAllowedWeight >= $weight;
            }

            if ($isValid) {
                break;
            }
        }

        return $isValid;
    }
}

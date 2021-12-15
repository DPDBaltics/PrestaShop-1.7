<?php

namespace Invertus\dpdBaltics\Validate\Weight;

use Invertus\dpdBaltics\Config\Config;
use Validate;



class CartWeightValidator
{
    const DISTRIBUTION_NONE = 'none';
    const DISTRIBUTION_PARCEL_PRODUCT = 'parcel_product';
    const DISTRIBUTION_PARCEL_QUANTITY = 'parcel_quantity';

    public function validate($cart, $parcelDistribution, $maxAllowedWeight)
    {
        if (!$maxAllowedWeight) {
            return true;
        }

        switch ($parcelDistribution) {
            case self::DISTRIBUTION_NONE:
                return $maxAllowedWeight >= $cart->getTotalWeight();

            case self::DISTRIBUTION_PARCEL_QUANTITY:
                return $this->isProductQuantityDistributionWeightValid($cart, $maxAllowedWeight);

            case self::DISTRIBUTION_PARCEL_PRODUCT:
                return $this->isProductDistributionWeightValid($cart, $maxAllowedWeight);

        }

        return false;
    }

    private function isProductQuantityDistributionWeightValid($cart, $maxAllowedWeight)
    {
        $cartProducts = $cart->getProducts();
        $isValid = true;

        if (!$cartProducts) {
            return false;
        }

        foreach ($cartProducts as $product) {
            if ((int) $product['weight'] > 0) {
                $isValid = $maxAllowedWeight >= $product['weight'];

            }

            if (!$isValid) {
                break;
            }
        }

        return $isValid;
    }

    private function isProductDistributionWeightValid($cart, $maxAllowedWeight)
    {
        $cartProducts = $cart->getProducts();
        $isValid = true;

        foreach ($cartProducts as $product) {
            if ((int) $product['weight'] > 0) {
                $weight = (int) $product['weight'] * (int) $product['quantity'];
                $isValid = $maxAllowedWeight >= $weight;
            }

            if (!$isValid) {
                break;
            }
        }

        return $isValid;
    }
}

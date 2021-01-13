<?php

namespace Invertus\dpdBaltics\Validate\Weight;

use Invertus\dpdBaltics\Config\Config;

class CartWeightValidator
{
    public function validate($cartWeight, $countryIso, $productReference)
    {
        $maxAllowedWeight = Config::getDefaultServiceWeights($countryIso, $productReference);
        if (!$maxAllowedWeight) {
            return true;
        }

        return $maxAllowedWeight >= $cartWeight;
    }
}
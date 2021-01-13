<?php

namespace Invertus\dpdBaltics\Service\Address;

use Invertus\dpdBaltics\Repository\ShopRepository;
use Tools;

class AddressTemplateService
{
    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var ShopRepository
     */
    private $shopRepository;

    public function __construct(ShopRepository $shopRepository)
    {
        $this->shopRepository = $shopRepository;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function addUpdateShops($object, $selectedShops)
    {
        $allSelected = false;

        if (!$selectedShops) {
            $allSelected = true;
            $this->shopRepository->updateAddressTemplateShops($object->id, [], $allSelected);
            return;
        }

        foreach ($selectedShops as $selectedShop) {
            // Zero means "All zones" was selected
            if (0 == $selectedShop) {
                $allSelected = true;
                break;
            }
        }

        $this->shopRepository->updateAddressTemplateShops($object->id, $selectedShops, $allSelected);
    }

    public function removeShops($idPriceRule)
    {
        $this->shopRepository->removeAddressTemplateShops($idPriceRule);
    }
}
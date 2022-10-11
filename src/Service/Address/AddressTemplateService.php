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
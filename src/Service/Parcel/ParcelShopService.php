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


namespace Invertus\dpdBaltics\Service\Parcel;

use Invertus\dpdBaltics\Factory\ShopFactory;
use Invertus\dpdBaltics\Repository\ParcelShopRepository;
use Invertus\dpdBalticsApi\Api\DTO\Object\ParcelShop;

class ParcelShopService
{
    /**
     * @var ParcelShopRepository
     */
    private $parcelShopRepository;
    /**
     * @var ShopFactory
     */
    private $shopFactory;

    public function __construct(ParcelShopRepository $parcelShopRepository, ShopFactory $shopFactory)
    {
        $this->parcelShopRepository = $parcelShopRepository;
        $this->shopFactory = $shopFactory;
    }

    public function getParcelShopsByCountryAndCity($countryCode, $city)
    {
        $parcelShops = $this->parcelShopRepository->getShopsByCity($countryCode, $city);

        return $this->shopFactory->createShop($parcelShops);
    }

    public function getFilteredParcels($countryCode, $city, $street)
    {
        $parcelShops = $this->parcelShopRepository->getFilterByAddress($countryCode, $city, $street);

        return $this->shopFactory->createShop($parcelShops);
    }

    public function getParcelShopByShopId($shopId)
    {
        $parcelShops = $this->parcelShopRepository->getShopsByShopId($shopId);

        return $this->shopFactory->createShop($parcelShops);
    }

    public function moveSelectedShopToFirst(array $parcelShops, $street)
    {
        /** @var ParcelShop $parcelShop */
        foreach ($parcelShops as $key =>$parcelShop) {
            if ($parcelShop->getStreet() === $street) {
                $selectedParcelShop = $parcelShops[$key];
                unset($parcelShops[$key]);
                array_unshift($parcelShops, $selectedParcelShop);
                return $parcelShops;
            }
        }

        return $parcelShops;
    }
}

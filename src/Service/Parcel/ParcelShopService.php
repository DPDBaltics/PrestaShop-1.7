<?php

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

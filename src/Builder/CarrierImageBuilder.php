<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    INVERTUS, UAB www.invertus.eu <support@invertus.eu>
 * @copyright Copyright (c) permanent, INVERTUS, UAB
 * @license   Addons PrestaShop license limitation
 * @see       /LICENSE
 *
 *  International Registered Trademark & Property of INVERTUS, UAB
 */

namespace Invertus\dpdBaltics\Builder;

use DPDBaltics;
use Invertus\dpdBaltics\Config\Config;

/**
 * Class DPDCarrierImageBuilder - stores carrier images by associated carrier
 * @package src\Builder
 */
class CarrierImageBuilder implements BuilderInterface
{
    /** @var int */
    private $idReference;
    /** @var bool */
    private $isPickupCarrier;
    /** @var int */
    private $errors = [];
    /** @var DPDBaltics  */
    private $module;

    public function __construct(DPDBaltics $module)
    {
        $this->module = $module;
    }

    /**
     * @param int $idReference
     */
    public function setIdReference($idReference)
    {
        $this->idReference = $idReference;
    }

    /**
     * @param bool $isPickupCarrier
     */
    public function setIsPickupCarrier($isPickupCarrier)
    {
        $this->isPickupCarrier = $isPickupCarrier;
    }


    /**
     * @inheritDoc
     *
     * @return  bool
     */
    public function save()
    {
        $image = $this->getCarrierImage(Config::CARRIER_TYPE_CLASSIC);

        return $this->uploadCarrierImage($image);
    }

    /**
     * @inheritDoc
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @inheritDoc
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * gets default images directory
     * @return string
     */
    private function getDirectoryName()
    {
        if ($this->isPickupCarrier) {
            return Config::CARRIER_PICKUP_DIR_NAME;
        }

        return Config::CARRIER_DEFAULT_DIR_NAME;
    }

    /**
     * gets carrier image according to it's type and location
     *
     * @param string $name
     * @return string
     */
    private function getCarrierImage($name)
    {
        $dir = $this->getDirectoryName();

        $size = $this->getImageSizeByPSVersion();

        return $this->module->getLocalPath() .
            'views/img/carriers/'.$dir.'/' . $size . '/'.$name.'.'.Config::CARRIER_LOGO_EXTENSION;
    }

    /**
     * uploads/ changes carrier image
     *
     * @param string $image - full path to the image with name
     * @return bool
     */
    private function uploadCarrierImage($image)
    {
        if (!$image) {
            return false;
        }

        if (file_exists(_PS_SHIP_IMG_DIR_.$this->idReference.'.jpg')) {
            unlink(_PS_SHIP_IMG_DIR_.$this->idReference.'.jpg');
        }

        if (!file_exists($image) || !copy($image, _PS_SHIP_IMG_DIR_.$this->idReference.'.jpg')) {
            return false;
        }

        return true;
    }

    private function getImageSizeByPSVersion()
    {
        if ((bool) version_compare(_PS_VERSION_, '1.7.6', '<')) {
            $imageSize = Config::IMAGE_SIZE_40_X_40;
        } else {
            $imageSize = Config::IMAGE_SIZE_50_X_50;
        }

        return $imageSize;
    }
}

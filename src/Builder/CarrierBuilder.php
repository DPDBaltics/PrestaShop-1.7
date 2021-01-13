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
use DPDPrestashopCarrier;
use PrestaShopDatabaseException;
use PrestaShopException;
use Shop;

/**
 * Class DPDCarrierBuilder - creates carrier
 */
class CarrierBuilder implements BuilderInterface
{
    const FILE_NAME = 'CarrierBuilder';

    /** @var int */
    private $idCarrier = 0;
    /** @var bool */
    private $active;
    /** @var int - constant from Carrier class */
    private $shippingMethod;
    /** @var array */
    private $delay;
    /** @var string */
    private $name;
    /** @var bool */
    private $shippingExternal;
    /** @var array */
    private $zones;
    /** @var array */
    private $groups;

    /** @var DPDBaltics  */
    private $module;
    /** @var array */
    private $errors = [];
    /** @var int */
    private $idShop;
    /** @var bool  */
    private $isModule = false;
    private $needRange = false;

    public function __construct(DPDBaltics $module)
    {
        $this->module = $module;
    }

    /**
     * @param int $idCarrier
     */
    public function setIdCarrier($idCarrier)
    {
        $this->idCarrier = $idCarrier;
    }

    /**
     * @param bool $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @param int $shippingMethod
     */
    public function setShippingMethod($shippingMethod)
    {
        $this->shippingMethod = $shippingMethod;
    }

    /**
     * @param array $delay
     */
    public function setDelay($delay)
    {
        $this->delay = $delay;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param bool $shippingExternal
     */
    public function setShippingExternal($shippingExternal)
    {
        $this->shippingExternal = $shippingExternal;
    }

    /**
     * @param array $zones
     */
    public function setZones($zones)
    {
        $this->zones = $zones;
    }

    /**
     * @param array $groups
     */
    public function setGroups($groups)
    {
        $this->groups = $groups;
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

    public function setIsModule($isModule)
    {
        $this->isModule = $isModule;
    }

    public function setNeedRange($needRange)
    {
        $this->needRange = $needRange;
    }

    /**
     * @inheritDoc
     *
     * @return DPDPrestashopCarrier|bool
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function save()
    {
        $carrier = new DPDPrestashopCarrier($this->idCarrier);
        $carrier->active = $this->active;
        $carrier->shipping_method = $this->shippingMethod;
        $carrier->delay = $this->delay;
        $carrier->name = $this->name;
        $carrier->shipping_external = $this->shippingExternal;
        $carrier->external_module_name = $this->module->name;
        $carrier->url = '';
        $carrier->id_shop_list = Shop::getShops(true, null, true);
        //required for pick-up carriers
        if ($this->needRange && $this->isModule) {
            $carrier->need_range = true;
            $carrier->is_module = true;
        }

        // Validate fields before adding the carrier to get those juicy validation messages
        // to nicely display failure reason to the user
        $validationError = $carrier->validateFields(false, true);
        $langValidationError = $carrier->validateFieldsLang(false, true);

        if (true !== $validationError) {
            $this->errors[] = $validationError;
        }

        if (true !== $langValidationError) {
            $this->errors[] = $langValidationError;
        }

        if ($this->errors) {
            return false;
        }

        try {
            $carrier->save();
        } catch (PrestaShopException $e) {
            $this->errors[] = sprintf(
                $this->module->l('Unable to create Prestashop carrier for DPD service %1s', self::FILE_NAME),
                $carrier->name
            );
            return false;
        }

        if (!$this->idCarrier) {
            foreach ($this->zones as $zone) {
                $carrier->addZone($zone['id_zone']);
            }
            $carrier->insertDeliveryPlaceholder($this->zones);
            $carrier->setGroups($this->groups);
        }

        if (!$this->idShop) {
            $shopIds = Shop::getContextListShopID();
            $carrier->associateTo($shopIds);
        } else {
            $carrier->associateTo($this->idShop);
        }

        return $carrier;
    }

    public function setIdShop($idShop)
    {
        $this->idShop = $idShop;
    }
}

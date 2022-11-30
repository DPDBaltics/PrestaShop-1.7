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


class DPDProduct extends ObjectModel
{
    public $id_dpd_product;

    public $product_reference;

    public $name;

    public $id_reference;

    public $active;

    public $is_pudo;

    public $is_cod;

    public $is_home_collection;

    public $all_zones;

    public $all_shops;

    public static $definition = [
        'table' => 'dpd_product',
        'primary' => 'id_dpd_product',
        'fields' => [
            'id_reference' => ['type' => self::TYPE_STRING, 'required' => 1],
            'name' => ['type' => self::TYPE_STRING, 'size' => 255, 'required' => 1],
            'product_reference' => ['type' => self::TYPE_STRING, 'size' => 255, 'required' => 1],
            'active' => ['type' => self::TYPE_BOOL, 'required' => 1, 'validate' => 'isBool'],
            'is_pudo' => ['type' => self::TYPE_BOOL, 'required' => 1, 'validate' => 'isBool'],
            'is_cod' => ['type' => self::TYPE_BOOL, 'required' => 1, 'validate' => 'isBool'],
            'is_home_collection' => ['type' => self::TYPE_BOOL, 'required' => 1, 'validate' => 'isBool'],
            'all_zones' => ['type' => self::TYPE_BOOL, 'required' => 1, 'validate' => 'isBool'],
            'all_shops' => ['type' => self::TYPE_BOOL, 'required' => 1, 'validate' => 'isBool'],
        ],
    ];

    /**
     * @return mixed
     */
    public function getIdDpdProduct()
    {
        return $this->id_dpd_product;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getIdReference()
    {
        return $this->id_reference;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @return mixed
     */
    public function getIsCod()
    {
        return $this->is_cod;
    }

    /**
     * @return mixed
     */
    public function getIsHomeCollection()
    {
        return $this->is_home_collection;
    }

    /**
     * @return bool
     */
    public function getIsAllZones()
    {
        return $this->all_zones;
    }

    /**
     * @return bool
     */
    public function getIsAllShops()
    {
        return $this->all_shops;
    }

    /**
     * @return mixed
     */
    public function getProductReference()
    {
        return $this->product_reference;
    }

    /**
     * @param mixed $product_reference
     */
    public function setProductReference($product_reference)
    {
        $this->product_reference = $product_reference;
    }

    /**
     * @return mixed
     */
    public function getIsPudo()
    {
        return $this->is_pudo;
    }

    /**
     * @param mixed $is_pudo
     */
    public function setIsPudo($is_pudo)
    {
        $this->is_pudo = $is_pudo;
    }

    /**
     * @return mixed
     */
    public function getAllZones()
    {
        return $this->all_zones;
    }

    /**
     * @param mixed $all_zones
     */
    public function setAllZones($all_zones)
    {
        $this->all_zones = $all_zones;
    }

    /**
     * @return mixed
     */
    public function getAllShops()
    {
        return $this->all_shops;
    }

    /**
     * @param mixed $all_shops
     */
    public function setAllShops($all_shops)
    {
        $this->all_shops = $all_shops;
    }
}

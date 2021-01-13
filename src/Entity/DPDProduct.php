<?php

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

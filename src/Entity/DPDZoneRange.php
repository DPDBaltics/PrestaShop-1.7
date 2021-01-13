<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    INVERTUS, UAB www.invertus.eu <support@invertus.eu>
 * @copyright Copyright (c) permanent, INVERTUS, UAB
 * @license   Addons PrestaShop license limitation
 * @see       /LICENSE
 *
 * International Registered Trademark & Property of INVERTUS, UAB
 */

/**
 * Class DPDZoneRange
 */
class DPDZoneRange extends ObjectModel
{
    /**
     * @var int References DPDZone object model ID
     */
    public $id_dpd_zone;

    /**
     * @var int
     */
    public $id_country;


    /**
     * @var bool
     */
    public $include_all_zip_codes;

    /**
     * @var string
     */
    public $zip_code_from;

    /**
     * @var string
     */
    public $zip_code_to;

    /**
     * @var int
     */
    public $zip_code_from_numeric;

    /**
     * @var int
     */
    public $zip_code_to_numeric;

    /**
     * @var array
     */
    public static $definition = [
        'table' => 'dpd_zone_range',
        'primary' => 'id_dpd_zone_range',
        'fields' => [
            'id_dpd_zone' => ['type' => self::TYPE_INT, 'required' => 1, 'validate' => 'isUnsignedInt'],
            'id_country' => ['type' => self::TYPE_STRING, 'required' => 1],
            'include_all_zip_codes' => ['type' => self::TYPE_BOOL, 'required' => 1, 'validate' => 'isBool'],
            'zip_code_from' => ['type' => self::TYPE_STRING, 'validate' => 'isPostCode', 'size' => 12],
            'zip_code_to' => ['type' => self::TYPE_STRING, 'validate' => 'isPostCode', 'size' => 12],
            'zip_code_from_numeric' => ['type' => self::TYPE_INT, 'validate' => 'isPostCode', 'size' => 12],
            'zip_code_to_numeric' => ['type' => self::TYPE_INT, 'validate' => 'isPostCode', 'size' => 12],
        ],
    ];

    /**
     * Get repository class name
     *
     * @return string
     */
    public static function getRepositoryClassName()
    {
        return 'DPDZoneRangeRepository';
    }

    /**
     * @param bool $autoDate
     * @param bool $nullValues
     *
     * @return bool
     */
    public function add($autoDate = true, $nullValues = false)
    {
        $this->checkRanges();

        return parent::add($autoDate, $nullValues);
    }

    /**
     * @param bool $nullValues
     *
     * @return bool
     */
    public function update($nullValues = false)
    {
        $this->checkRanges();

        return parent::update($nullValues);
    }

    /**
     * If "include_all_zip_codes" is enabled, then reset ranges
     */
    protected function checkRanges()
    {
        if ($this->include_all_zip_codes) {
            $this->zip_code_from = '';
            $this->zip_code_to = '';
            $this->zip_code_from_numeric = 0;
            $this->zip_code_to_numeric = 0;
        }
    }
}

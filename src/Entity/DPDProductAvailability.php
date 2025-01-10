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

if (!defined('_PS_VERSION_')) {
    exit;
}

class DPDProductAvailability extends ObjectModel
{
    public $id_dpd_product_availability;

    public $product_reference;

    public $day;

    public $interval_start;

    public $interval_end;

    public static $definition = [
        'table' => 'dpd_product_availability',
        'primary' => 'id_dpd_product_availability',
        'fields' => [
            'product_reference' => ['type' => self::TYPE_STRING, 'size' => 255, 'required' => 1],
            'interval_start' => ['type' => self::TYPE_STRING, 'required' => 1, 'validate' => 'isString'],
            'interval_end' => ['type' => self::TYPE_STRING, 'required' => 1, 'validate' => 'isString'],
            'day' => ['type' => self::TYPE_STRING, 'required' => 1, 'validate' => 'isString'],
        ],
    ];

    /**
     * @return mixed
     */
    public function getIdDpdProductAvailability()
    {
        return $this->id_dpd_product_availability;
    }

    /**
     * @param mixed $id_dpd_product_availability
     */
    public function setIdDpdProductAvailability($id_dpd_product_availability)
    {
        $this->id_dpd_product_availability = $id_dpd_product_availability;
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
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param mixed $day
     */
    public function setDay($day)
    {
        $this->day = $day;
    }

    /**
     * @return mixed
     */
    public function getIntervalStart()
    {
        return $this->interval_start;
    }

    /**
     * @param mixed $interval_start
     */
    public function setIntervalStart($interval_start)
    {
        $this->interval_start = $interval_start;
    }

    /**
     * @return mixed
     */
    public function getIntervalEnd()
    {
        return $this->interval_end;
    }

    /**
     * @param mixed $interval_end
     */
    public function setIntervalEnd($interval_end)
    {
        $this->interval_end = $interval_end;
    }
}

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

use Invertus\dpdBaltics\Repository\DPDZoneRepository;
use Invertus\dpdBaltics\Repository\PriceRuleRepository;

/**
 * Class DPDPriceRule
 */
class DPDPriceRule extends ObjectModel
{
    const CUSTOMER_TYPE_ALL = 'all';
    const CUSTOMER_TYPE_REGULAR = 'individual';
    const CUSTOMER_TYPE_COMPANY = 'company';

    /**
     * @var string
     */
    public $name;

    /**
     * @var float
     */
    public $order_price_from;

    /**
     * @var float
     */
    public $order_price_to;

    /**
     * @var float
     */
    public $weight_from;

    /**
     * @var float
     */
    public $weight_to;

    /**
     * @var float
     */
    public $price;

    /** @var int Position */
    public $position;

    /** @var  bool */
    public $active;

    /** @var string - enumeration ['all', 'customer', 'company'] */
    public $customer_type;

    public static $definition = [
        'table' => 'dpd_price_rule',
        'primary' => 'id_dpd_price_rule',
        'fields' => [
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => true],
            'order_price_from' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
            'order_price_to' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
            'weight_from' => ['type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'],
            'weight_to' => ['type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'],
            'price' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
            'position' => ['type' => self::TYPE_INT],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'customer_type' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml']
        ],
    ];

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        Shop::addTableAssociation(self::$definition['table'], ['type' => 'shop']);
        parent::__construct($id, $id_lang, $id_shop);
    }

    /**
     * Get repository class name
     *
     * @return string
     */
    public static function getRepositoryClassName()
    {
        return 'DPDPriceRuleRepository';
    }

    public function add($autodate = true, $null_values = false)
    {
        if ($this->position <= 0) {
            $this->position = $this->getHighestPosition();
        }

        if (!parent::add($autodate, $null_values) || !Validate::isLoadedObject($this)) {
            return false;
        }

        return true;
    }

    public function delete()
    {
        if (!parent::delete()) {
            return false;
        }

        if (!$this->clearPositions()) {
            return false;
        }
        return true;
    }

    /** moves Dpd price rule position
     * @param bool $way Up (1) or Down (0)
     * @param $position
     *
     * @return bool Update result
     * @throws PrestaShopDatabaseException
     */
    public function updatePosition($way, $position)
    {
        $query = new DbQuery();
        $query->select('pr.`id_dpd_price_rule`, pr.`position`');
        $query->from(pSQL(self::$definition['table']), 'pr');
        $query->innerJoin(
            pSQL(self::$definition['table'].'_shop'),
            'prs',
            'pr.`id_dpd_price_rule`= prs.`id_dpd_price_rule`'
        );
        $priceRules = (array) Db::getInstance()->executeS($query);

        if (empty($priceRules)) {
            return false;
        }

        $movedRule = false;
        foreach ($priceRules as $rule) {
            if ($rule['id_dpd_price_rule'] == (int) $this->id) {
                $movedRule = $rule;
            }
        }

        if (!$movedRule) {
            return false;
        }
        return $this->updateDbPositions($movedRule, $way, $position);
    }

    /**
     * Checks if this price rule can be applied, based on given product
     *
     * @param $price
     * @param $currency
     * @param $weight
     * @param $idAddress
     * @param $shopId
     *
     * @return bool
     */
    public function isApplicableForProduct($price, $currency, $weight, $idAddress)
    {
        if (!$this->isApplicableByPrice($price, $currency)) {
            return false;
        }

        if (!$this->isApplicableByWeight($weight)) {
            return false;
        }

        if (!$this->isApplicableByAddress(new Address($idAddress))) {
            return false;
        }

        return true;
    }

    public function clearPositions()
    {
        $query = new DbQuery();
        $query->select('pr.`id_dpd_price_rule`');
        $query->from(pSQL(self::$definition['table']), 'pr');
        $query->innerJoin(
            pSQL(self::$definition['table'].'_shop'),
            'prs',
            'pr.`id_dpd_price_rule`= prs.`id_dpd_price_rule`'
        );

        $result = Db::getInstance()->executeS($query);

        if (empty($result)) {
            return true;
        }

        $i = 0;
        $return = false;
        foreach ($result as $value) {
            $return = Db::getInstance()->update(
                pSQL(self::$definition['table']),
                [
                    'position' => (int) $i++,
                ],
                '`id_dpd_price_rule`='.(int) $value['id_dpd_price_rule']
            );
        }
        return $return;
    }

    public function getHighestPosition()
    {
        $query = new DbQuery();
        $query->select('IF(COUNT(pr.`id_dpd_price_rule`) = 0, 0, MAX(pr.`position`) + 1)');
        $query->from(pSQL(self::$definition['table']), 'pr');
        $query->innerJoin(
            pSQL(self::$definition['table'].'_shop'),
            'prs',
            'pr.`id_dpd_price_rule`= prs.`id_dpd_price_rule`'
        );

        return (int) Db::getInstance()->getValue($query);
    }

    /**
     * Checks if this price rule can be applied, based on given cart
     *
     * @param Cart $cart
     *
     * @return bool
     */
    public function isApplicableForCart(Cart $cart)
    {
        if (!$this->isApplicableByPrice($cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING), $cart->id_currency)) {
            return false;
        }

        if (!$this->isApplicableByWeight($cart->getTotalWeight())) {
            return false;
        }

        if (!$this->isApplicableByAddress(new Address($cart->id_address_delivery))) {
            return false;
        }

        return true;
    }

    public function isApplicableByAddress(Address $address)
    {
        /** @var DPDBaltics $module */
        $module = Module::getInstanceByName('dpdbaltics');

        /** @var DPDZoneRepository $repo */
        $repo = $module->getModuleContainer()->get(DPDZoneRepository::class);
        $zonesIds = $repo->getZonesIdsByPriceRule($this->id);

        return DPDZone::checkAddressInZones($address, $zonesIds);
    }

    public function isApplicableByPrice($price, $idCurrency)
    {
        $priceFrom  = Tools::convertPrice($this->order_price_from, $idCurrency);
        $toPrice  = Tools::convertPrice($this->order_price_to, $idCurrency);

        return $this->isApplicableForRange($price, $priceFrom, $toPrice);
    }

    public function isApplicableByWeight($weight)
    {
        return $this->isApplicableForRange($weight, $this->weight_from, $this->weight_to);
    }

    /**
     * Checks if given value is applicable for given range
     * rangeFrom <= value <= rangeTo
     * @param float $value
     * @param float $rangeFrom
     * @param float $rangeTo
     *
     * @return bool
     */
    private function isApplicableForRange($value, $rangeFrom, $rangeTo)
    {
        $value = (float) $value;
        $rangeFrom = (float) $rangeFrom;
        $rangeTo = (float) $rangeTo;

        if (!$rangeTo) {
            return $rangeFrom <= $value;
        }

        if ($rangeFrom <= $value && $value <= $rangeTo) {
            return true;
        }

        return false;
    }

    /**
     * @param array $movedRule - current position and ind
     * @param bool $way Up (1) or Down (0)
     * @param int $position - current position
     * @return bool result
     */
    private function updateDbPositions($movedRule, $way, $position)
    {
        $positionCounter = '';
        if ($way) {
            $positionCounter .= '- 1';
        } elseif (!$way) {
            $positionCounter .= '+ 1';
        }
        $updateOtherPositions = Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.self::$definition['table'].'`
                SET `position`= `position` '.pSQL($positionCounter).'
                WHERE `position`
                '.(
                $way
                ? '> '.(int) $movedRule['position'].' AND `position` <= '.(int) $position
                : '< '.(int) $movedRule['position'].' AND `position` >= '.(int) $position
            )
        );

        $updateCurrentPosition = Db::getInstance()->update(
            pSQL(self::$definition['table']),
            [
                'position' => (int) $position
            ],
            '`id_dpd_price_rule` = '.(int) $movedRule['id_dpd_price_rule']
        );

        return $updateOtherPositions && $updateCurrentPosition;
    }
}

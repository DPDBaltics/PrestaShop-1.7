<?php

namespace Invertus\dpdBaltics\Repository;

use Db;
use DbQuery;
use DPDProduct;
use mysqli_result;
use PDOStatement;
use PrestaShopCollection;
use PrestaShopDatabaseException;

class ProductRepository extends AbstractEntityRepository
{
    public function getAllProducts()
    {
        return new PrestaShopCollection('DPDProduct');
    }

    public function getAllActiveProducts($onlyId = false)
    {
        $query = new DbQuery();
        if ($onlyId) {
            $query->select('id_dpd_product');
        } else {
            $query->select('*');
        }
        $query->from('dpd_product');
        $query->where('active = 1');

        return $this->db->getValue($query);
    }

    public function deleteOldData()
    {
        $this->db->delete('dpd_product_shop');
        $this->db->delete('dpd_product_zone');
    }

    /**
     * @param $carrierId
     * @return array|bool|object|null
     */
    public function findProductByCarrierReference($carrierReference)
    {
        $query = new DbQuery();
        $query->select('*');
        $query->from('dpd_product', 'dsc');
        $query->where('dsc.id_reference = '.(int) $carrierReference);

        return $this->db->getRow($query);
    }

    /**
     *
     * @param $idCarrier
     * @param $idShop
     * @return array|false|mysqli_result|PDOStatement|resource|null
     *
     * @throws PrestaShopDatabaseException
     */
    public function checkIfCarrierIsAvailableInShop($carrierReference, $idShop)
    {
        $query = new DbQuery();
        $query->select('csp.id_dpd_product');
        $query->from('dpd_product', 'sc');
        $query->leftJoin(
            'dpd_product_shop',
            'csp',
            'csp.`id_dpd_product` = sc.`id_dpd_product`'
        );
        $query->where('sc.id_reference = '.(int)$carrierReference . ' AND (csp.id_shop = ' . (int)$idShop . ' OR all_shops = 1)');

        return $this->db->executeS($query);
    }

    public function updateProductsForPriceRule($idPriceRule, array $carriers, $checkAll = 0)
    {
        if (!$idPriceRule) {
            return;
        }

        if (empty($carriers)) {
            return true;
        }

        if ($checkAll) {
            return $this->db->insert(
                'dpd_price_rule_carrier',
                [
                    'id_dpd_price_rule' => (int) $idPriceRule,
                    'all_carriers' => (int) $checkAll
                ]
            );
        }

        $counter = 0;
        foreach ($carriers as $carrier) {
            $result = $this->db->insert(
                'dpd_price_rule_carrier',
                [
                    'id_dpd_price_rule' => (int)$idPriceRule,
                    'id_reference' => (int)$carrier
                ],
                false,
                true,
                Db::ON_DUPLICATE_KEY
            );
            if (!$result) {
                $counter++;
            }
        }

        return ($counter) ? false : true;
    }

    public function removePriceRuleProducts($idPriceRule)
    {
        $this->db->delete(
            'dpd_price_rule_carrier',
            '`id_dpd_price_rule`=' . (int)$idPriceRule
        );
    }

    public function isProductPudo($carrierReference)
    {
        $query = new DbQuery();
        $query->select('id_dpd_product');
        $query->from('dpd_product');
        $query->where('id_reference = '.(int)$carrierReference . ' AND (is_pudo = 1)');

        return $this->db->getValue($query);
    }

    public function getPudoProducts()
    {
        $query = new DbQuery();
        $query->select('sc.`id_reference`, sc.`is_pudo`');
        $query->from('dpd_product', 'sc');
        $resource = $this->db->query($query);
        $result = [];
        while ($row = $this->db->nextRow($resource)) {
            $result[$row['id_reference']] = $row['is_pudo'];
        }

        return $result;
    }
    public function getProductIdByCarrierReference($carrierReference)
    {
        $query = new DbQuery();
        $query->select('id_dpd_product');
        $query->from('dpd_product');
        $query->where('id_reference = '.(int)$carrierReference);

        return $this->db->getValue($query);
    }

    public function getProductIdByProductReference($productReference)
    {
        $query = new DbQuery();
        $query->select('id_dpd_product');
        $query->from('dpd_product');
        $query->where('product_reference = "' . pSQL($productReference) . '"');

        return $this->db->getValue($query);
    }

    public function getProductIdByProductId($productId)
    {
        $query = new DbQuery();
        $query->select('id_dpd_product');
        $query->from('dpd_product');
        $query->where('id_dpd_product = "' . pSQL($productId) . '"');

        return $this->db->getValue($query);
    }

    public function getProductsByIdZone($idZone)
    {
        $query = new DbQuery();
        $query->select('sc.`id_dpd_product`, c.`name`');
        $query->from('dpd_product_zone', 'z');
        $query->innerJoin(
            'dpd_product',
            'sc',
            'sc.`id_dpd_product` = z.`id_dpd_product`'
        );
        $query->innerJoin(
            'carrier',
            'c',
            'c.`id_reference`=sc.`id_reference` AND c.`deleted`="0"'
        );
        $query->where('z.`id_dpd_zone`="'.(int) $idZone.'"');
        $result = $this->db->executeS($query);
        if (empty($result)) {
            return [];
        }
        return (array) $result;
    }

    public function deleteByProductReference($productReference)
    {
        return $this->db->delete(
            'dpd_product',
            '`product_reference`= "' . pSQL($productReference) . '"'
        );
    }
}

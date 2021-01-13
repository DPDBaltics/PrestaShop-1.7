<?php

namespace Invertus\dpdBaltics\Repository;

use DbQuery;

class ShopRepository extends AbstractEntityRepository
{
    public function removeServiceCarrierShops($productId)
    {
        return $this->db->delete('dpd_product_shop', 'id_dpd_product = ' . (int)$productId);
    }

    public function addProductShopsFromArray(array $shops)
    {
        foreach ($shops as $shop) {
            $this->db->insert('dpd_product_shop', $shop);
        }
    }

    public function getPriceRuleShops($priceRuleId)
    {
        $query = new DbQuery();
        $query->select('s.id_shop AS id, s.name, IF(ISNULL(prs.id_shop), 0, 1) `selected`');
        $query->select('prs.`all_shops` AS `all_selected_flag`');
        $query->from('shop', 's');
        $query->leftJoin(
            'dpd_price_rule_shop',
            'prs',
            'prs.id_dpd_price_rule = ' . (int)$priceRuleId .
            ' AND (s.id_shop = prs.id_shop OR prs.all_shops = 1)'
        );
        $query->groupBy('s.id_shop');

        return $this->db->executeS($query);
    }

    public function updatePriceRuleShops($idPriceRule, array $shops, $allElementsSelected)
    {
        if (!$idPriceRule) {
            return;
        }

        if ($allElementsSelected) {
            return $this->db->insert(
                'dpd_price_rule_shop',
                [
                    'id_dpd_price_rule' => (int)$idPriceRule,
                    'id_shop' => 0,
                    'all_shops' => 1
                ]
            );
        }

        if (empty($shops)) {
            return true;
        }

        $counter = 0;
        foreach ($shops as $shopId) {
            $result = $this->db->insert(
                'dpd_price_rule_shop',
                [
                    'id_dpd_price_rule' => (int)$idPriceRule,
                    'id_shop' => (int)$shopId,
                    'all_shops' => 0
                ]
            );
            if (!$result) {
                $counter++;
            }
        }
        return $counter ? false : true;
    }

    public function removePriceRuleShops($idPriceRule)
    {
        $this->db->delete(
            'dpd_price_rule_shop',
            '`id_dpd_price_rule`=' . (int)$idPriceRule
        );
    }

    public function getAddressTemplateShops($addressTemplateId)
    {
        $query = new DbQuery();
        $query->select('s.id_shop AS id, s.name, IF(ISNULL(prs.id_shop), 0, 1) `selected`');
        $query->select('prs.`all_shops` AS `all_selected_flag`');
        $query->from('shop', 's');
        $query->leftJoin(
            'dpd_address_template_shop',
            'prs',
            'prs.id_dpd_address_template = ' . (int)$addressTemplateId .
            ' AND (s.id_shop = prs.id_shop OR prs.all_shops = 1)'
        );
        $query->groupBy('s.id_shop');

        return $this->db->executeS($query);
    }

    public function updateAddressTemplateShops($addressTemplateId, array $shops, $allElementsSelected)
    {
        if (!$addressTemplateId) {
            return;
        }

        if ($allElementsSelected) {
            return $this->db->insert(
                'dpd_address_template_shop',
                [
                    'id_dpd_address_template' => (int)$addressTemplateId,
                    'id_shop' => 0,
                    'all_shops' => 1
                ]
            );
        }

        if (empty($shops)) {
            return true;
        }

        $counter = 0;
        foreach ($shops as $shopId) {
            $result = $this->db->insert(
                'dpd_address_template_shop',
                [
                    'id_dpd_address_template' => (int)$addressTemplateId,
                    'id_shop' => (int)$shopId,
                    'all_shops' => 0
                ]
            );
            if (!$result) {
                $counter++;
            }
        }
        return $counter ? false : true;
    }

    public function removeAddressTemplateShops($addressTemplateId)
    {
        $this->db->delete(
            'dpd_address_template_shop',
            '`id_dpd_address_template`=' . (int)$addressTemplateId
        );
    }
}
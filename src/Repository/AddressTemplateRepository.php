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


namespace Invertus\dpdBaltics\Repository;

use DbQuery;
use DPDAddressTemplate;

class AddressTemplateRepository extends AbstractEntityRepository
{
    public function getReturnServiceAddressTemplates()
    {
        $query = new DbQuery();
        $query->select('`id_dpd_address_template`, name');
        $query->from('dpd_address_template');
        $query->where('type = "' . DPDAddressTemplate::ADDRESS_TYPE_RETURN_SERVICE . '"');
        $result = $this->db->query($query);

        $templates = [];
        while ($row = $this->db->nextRow($result)) {
            $templates[$row['id_dpd_address_template']] = $row['name'];
        }

        return $templates;
    }

    public function getAllAddressTemplateIds()
    {
        $query = new DbQuery();
        $query->select('`id_dpd_address_template`');
        $query->from('dpd_address_template');
        $result = $this->db->query($query);

        $templates = [];
        while ($row = $this->db->nextRow($result)) {
            $templates[] = $row['id_dpd_address_template'];
        }

        return $templates;
    }

    public function findIsAllShopsAssigned($addressTemplateId)
    {
        $query = new DbQuery();
        $query->select('pr.id_dpd_address_template');
        $query->from('dpd_address_template', 'pr');
        $query->leftJoin(
            'dpd_address_template_shop',
            'prz',
            'pr.id_dpd_address_template = prz.id_dpd_address_template'
        );
        $query->where('pr.id_dpd_address_template= ' . (int)$addressTemplateId);
        $query->where('prz.all_shops = 1');

        $result = $this->db->executeS($query);
        if (!is_array($result) || empty($result)) {
            return false;
        }

        return true;
    }

    public function findAllShopsAssigned($priceRuleId)
    {
        $query = new DbQuery();
        $query->select('prz.id_shop');
        $query->from('dpd_address_template', 'pr');
        $query->leftJoin(
            'dpd_address_template_shop',
            'prz',
            'pr.id_dpd_address_template = prz.id_dpd_address_template'
        );
        $query->where('pr.id_dpd_address_template = ' . (int)$priceRuleId);

        $ids = [];
        $result = $this->db->query($query);

        while ($row = $this->db->nextRow($result)) {
            $idShop = (int)$row['id_shop'];
            if (!$idShop) {
                continue;
            }

            $ids[] = (int)$idShop;
        }

        return $ids;
    }
}

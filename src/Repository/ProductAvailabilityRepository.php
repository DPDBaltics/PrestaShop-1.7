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

namespace Invertus\dpdBaltics\Repository;

use DbQuery;

class ProductAvailabilityRepository extends AbstractEntityRepository
{
    public function getProductAvailabilityByReference($dpdProductReference)
    {
        $query = new DbQuery();
        $query->select('*');
        $query->from('dpd_product_availability');
        $query->where('`product_reference`="' . pSQL($dpdProductReference) . '"');

        return $this->db->executeS($query);
    }

    public function deleteProductAvailabilities($dpdProductReference)
    {
        return $this->db->delete(
            'dpd_product_availability',
            '`product_reference`="' . pSQL($dpdProductReference) . '"'
        );
    }

    public function getProductAvailabilityByReferenceAndDay($dpdProductReference, $day)
    {
        $query = new DbQuery();
        $query->select('*');
        $query->from('dpd_product_availability');
        $query->where('`product_reference`="' . pSQL($dpdProductReference) . '"
         AND `day` = "' . pSQL($day) . '"');

        return $this->db->executeS($query);
    }
}

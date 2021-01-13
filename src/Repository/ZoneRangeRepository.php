<?php

namespace Invertus\dpdBaltics\Repository;

use DbQuery;

class ZoneRangeRepository extends AbstractEntityRepository
{
    public function findBy(array $criteria, $limit = null, array $notEqualsCriteria = [])
    {
        $query = new DbQuery();
        $query->select(
            'dzr.id_dpd_zone_range, dzr.id_dpd_zone, dzr.id_country, dzr.include_all_zip_codes'
        );
        $query->select('dzr.zip_code_from, dzr.zip_code_to');
        $query->from('dpd_zone_range', 'dzr');

        foreach ($criteria as $field => $value) {
            $query->where('dzr.'.bqSQL($field).' = "'.pSQL($value).'"');
        }

        foreach ($notEqualsCriteria as $field => $value) {
            $query->where('dzr.'.bqSQL($field).' != "'.pSQL($value).'"');
        }

        if ($limit) {
            $query->limit((int) $limit);
        }

        $result = $this->db->executeS($query);

        return $result ? $result : [];
    }
}

<?php

namespace Invertus\dpdBaltics\Repository;

use Db;
use DbQuery;
use PrestaShopDatabaseException;
use PrestaShopException;

class LogsRepository extends AbstractEntityRepository
{
    /**
     * Returns logs from database
     * @return array|false|mysqli_result|null|PDOStatement|resource
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function getAllLogs()
    {
        $query = new DbQuery();
        $query->select('id_dpd_log, request, response, status, date_add');
        $query->from('dpd_log');

        return Db::getInstance()->executes($query->build());
    }
}

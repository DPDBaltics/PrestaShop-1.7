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



namespace Invertus\dpdBaltics\Service;

use DPDBaltics;
use Invertus\dpdBaltics\Repository\LogsRepository;
use PrestaShopDatabaseException;
use PrestaShopException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class LogsService
{
    const FILE_NAME = 'LogsService';

    /**
     * @var LogsRepository
     */
    private $logsRepository;
    /**
     * @var DPDBaltics
     */
    private $module;

    public function __construct(LogsRepository $logsRepository, DPDBaltics $module)
    {
        $this->logsRepository = $logsRepository;
        $this->module = $module;
    }

    /**
     * Exports logs into csv file
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function downloadLogsCsv()
    {
        $fileName = sprintf('logs_%s.csv', date('Y-m-d_His'));
        $exportRows = $this->getRows();
        $headers = $this->getHeaders();

        if (!$exportRows || !$fileName) {
            return false;
        }

        $delimiter = ';';
        $output = fopen('php://output', 'w');

        $this->sendHttpHeaders($fileName);

        if ($headers && is_array($headers)) {
            fputcsv($output, $headers, $delimiter);
        }

        foreach ($exportRows as $row) {
            fputcsv($output, $row, $delimiter);
        }

        fclose($output);
        die;
    }

    public function hideUsernameAndPasswordFromRequest($request)
    {
        $passwordPositionStart = strpos($request, 'password');
        $passwordPositionEnd = strpos($request, '&', $passwordPositionStart);
        $usernamePositionStart = strpos($request, 'username');
        $loginData = substr($request, $usernamePositionStart, $passwordPositionEnd - $usernamePositionStart);

        return str_replace($loginData, 'username=&password=', $request);
    }
    /**
     * Gets all logs from databse and substr_replace($str, '', $i, 1);parses them into rows ready for csv
     * @return array
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function getRows()
    {
        $rows = [];
        $logs = $this->logsRepository->getAllLogs();
        foreach ($logs as $log) {
            $row = [
                $log['id_dpd_log'],
                $log['request'],
                $log['response'],
                $log['status'],
                $log['date_add']
            ];
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     * Gets header required for csv
     * @return array
     */
    private function getHeaders()
    {
        return [
            $this->module->l('id', self::FILE_NAME),
            $this->module->l('request', self::FILE_NAME),
            $this->module->l('response', self::FILE_NAME),
            $this->module->l('status', self::FILE_NAME),
            $this->module->l('date_add', self::FILE_NAME),
        ];
    }

    /**
     * Send HTTP header to force file download
     *
     * @param string $fileName
     */
    private function sendHttpHeaders($fileName)
    {
        if (ob_get_level() && ob_get_length() > 0) {
            ob_clean();
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$fileName);
        header('Content-Type: application/force-download; charset=UTF-8');
        header('Cache-Control: no-store, no-cache');
    }
}

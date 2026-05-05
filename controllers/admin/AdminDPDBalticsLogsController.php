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

use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Controller\AbstractAdminController;
use Invertus\dpdBaltics\Infrastructure\Bootstrap\ModuleTabs;

require_once dirname(__DIR__).'/../vendor/autoload.php';

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminDPDBalticsLogsController extends AbstractAdminController
{
    const FILE_NAME = 'AdminDPDBalticsLogsController';

    const LOG_INFORMATION_TYPE_REQUEST = 'request';
    const LOG_INFORMATION_TYPE_RESPONSE = 'response';

    public function __construct()
    {
        $this->className = 'DPDProduct';
        $this->table = DPDLog::$definition['table'];
        $this->identifier = DPDLog::$definition['primary'];
        $this->allow_export = true;

        parent::__construct();
        $this->initList();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        Media::addJsDef([
            'dpdbaltics' => [
                'logsUrl' => $this->context->link->getAdminLink(ModuleTabs::ADMIN_LOGS_CONTROLLER),
            ],
        ]);

        $this->addCSS($this->module->getPathUri() . 'views/css/admin/logs_tab.css');
        $this->addJS($this->module->getPathUri() . 'views/js/admin/log.js');
    }

    public function printRequestButton($request, $data)
    {
        return $this->getDisplayButton($data['id_dpd_log'], $request, self::LOG_INFORMATION_TYPE_REQUEST);
    }

    public function printResponseButton($response, $data)
    {
        return $this->getDisplayButton($data['id_dpd_log'], $response, self::LOG_INFORMATION_TYPE_RESPONSE);
    }

    public function printSeverity($severity, $data)
    {
        $level = strtolower((string) $severity);
        $levelMap = [
            'emergency' => 1, 'alert' => 1, 'critical' => 1, 'error' => 1,
            'warning' => 2,
            'notice' => 3, 'info' => 3,
            'debug' => 4,
        ];
        $num = isset($levelMap[$level]) ? $levelMap[$level] : null;
        $cssClass = $num ? 'dpd-log-severity dpd-log-severity-' . $num : 'dpd-log-severity';
        $label = $level !== '' ? ucfirst($level) : '--';

        return sprintf(
            '<span class="%s">%s</span>',
            htmlspecialchars($cssClass, ENT_QUOTES, 'UTF-8'),
            $num ? sprintf('%d &middot; %s', $num, htmlspecialchars($label, ENT_QUOTES, 'UTF-8')) : htmlspecialchars($label, ENT_QUOTES, 'UTF-8')
        );
    }

    public function printMessage($message, $data)
    {
        if ($message === null || $message === '') {
            return '--';
        }
        $value = (string) $message;
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && isset($decoded['message'])) {
            $value = (string) $decoded['message'];
        }
        $clean = trim(preg_replace('/\s+/', ' ', strip_tags($value)));
        if (function_exists('mb_strlen') && mb_strlen($clean) > 90) {
            $clean = mb_substr($clean, 0, 87) . '...';
        }
        return htmlspecialchars($clean, ENT_QUOTES, 'UTF-8');
    }

    public function printContext($context, $data)
    {
        if ($context === null || $context === '') {
            return '--';
        }
        $endpoint = '';
        $decoded = json_decode((string) $context, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && !empty($decoded['endpoint'])) {
            $endpoint = (string) $decoded['endpoint'];
        } else {
            $endpoint = (string) $context;
        }
        $endpoint = strtok($endpoint, '?');
        $endpoint = $endpoint !== false ? basename($endpoint) : '';
        if ($endpoint === '') {
            return '--';
        }
        return htmlspecialchars($endpoint, ENT_QUOTES, 'UTF-8');
    }

    public function processExport($textDelimiter = '"')
    {
        if (ob_get_level() && ob_get_length() > 0) {
            ob_clean();
        }

        $fileName = sprintf('dpdbaltics_logs_%s.csv', date('Y-m-d_His'));
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $fileName);
        header('Cache-Control: no-store, no-cache');

        $fd = fopen('php://output', 'wb');

        $storeInfo = [
            'PrestaShop Version' => _PS_VERSION_,
            'PHP Version' => phpversion(),
            'Module Version' => $this->module->version,
            'MySQL Version' => Db::getInstance()->getVersion(),
            'Shop URL' => $this->context->shop ? $this->context->shop->getBaseURL(true) : '',
            'Shop Name' => Configuration::get('PS_SHOP_NAME'),
        ];

        $moduleConfigurations = [
            'Test mode' => Configuration::get(Config::SHIPMENT_TEST_MODE) ? 'Yes' : 'No',
            'API country' => Configuration::get(Config::WEB_SERVICE_COUNTRY),
            'Track logs' => Configuration::get(Config::TRACK_LOGS) ? 'Yes' : 'No',
        ];

        $psSettings = [
            'Default country' => Configuration::get('PS_COUNTRY_DEFAULT'),
            'Default currency' => Configuration::get('PS_CURRENCY_DEFAULT'),
            'Default language' => Configuration::get('PS_LANG_DEFAULT'),
            'Round mode' => Configuration::get('PS_PRICE_ROUND_MODE'),
            'Round type' => Configuration::get('PS_ROUND_TYPE'),
            'PHP memory limit' => ini_get('memory_limit'),
        ];

        fputcsv($fd, array_keys($storeInfo), ';', $textDelimiter);
        fputcsv($fd, array_values($storeInfo), ';', $textDelimiter);
        fputcsv($fd, [], ';', $textDelimiter);

        $moduleConfigInfo = "**Module configurations:**\n";
        foreach ($moduleConfigurations as $key => $value) {
            $moduleConfigInfo .= '- ' . $key . ': ' . $value . "\n";
        }

        $psSettingsInfo = "**Prestashop settings:**\n";
        foreach ($psSettings as $key => $value) {
            $psSettingsInfo .= '- ' . $key . ': ' . $value . "\n";
        }

        fputcsv($fd, [$moduleConfigInfo], ';', $textDelimiter);
        fputcsv($fd, [$psSettingsInfo], ';', $textDelimiter);
        fputcsv($fd, [], ';', $textDelimiter);

        fputcsv($fd, [
            $this->module->l('ID', self::FILE_NAME),
            $this->module->l('Severity', self::FILE_NAME),
            $this->module->l('Message', self::FILE_NAME),
            $this->module->l('Request', self::FILE_NAME),
            $this->module->l('Response', self::FILE_NAME),
            $this->module->l('Context', self::FILE_NAME),
            $this->module->l('Date', self::FILE_NAME),
        ], ';', $textDelimiter);

        $rows = Db::getInstance()->executeS(
            'SELECT id_dpd_log, request, response, status, date_add FROM `' . _DB_PREFIX_ . pSQL($this->table) . '` ORDER BY id_dpd_log ASC'
        );
        if ($rows === false) {
            $rows = [];
        }

        foreach ($rows as $row) {
            $message = (string) ($row['response'] ?? '');
            $decodedRes = $message !== '' ? json_decode($message, true) : null;
            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedRes) && isset($decodedRes['message'])) {
                $message = (string) $decodedRes['message'];
            }

            $endpoint = '';
            $decodedReq = isset($row['request']) ? json_decode((string) $row['request'], true) : null;
            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedReq) && !empty($decodedReq['endpoint'])) {
                $endpoint = basename(strtok((string) $decodedReq['endpoint'], '?'));
            }

            fputcsv($fd, [
                $row['id_dpd_log'],
                $row['status'],
                $message,
                $row['request'],
                $row['response'],
                $endpoint,
                $row['date_add'],
            ], ';', $textDelimiter);
        }

        fclose($fd);
        exit;
    }

    public function displayAjaxGetLog()
    {
        $logId = (int) Tools::getValue('log_id');
        $log = new DPDLog($logId);

        if (!Validate::isLoadedObject($log)) {
            $this->ajaxDie(json_encode([
                'error' => true,
                'message' => $this->module->l('No log information found.', self::FILE_NAME),
            ]));
        }

        $this->ajaxDie(json_encode([
            'error' => false,
            'log' => [
                self::LOG_INFORMATION_TYPE_REQUEST => $log->request,
                self::LOG_INFORMATION_TYPE_RESPONSE => $log->response,
            ],
        ]));
    }

    private function initList()
    {
        $this->list_no_link = true;
        $this->_select = 'a.status AS severity, a.response AS message, a.request AS context';

        $this->fields_list = [
            'id_dpd_log' => [
                'title' => $this->module->l('ID', self::FILE_NAME),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
                'havingFilter' => true,
            ],
            'severity' => [
                'title' => $this->module->l('Severity (1-4)', self::FILE_NAME),
                'align' => 'text-center',
                'callback' => 'printSeverity',
                'search' => false,
                'orderby' => false,
                'remove_onclick' => true,
            ],
            'message' => [
                'title' => $this->module->l('Message', self::FILE_NAME),
                'callback' => 'printMessage',
                'search' => false,
                'orderby' => false,
                'remove_onclick' => true,
            ],
            'request' => [
                'title' => $this->module->l('Request', self::FILE_NAME),
                'align' => 'text-center',
                'callback' => 'printRequestButton',
                'orderby' => false,
                'search' => false,
                'remove_onclick' => true,
            ],
            'response' => [
                'title' => $this->module->l('Response', self::FILE_NAME),
                'align' => 'text-center',
                'callback' => 'printResponseButton',
                'orderby' => false,
                'search' => false,
                'remove_onclick' => true,
            ],
            'context' => [
                'title' => $this->module->l('Context', self::FILE_NAME),
                'callback' => 'printContext',
                'search' => false,
                'orderby' => false,
                'remove_onclick' => true,
            ],
            'date_add' => [
                'title' => $this->module->l('Date', self::FILE_NAME),
                'align' => 'right',
                'type' => 'datetime',
                'havingFilter' => true,
            ],
        ];
    }

    private function getDisplayButton($logId, $data, $logInformationType)
    {
        if (empty($data)) {
            return '--';
        }

        $this->context->smarty->assign([
            'log_id' => $logId,
            'log_information_type' => $logInformationType,
        ]);

        return $this->context->smarty->fetch(
            $this->module->getLocalPath() . 'views/templates/admin/logs/log_modal.tpl'
        );
    }
}

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

class TabService
{
    const FILE_NAME = 'TabService';

    /**
     * @var DPDBaltics
     */
    private $module;

    public function __construct(DPDBaltics $module)
    {
        $this->module = $module;
    }

    /**
     * @return array
     */
    public function getTabs()
    {
        return [
            [
                'name' => $this->module->displayName,
                'class_name' => DPDBaltics::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'ParentClassName' => 'AdminParentShipping',
                'parent' => 'AdminParentShipping',
                'visible' => true
            ],
            [
                'name' => $this->module->l('Shipment', self::FILE_NAME),
                'class_name' => DPDBaltics::ADMIN_SHIPMENT_CONTROLLER,
                'ParentClassName' => DPDBaltics::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'parent' => DPDBaltics::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'module_tab' => true,
                'visible' => true,
            ],
            [
                'name' => $this->module->l('Orders returns', self::FILE_NAME),
                'class_name' => DPDBaltics::ADMIN_ORDER_RETURN_CONTROLLER,
                'ParentClassName' => DPDBaltics::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'parent' => DPDBaltics::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'module_tab' => true,
                'visible' => true,
            ],
            [
                'name' => $this->module->l('Collection request', self::FILE_NAME),
                'class_name' => DPDBaltics::ADMIN_COLLECTION_REQUEST_CONTROLLER,
                'ParentClassName' => DPDBaltics::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'parent' => DPDBaltics::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'module_tab' => true,
                'visible' => true,
            ],
            [
                'name' => $this->module->l('Courier request', self::FILE_NAME),
                'class_name' => DPDBaltics::ADMIN_COURIER_REQUEST_CONTROLLER,
                'ParentClassName' => DPDBaltics::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'parent' => DPDBaltics::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'module_tab' => true,
                'visible' => true,
            ],
            [
                'name' => $this->module->l('Addresses', self::FILE_NAME),
                'class_name' => DPDBaltics::ADMIN_ADDRESS_TEMPLATE_CONTROLLER,
                'ParentClassName' => DPDBaltics::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'parent' => DPDBaltics::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'module_tab' => true,
                'visible' => true,
            ],
            [
                'name' => $this->module->l('Price rules', self::FILE_NAME),
                'class_name' => DPDBaltics::ADMIN_PRICE_RULES_CONTROLLER,
                'ParentClassName' => DPDBaltics::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'parent' => DPDBaltics::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'module_tab' => true,
                'visible' => true,
            ],
            [
                'name' => $this->module->l('Products', self::FILE_NAME),
                'class_name' => DPDBaltics::ADMIN_PRODUCTS_CONTROLLER,
                'ParentClassName' => DPDBaltics::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'parent' => DPDBaltics::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'module_tab' => true,
                'visible' => true,
            ],
            [
                'name' => $this->module->l('Products availability', self::FILE_NAME),
                'class_name' => DPDBaltics::ADMIN_PRODUCT_AVAILABILITY_CONTROLLER,
                'ParentClassName' => DPDBaltics::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'parent' => DPDBaltics::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'module_tab' => true,
                'visible' => false,
            ],
            [
                'name' => $this->module->l('Zones', self::FILE_NAME),
                'class_name' => DPDBaltics::ADMIN_ZONES_CONTROLLER,
                'ParentClassName' => DPDBaltics::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'parent' => DPDBaltics::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'module_tab' => true,
                'visible' => true,
            ],
            [
                'name' => $this->module->l('Shipment Settings', self::FILE_NAME),
                'class_name' => DPDBaltics::ADMIN_SHIPMENT_SETTINGS_CONTROLLER,
                'ParentClassName' => DPDBaltics::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'parent' => DPDBaltics::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'module_tab' => true,
                'visible' => true,
            ],
            [
                'name' => $this->module->l('Basic Settings', self::FILE_NAME),
                'class_name' => DPDBaltics::ADMIN_SETTINGS_CONTROLLER,
                'ParentClassName' => DPDBaltics::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'parent' => DPDBaltics::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'module_tab' => true,
                'visible' => true,
            ],
            [
                'name' => $this->module->l('Import', self::FILE_NAME),
                'class_name' => DPDBaltics::ADMIN_IMPORT_EXPORT_CONTROLLER,
                'ParentClassName' => DPDBaltics::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'parent' => DPDBaltics::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'module_tab' => true,
                'visible' => false,
            ],
            [
                'name' => $this->module->l('Logs', self::FILE_NAME),
                'class_name' => DPDBaltics::ADMIN_LOGS_CONTROLLER,
                'ParentClassName' => DPDBaltics::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'parent' => DPDBaltics::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'module_tab' => true,
                'visible' => false,
            ],
            [
                'name' => $this->module->l('Ajax', self::FILE_NAME),
                'class_name' => DPDBaltics::ADMIN_AJAX_CONTROLLER,
                'ParentClassName' => DPDBaltics::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'parent' => DPDBaltics::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'module_tab' => true,
                'visible' => false,
            ],
            [
                'name' => $this->module->l('Pudo Ajax', self::FILE_NAME),
                'class_name' => DPDBaltics::ADMIN_PUDO_AJAX_CONTROLLER,
                'ParentClassName' => -1,
                'module_tab' => true,
                'visible' => false,
            ],
            [
                'name' => $this->module->l('Request support', self::FILE_NAME),
                'class_name' => DPDBaltics::ADMIN_REQUEST_SUPPORT_CONTROLLER,
                'ParentClassName' => DPDBaltics::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'parent' => DPDBaltics::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'module_tab' => true,
                'visible' => false,
            ],
            [
                'name' => $this->module->l('Shipment ajax', self::FILE_NAME),
                'class_name' => DPDBaltics::ADMIN_AJAX_SHIPMENTS_CONTROLLER,
                'ParentClassName' => DPDBaltics::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'parent' => DPDBaltics::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'module_tab' => true,
                'visible' => false,
            ],
            [
                'name' => $this->module->l('Ajax on-board', self::FILE_NAME),
                'class_name' => DPDBaltics::ADMIN_AJAX_ON_BOARD_CONTROLLER,
                'ParentClassName' => -1,
            ],
        ];
    }

    /**
     * Filter visible tabs to handle in javascript for ps versiosns below 1704
     */
    public function getTabsClassNames($visible = true)
    {
        $filtered = [];
        $tabs = $this->getTabs();


        foreach ($tabs as $tab) {

            if (!$visible) {
                $filtered[] = $tab['class_name'];
            }
            if (isset($tab['visible']) && $tab['visible']) {
                $filtered[] = $tab['class_name'];
            }
        }
        return $filtered;
    }
}

<?php

namespace Invertus\dpdBaltics\Infrastructure\Bootstrap;

use Invertus\dpdBaltics\Infrastructure\Adapter\ModuleFactory;

class ModuleTabs
{
    const FILE_NAME = 'TabService';

    /**
     * Controller for displaying dpd links in prestashop's menu
     */
    const ADMIN_DPDBALTICS_MODULE_CONTROLLER = 'AdminDPDBalticsModule';

    const ADMIN_ZONES_CONTROLLER = 'AdminDPDBalticsZones';
    const ADMIN_PRODUCT_AVAILABILITY_CONTROLLER = 'AdminDPDBalticsProductsAvailability';
    const ADMIN_PRODUCTS_CONTROLLER = 'AdminDPDBalticsProducts';
    const ADMIN_SETTINGS_CONTROLLER = 'AdminDPDBalticsSettings';
    const ADMIN_SHIPMENT_SETTINGS_CONTROLLER = 'AdminDPDBalticsShipmentSettings';
    const ADMIN_IMPORT_EXPORT_CONTROLLER = 'AdminDPDBalticsImportExport';
    const ADMIN_PRICE_RULES_CONTROLLER = 'AdminDPDBalticsPriceRules';
    const ADMIN_ADDRESS_TEMPLATE_CONTROLLER = 'AdminDPDBalticsAddressTemplate';
    const ADMIN_AJAX_CONTROLLER = 'AdminDPDBalticsAjax';
    const ADMIN_PUDO_AJAX_CONTROLLER = 'AdminDPDBalticsPudoAjax';
    const ADMIN_REQUEST_SUPPORT_CONTROLLER = 'AdminDPDBalticsRequestSupport';
    const ADMIN_AJAX_SHIPMENTS_CONTROLLER = 'AdminDPDBalticsAjaxShipments';
    const ADMIN_LOGS_CONTROLLER = 'AdminDPDBalticsLogs';
    const ADMIN_SHIPMENT_CONTROLLER = 'AdminDPDBalticsShipment';
    const ADMIN_ORDER_RETURN_CONTROLLER = 'AdminDPDBalticsOrderReturn';
    const ADMIN_COLLECTION_REQUEST_CONTROLLER = 'AdminDPDBalticsCollectionRequest';
    const ADMIN_COURIER_REQUEST_CONTROLLER = 'AdminDPDBalticsCourierRequest';
    const ADMIN_AJAX_ON_BOARD_CONTROLLER = 'AdminDPDAjaxOnBoard';


    /**
     * @var \DPDBaltics
     */
    private $module;

    public function __construct(ModuleFactory $moduleFactory)
    {
        $this->module = $moduleFactory->getModule();
    }

    /**
     * @return array
     */
    public function getTabs()
    {
        return [
            [
                'name' => $this->module->displayName,
                'class_name' => self::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'ParentClassName' => 'AdminParentShipping',
                'parent' => 'AdminParentShipping',
                'visible' => true
            ],
            [
                'name' => $this->module->l('Shipment', self::FILE_NAME),
                'class_name' => self::ADMIN_SHIPMENT_CONTROLLER,
                'ParentClassName' => self::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'parent' => self::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'module_tab' => true,
                'visible' => true,
            ],
            [
                'name' => $this->module->l('Orders returns', self::FILE_NAME),
                'class_name' => self::ADMIN_ORDER_RETURN_CONTROLLER,
                'ParentClassName' => self::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'parent' => self::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'module_tab' => true,
                'visible' => true,
            ],
            [
                'name' => $this->module->l('Collection request', self::FILE_NAME),
                'class_name' => self::ADMIN_COLLECTION_REQUEST_CONTROLLER,
                'ParentClassName' => self::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'parent' => self::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'module_tab' => true,
                'visible' => true,
            ],
            [
                'name' => $this->module->l('Courier request', self::FILE_NAME),
                'class_name' => self::ADMIN_COURIER_REQUEST_CONTROLLER,
                'ParentClassName' => self::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'parent' => self::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'module_tab' => true,
                'visible' => true,
            ],
            [
                'name' => $this->module->l('Addresses', self::FILE_NAME),
                'class_name' => self::ADMIN_ADDRESS_TEMPLATE_CONTROLLER,
                'ParentClassName' => self::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'parent' => self::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'module_tab' => true,
                'visible' => true,
            ],
            [
                'name' => $this->module->l('Price rules', self::FILE_NAME),
                'class_name' => self::ADMIN_PRICE_RULES_CONTROLLER,
                'ParentClassName' => self::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'parent' => self::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'module_tab' => true,
                'visible' => true,
            ],
            [
                'name' => $this->module->l('Products', self::FILE_NAME),
                'class_name' => self::ADMIN_PRODUCTS_CONTROLLER,
                'ParentClassName' => self::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'parent' => self::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'module_tab' => true,
                'visible' => true,
            ],
            [
                'name' => $this->module->l('Products availability', self::FILE_NAME),
                'class_name' => self::ADMIN_PRODUCT_AVAILABILITY_CONTROLLER,
                'ParentClassName' => self::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'parent' => self::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'module_tab' => true,
                'visible' => false,
            ],
            [
                'name' => $this->module->l('Zones', self::FILE_NAME),
                'class_name' => self::ADMIN_ZONES_CONTROLLER,
                'ParentClassName' => self::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'parent' => self::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'module_tab' => true,
                'visible' => true,
            ],
            [
                'name' => $this->module->l('Shipment Settings', self::FILE_NAME),
                'class_name' => self::ADMIN_SHIPMENT_SETTINGS_CONTROLLER,
                'ParentClassName' => self::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'parent' => self::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'module_tab' => true,
                'visible' => true,
            ],
            [
                'name' => $this->module->l('Basic Settings', self::FILE_NAME),
                'class_name' => self::ADMIN_SETTINGS_CONTROLLER,
                'ParentClassName' => self::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'parent' => self::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'module_tab' => true,
                'visible' => true,
            ],
            [
                'name' => $this->module->l('Import', self::FILE_NAME),
                'class_name' => self::ADMIN_IMPORT_EXPORT_CONTROLLER,
                'ParentClassName' => self::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'parent' => self::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'module_tab' => true,
                'visible' => false,
            ],
            [
                'name' => $this->module->l('Logs', self::FILE_NAME),
                'class_name' => self::ADMIN_LOGS_CONTROLLER,
                'ParentClassName' => self::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'parent' => self::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'module_tab' => true,
                'visible' => false,
            ],
            [
                'name' => $this->module->l('Ajax', self::FILE_NAME),
                'class_name' => self::ADMIN_AJAX_CONTROLLER,
                'ParentClassName' => self::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'parent' => self::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'module_tab' => true,
                'visible' => false,
            ],
            [
                'name' => $this->module->l('Pudo Ajax', self::FILE_NAME),
                'class_name' => self::ADMIN_PUDO_AJAX_CONTROLLER,
                'ParentClassName' => -1,
                'module_tab' => true,
                'visible' => false,
            ],
            [
                'name' => $this->module->l('Request support', self::FILE_NAME),
                'class_name' => self::ADMIN_REQUEST_SUPPORT_CONTROLLER,
                'ParentClassName' => self::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'parent' => self::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'module_tab' => true,
                'visible' => false,
            ],
            [
                'name' => $this->module->l('Shipment ajax', self::FILE_NAME),
                'class_name' => self::ADMIN_AJAX_SHIPMENTS_CONTROLLER,
                'ParentClassName' => self::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'parent' => self::ADMIN_DPDBALTICS_MODULE_CONTROLLER,
                'module_tab' => true,
                'visible' => false,
            ],
            [
                'name' => $this->module->l('Ajax on-board', self::FILE_NAME),
                'class_name' => self::ADMIN_AJAX_ON_BOARD_CONTROLLER,
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
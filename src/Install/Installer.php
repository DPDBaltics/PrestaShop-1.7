<?php

namespace Invertus\dpdBaltics\Install;

use Carrier;
use Configuration;
use Country;
use Db;
use DbQuery;
use DPDBaltics;
use DPDParcel;
use DPDShipment;
use Exception;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Service\Carrier\CreateCarrierService;
use Invertus\dpdBaltics\Factory\TabFactory;
use Invertus\dpdBaltics\Service\Label\LabelPositionService;
use Invertus\dpdBaltics\Uninstaller\ModuleTabs\ModuleTabsUninstaller;
use Invertus\psModuleTabs\Object\TabsCollection;
use Invertus\psModuleTabs\Service\TabsInitializer;
use Invertus\psModuleTabs\Service\TabsUninstaller;
use Symfony\Component\Validator\Constraints\Count;
use Tools;
use Validate;

class Installer
{
    const FILE_NAME = 'Installer';
    
    const DB_ACTION_INSTALL = 'install';
    const DB_ACTION_UNINSTALL = 'uninstall';
    const DEFAULT_EMAIL_LANGUAGE_ISO_CODE = 'en';

    /**
     * @var DPDBaltics
     */
    private $module;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var TabFactory
     */
    private $tabFactory;

    /**
     * @var CreateCarrierService
     */
    private $createCarriersService;

    /**
     * Installer constructor.
     * @param DPDBaltics $module
     * @param CreateCarrierService $createCarriersService
     */
    public function __construct(
        DPDBaltics $module,
        TabFactory $tabFactory,
        CreateCarrierService $createCarriersService
    ) {
        $this->module = $module;
        $this->tabFactory = $tabFactory;
        $this->createCarriersService = $createCarriersService;
    }

    /**
     * Get installer errors
     *
     * @return array|string[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return bool
     */
    public function install()
    {
        if (!$this->installConfiguration()) {
            $this->errors[] = $this->module->l('Failed to install configuration', self::FILE_NAME);
            return false;
        }

        if (!$this->installTabs()) {
            $this->errors[] = $this->module->l('Failed to install tabs', self::FILE_NAME);
            return false;
        }

        if (!$this->processDatabase(self::DB_ACTION_INSTALL)) {
            $this->errors[] = $this->module->l('Failed to install database tables', self::FILE_NAME);
            return false;
        }

        if (!$this->createCarriers()) {
            $this->errors[] = $this->module->l('Failed to create carriers', self::FILE_NAME);
            return false;
        }

        if (!$this->registerHooks()) {
            $this->errors[] = $this->module->l('Failed to register hooks', self::FILE_NAME);
            return false;
        }

        return true;
    }

    /**
     * Uninstall controllers, hooks, database & etc.
     *
     * @return bool
     */
    public function uninstall()
    {
        if (!$this->uninstallConfiguration()) {
            $this->errors[] = $this->module->l('Failed to uninstall configuration', self::FILE_NAME);
            return false;
        }

        if (!$this->uninstallTabs()) {
            $this->errors[] = $this->module->l('Failed to uninstall tabs', self::FILE_NAME);
            return false;
        }

        $this->processDeleteCarriers();

        if (!$this->processDatabase(self::DB_ACTION_UNINSTALL)) {
            $this->errors[] = $this->module->l('Failed to uninstall database tables', self::FILE_NAME);
            return false;
        }

        return true;
    }

    /**
     * Process database install/uninstall
     *
     * @param string $action Can be "install" or "uninstall"
     *
     * @return bool
     */
    private function processDatabase($action)
    {
        $pathToSql = sprintf('sql/%s/*.sql', $action);
        $sqlFiles = glob($this->module->getLocalPath() . $pathToSql);

        foreach ($sqlFiles as $sqlFile) {
            $sqlStatements = $this->getSqlStatements($sqlFile);

            if (!$this->execute($sqlStatements)) {
                if (self::DB_ACTION_INSTALL === $action) {
                    $this->errors[] = sprintf(
                        $this->module->l('Failed to execute SQL in file %s', self::FILE_NAME),
                        $sqlFile
                    );
                }
                return false;
            }
        }

        return true;
    }

    /**
     * Execute SQL statements
     *
     * @param $sqlStatements
     *
     * @return bool
     */
    private function execute($sqlStatements)
    {
        try {
            $result = Db::getInstance()->execute($sqlStatements);
        } catch (Exception $e) {
            return false;
        }

        return (bool)$result;
    }

    /**
     * Format and get sql statements from file
     *
     * @param string $fileName
     *
     * @return string
     */
    private function getSqlStatements($fileName)
    {
        $sqlStatements = Tools::file_get_contents($fileName);
        $sqlStatements = str_replace('PREFIX_', _DB_PREFIX_, $sqlStatements);
        $sqlStatements = str_replace('ENGINE_TYPE', _MYSQL_ENGINE_, $sqlStatements);
        $sqlStatements = str_replace('DB_NAME', _DB_NAME_, $sqlStatements);

        return $sqlStatements;
    }

    /**
     * @return bool
     */
    public function createCarriers()
    {
        try {
            $result = $this->createCarriersService->createCarriers(Config::getProducts());
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            $result = false;
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getDefaultConfiguration()
    {
        return [
            Config::SHIPMENT_TEST_MODE => 1,
            Config::WEB_SERVICE_USERNAME => '',
            Config::WEB_SERVICE_PASSWORD => '',
            Config::WEB_SERVICE_COUNTRY => $this->getDefaultWSCountry(),
            Config::SHOW_CARRIERS_IN_PRODUCT_PAGE => 0,
            Config::TRACK_LOGS => 1,
            Config::PARCEL_TRACKING => 0,
            Config::PARCEL_RETURN => 0,
            Config::DOCUMENT_RETURN => 0,
            Config::PICKUP_MAP => 0,
            Config::GOOGLE_API_KEY => '',
            Config::PARCEL_DISTRIBUTION => DPDParcel::DISTRIBUTION_NONE,
            config::AUTO_VALUE_FOR_REF => DPDShipment::AUTO_VAL_REF_NONE,
            config::LABEL_PRINT_OPTION => config::PRINT_OPTION_DOWNLOAD,
            config::DEFAULT_LABEL_FORMAT => LabelPositionService::PDF_FORMAT_A4,
            config::DEFAULT_LABEL_POSITION => LabelPositionService::LB_POSITION_1,
            Config::ON_BOARD_INFO => '',
            Config::EXPORT_FIELD_SEPARATOR => ';',
            Config::EXPORT_FIELD_MULTIPLE_SEPARATOR => ',',
            Config::IMPORT_FIELD_MULTIPLE_SEPARATOR => ',',
            Config::EXPORT_OPTION => Config::IMPORT_EXPORT_OPTION_ZONES,
            Config::IMPORT_OPTION => Config::IMPORT_EXPORT_OPTION_ZONES,
            Config::IMPORT_LINES_SKIP => 1,
            Config::IMPORT_FIELD_SEPARATOR => ';',
            Config::IMPORT_DELETE_OLD_DATA => 0,

            /** On-board */
            Config::ON_BOARD_TURNED_ON => 1,
            Config::ON_BOARD_PAUSE => 0,
            Config::ON_BOARD_STEP => Config::STEP_MAIN_1,
            Config::ON_BOARD_MANUAL_CONFIG_CURRENT_PART => 1,
            Config::DPDBALTICS_HASH_TOKEN => Tools::hash('dpdbaltics_token'),
            Config::PARCEL_SHOP_DISPLAY => Config::PARCEL_SHOP_DISPLAY_BLOCK,
        ];
    }

    private function installConfiguration()
    {
        $configuration = $this->getDefaultConfiguration();

        foreach ($configuration as $name => $value) {
            if (!Configuration::updateValue($name, $value, false, 0, 0)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Uninstall defaulf module configuration
     *
     * @return bool
     */
    private function uninstallConfiguration()
    {
        $configuration = array_keys($this->getDefaultConfiguration());

        foreach ($configuration as $name) {
            if (!Configuration::deleteByName($name)) {
                return false;
            }
        }

        return true;
    }

    private function installTabs()
    {
        if (!Config::isPrestashopVersionBelow174()) {
            return true;
        }
        /** @var TabsCollection $tabCollection */
        $tabCollection = $this->tabFactory->getTabsCollection();
        $tabsInitializer = new TabsInitializer(_PS_VERSION_, $tabCollection);

        return $tabsInitializer->initializeTabsByPsVersion();
    }

    private function uninstallTabs()
    {
        if (!Config::isPrestashopVersionBelow174()) {
            return true;
        }

        /** @var TabsCollection $tabCollection */
        $tabCollection = $this->tabFactory->getTabsCollection()->getTabsCollection();

        $tabsUninstaller = new ModuleTabsUninstaller($tabCollection);

        return $tabsUninstaller->uninstallTabs();
    }
    private function processDeleteCarriers()
    {
        $sql = 'SHOW TABLES LIKE "ps_dpd_product";';
        $carrierTable = Db::getInstance()->executeS($sql);

        if (!$carrierTable) {
            return;
        }
        $query = new DbQuery();
        $query->select('`id_reference`');
        $query->from('dpd_product');
        $idReferences = Db::getInstance()->executeS($query);

        if (empty($idReferences)) {
            return;
        }
        foreach ($idReferences as $id) {
            $carrier = Carrier::getCarrierByReference($id['id_reference']);
            if (Validate::isLoadedObject($carrier)) {
                $carrier->deleted = 1;
                $carrier->update();
            }
        }
    }

    private function getDefaultWSCountry()
    {
        $defaultCountryId = Configuration::get('PS_COUNTRY_DEFAULT');
        $defaultCountry = new Country($defaultCountryId);

        switch ($defaultCountry->iso_code) {
            case Config::ESTONIA_ISO_CODE:
                $wsCountry = Config::ESTONIA_ISO_CODE;
                break;
            case Config::LITHUANIA_ISO_CODE:
                $wsCountry = Config::LITHUANIA_ISO_CODE;
                break;
            default:
                $wsCountry = Config::LATVIA_ISO_CODE;
        }

        return $wsCountry;
    }

    private function registerHooks()
    {
        $hooks = $this->hooks();

        if (empty($hooks)) {
            return true;
        }

        return $this->module->registerHook($hooks);
    }

    /**
     * List of hooks to register
     *
     * @return array|string[]
     */
    private function hooks()
    {
        return [
            'displayCarrierExtraContent',
            'actionFrontControllerSetMedia',
            'displayProductPriceBlock',
            'actionObjectOrderUpdateAfter',
            'actionValidateOrder',
            'displayAdminOrder',
            'displayAdminOrderTabContent',
            'actionAdminControllerSetMedia',
            'actionValidateStepComplete',
            'displayOrderDetail',
            'displayBackOfficeTop',
            'displayOrderDetail',
            'actionDispatcher',
            'actionAdminOrdersListingFieldsModifier',
            'displayAdminListBefore',
            'actionCarrierProcess',
            'displayAdminOrderTabContent',
            'actionOrderGridDefinitionModifier'
        ];
    }
}

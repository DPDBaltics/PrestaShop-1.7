<?php

use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Controller\AbstractAdminController;
use Invertus\dpdBaltics\Repository\AddressTemplateRepository;
use Invertus\dpdBaltics\Repository\CodPaymentRepository;
use Invertus\dpdBaltics\Repository\PaymentRepository;
use Invertus\dpdBaltics\Repository\ProductRepository;
use Invertus\dpdBaltics\Service\Label\LabelPositionService;
use Invertus\dpdBaltics\Templating\InfoBlockRender;
use Invertus\dpdBaltics\Util\CountryUtility;

require_once dirname(__DIR__).'/../vendor/autoload.php';

class AdminDPDBalticsShipmentSettingsController extends AbstractAdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->override_folder = 'field-option-swap/';
        $this->tpl_folder = 'field-option-swap/';
    }

    public function postProcess()
    {
        if (!Tools::isSubmit('submitOptionsconfiguration')) {
            return parent::postProcess();
        }

        parent::postProcess();

        // set label position to 1 if format is A6
        $isA4Format = strpos(Configuration::get(Config::DEFAULT_LABEL_FORMAT), 'A4');
        if (false === $isA4Format) {
            Configuration::updateValue(Config::DEFAULT_LABEL_POSITION, 1);
        }

        if (!empty($this->errors)) {
            return;
        }

        /** @var CodPaymentRepository $codPaymentRepo */
        $codPaymentRepo = $this->module->getModuleContainer(CodPaymentRepository::class);

        if (!$codPaymentRepo->removeCodPaymentModules()) {
            $this->errors[] = $this->l('Failed to delete COD payment methods');
        }

        if (Tools::getIsset('payments_selected')) {
            $codPaymentModules = Tools::getValue('payments_selected');

            $codPaymentsSqlArray = [];

            foreach ($codPaymentModules as $codPaymentModule) {
                $codPaymentsSqlArray[] = '(' . (int)$codPaymentModule . ')';
            }

            if (!$codPaymentRepo->addCodPaymentModules($codPaymentsSqlArray)) {
                $this->errors[] = $this->l('Failed to add COD payment methods');
            }
        }

        if (empty($this->errors)) {
            $this->redirect_after =
                $this->context->link->getAdminLink($this->controller_name, true, [], ['conf' => 6]);
        } else {
            $this->confirmations = [];
        }
    }

    /**
     * Initialize controller with options
     */
    public function init()
    {
        $parentReturn = parent::init();
        $this->initOptions();

        if (!Tools::isSubmit('submitOptionsconfiguration')) {
            return $parentReturn;
        }

        parent::init();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addJS($this->getModuleJSUri() . 'pickup_map_settings.js');
        $this->addJS($this->getModuleJSUri() . 'swap.js');
        $this->addJS($this->getModuleJSUri() . 'custom_select.js');
        $this->addJS($this->getModuleJSUri() . 'label_position.js');
        $this->addJS($this->getModuleJSUri() . 'shipment_settings.js');
        $this->addCSS($this->getModuleCssUri() . 'customSelect/custom-select.css');
    }

    /**
     * Options form definition
     */
    protected function initOptions()
    {
        /** @var AddressTemplateRepository $addressTemplateRepo */
        $addressTemplateRepo = $this->module->getModuleContainer(AddressTemplateRepository::class);
        /** @var InfoBlockRender $infoBlockRender */
        $infoBlockRender = $this->module->getModuleContainer()->get(InfoBlockRender::class);

        $infoBlockText = $this->module->l('Please move COD modules to the right, non-COD modules leave on the left');
        $returnServiceAddresses = $addressTemplateRepo->getReturnServiceAddressTemplates();
        if (!$returnServiceAddresses) {
            $addressTabRedirect = $this->context->link->getAdminLink(DPDBaltics::ADMIN_ADDRESS_TEMPLATE_CONTROLLER);
            $this->context->smarty->assign(
                [
                    'addressTabRedirect' => $addressTabRedirect
                ]
            );

            if (Configuration::get(Config::PARCEL_RETURN)) {
                $this->warnings[] =
                    sprintf(
                        $this->l('To allow returns for your customers, you need to create return %s address %s in Addresses tab'),
                        $this->module->display($this->module->getPathUri(), 'views/templates/admin/redirect/redirect-opening.tpl'),
                        $this->module->display($this->module->getPathUri(), 'views/templates/admin/redirect/redirect-closing.tpl')
                    );
            }

        }

        /** @var LabelPositionService $labelPositionService */
        $labelPositionService = $this->module->getModuleContainer(LabelPositionService::class);

        /** @var CodPaymentRepository $codPaymentRepo */
        $codPaymentRepo = $this->module->getModuleContainer(CodPaymentRepository::class);
        $codPaymentModules = $codPaymentRepo->getCodPaymentModules();
        $this->context->smarty->assign('swap', [
            'name' => 'payments',
            'disabled' => !$this->getPaymentMethods() ? true : false,
            'fields_value' => $codPaymentModules,
            'options' => [
                'query' => $this->getPaymentMethods(),
                'id' => 'id_module',
                'name' => 'display_name',
                'type' => 'name'
            ]
        ]);

        $parcelReturn = [
            'title' => $this->l('Parcel return'),
            'validation' => 'isBool',
            'cast' => 'intval',
            'type' => 'bool'
        ];

        if (CountryUtility::isEstonia()) {
            $parcelReturn = [
                'title' => $this->l('Parcel return'),
                'validation' => 'isBool',
                'cast' => 'intval',
                'type' => 'hidden',
                'disabled' => true
            ];
        }

        $this->context->smarty->assign('googleMapsApiKeyLink', Config::GOOGLE_MAPS_API_KEY_LINK);
        $this->fields_options = [
            'shipping_configuration' => [
                'title' => $this->l('Shipping configuration'),
                'icon' => 'dpd-icon-settings',
                'fields' => [
                    Config::PARCEL_TRACKING => [
                        'title' => $this->l('Parcel tracking'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool',
                    ],
                    Config::PARCEL_RETURN => $parcelReturn,
                    Config::DOCUMENT_RETURN => [
                        'title' => $this->l('Document return'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool',
                    ],
                    Config::PICKUP_MAP => [
                        'title' => $this->l('Pickup map'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool',
                    ],
                    Config::GOOGLE_API_KEY => [
                        'title' => $this->l('Google maps Api key'),
                        'validation' => 'isCleanHtml',
                        'type' => 'text',
                        'class' => 'fixed-width-xxl',
                        'desc' => $this->context->smarty->fetch($this->getTemplatePath() . 'api-key-description.tpl'),
                    ],
                    Config::PARCEL_SHOP_DISPLAY => [
                        'title' => $this->l('Pickup display'),
                        'type' => 'radio',
                        'choices' => [
                            Config::PARCEL_SHOP_DISPLAY_LIST
                            => $this->l('Show pickup points in list'),
                            Config::PARCEL_SHOP_DISPLAY_BLOCK
                            => $this->l('Show pickup points in blocks'),
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
            'parcel_configuration' => [
                'title' => $this->l('Shipment configuration'),
                'icon' => 'dpd-icon-settings',
                'fields' => [
                    Config::PARCEL_DISTRIBUTION => [
                        'title' => $this->l('Product distribution'),
                        'type' => 'radio',
                        'choices' => [
                            DPDParcel::DISTRIBUTION_NONE
                            => $this->l('All products in same shipment'),
                            DPDParcel::DISTRIBUTION_PARCEL_PRODUCT
                            => $this->l('Each product in separate parcel'),
                            DPDParcel::DISTRIBUTION_PARCEL_QUANTITY
                            => $this->l('Each product quantity in separate parcel'),
                        ],
                    ],
                    Config::AUTO_VALUE_FOR_REF => [
                        'title' => $this->l('Automatic value for Reference 1 in Shipment'),
                        'type' => 'select',
                        'list' => [
                            [
                                'value' => DPDShipment::AUTO_VAL_REF_NONE,
                                'name' => $this->l('None'),
                            ],
                            [
                                'value' => DPDShipment::AUTO_VAL_REF_ORDER_ID,
                                'name' => $this->l('Order Id'),
                            ],
                            [
                                'value' => DPDShipment::AUTO_VAL_REF_ORDER_REF,
                                'name' => $this->l('Order Reference'),
                            ],
                        ],
                        'identifier' => 'value',
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
            'label_configuration' => [
                'title' => $this->l('Label configuration'),
                'icon' => 'dpd-icon-settings',
                'fields' => [
                    Config::LABEL_PRINT_OPTION => [
                        'title' => $this->l('Label print option'),
                        'desc' => $this->l('Define label to be printed directly from browser or downloaded'),
                        'type' => 'select',
                        'list' => [
                            [
                                'value' => Config::PRINT_OPTION_DOWNLOAD,
                                'name' => $this->l('Download'),
                            ],
                            [
                                'value' => Config::PRINT_OPTION_BROWSER,
                                'name' => $this->l('Print from browser'),
                            ],
                        ],
                        'identifier' => 'value',
                    ],
                    Config::DEFAULT_LABEL_FORMAT => [
                        'title' => $this->l('Default label format'),
                        'desc' => $this->l('Used when printing labels in order page.'),
                        'type' => 'select',
                        'list' => $labelPositionService->getLabelFormatList(),
                        'identifier' => 'value',
                    ],
                    Config::DEFAULT_LABEL_POSITION => [
                        'title' => $this->module->l('Default label position'),
                        'desc' => $this->l('Used when printing labels in order page.'),
                        'type' => 'select',
                        'list' => $labelPositionService->getLabelPositionList(),
                        'identifier' => 'value',
                        'form_group_class' => 'DPD_DEFAULT_LABEL_POSITION'
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
            'cod_payment_configuration' => [
                'title' => $this->l('COD Payment configuration'),
                'icon' => 'dpd-icon-settings',
                'fields' => [
                    Config::ON_BOARD_INFO => [
                        'type' => 'free',
                        'desc' => $infoBlockRender->getInfoBlockTemplate($infoBlockText),
                        'class' => 'hidden',
                        'form_group_class' => 'dpd-info-block',
                    ],
                    Config::COD_PAYMENT_SWAP => [
                        'title' => $this->l('Payment methods'),
                        'type' => 'swap',
                        'class' => 'cod-payments-container',
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
    }

    private function getPaymentMethods()
    {
        $paymentMethods = (array)PaymentModule::getInstalledPaymentModules();

        foreach ($paymentMethods as $key => $item) {
            $idModule = $item['id_module'];
            $module = Module::getInstanceById($idModule);

            if ($module) {
                $paymentMethods[$key]['display_name'] = $module->displayName;
            } else {
                unset($paymentMethods[$key]);
            }
        }

        return $paymentMethods;
    }
}

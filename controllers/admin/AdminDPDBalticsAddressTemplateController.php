<?php

use Invertus\dpdBaltics\Builder\Template\Admin\PhoneInputBuilder;
use Invertus\dpdBaltics\Builder\Template\SearchBoxBuilder;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Controller\AbstractAdminController;
use Invertus\dpdBaltics\Repository\AddressRepository;
use Invertus\dpdBaltics\Repository\PhonePrefixRepository;
use Invertus\dpdBaltics\Repository\ShopRepository;
use Invertus\dpdBaltics\Service\Address\AddressTemplateService;
use Invertus\dpdBaltics\Util\CountryUtility;

require_once dirname(__DIR__).'/../vendor/autoload.php';

class AdminDPDBalticsAddressTemplateController extends AbstractAdminController
{

    /**
     * @var DPDAddressTemplate
     */
    public $object;

    /**
     * @var AddressTemplateService
     */
    private $addressTemplateService;

    public function __construct()
    {
        $this->allow_export = true;
        $this->can_import = true;
        $this->className = 'DPDAddressTemplate';
        $this->identifier = DPDAddressTemplate::$definition['primary'];
        $this->table = DPDAddressTemplate::$definition['table'];
        $this->lang = false;

        parent::__construct();
        $this->initList();
        $this->initForm();
        $this->addressTemplateService = $this->module->getModuleContainer()->get('invertus.dpdbaltics.service.address.address_template_service');

    }

    public function renderForm()
    {
        $this->getFieldFormVars();

        return parent::renderForm();
    }

    /**
     * Add custom buttons to toolbar
     */
    public function initToolbar()
    {
        parent::initToolbar();

        if ($this->display !== 'edit' && $this->display !== 'add') {
            $this->toolbar_btn['import'] = [
                'href' => $this->context->link->getAdminLink(
                    'AdminDPDBalticsImportExport',
                    true,
                    [],
                    ['importContr' => Config::IMPORT_EXPORT_OPTION_ADDRESS_TEMPLATES]
                ),
                'desc' => $this->l('Import')
            ];
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        //checks if its add or update or submit
        if (Tools::isSubmit('adddpd_address_template')
            || Tools::isSubmit('updatedpd_address_template')
            || Tools::isSubmit('submitAdddpd_address_template')
        ) {
            $this->addJS($this->module->getLocalPath() . 'views/js/admin/zip_code_hint.js');
            $this->addJS($this->module->getPathUri() . 'views/js/admin/addresses.js');

            Media::addJsDef([
                'dpdAjaxUrl' =>
                    $this->context->link->getAdminLink(DPDBaltics::ADMIN_AJAX_CONTROLLER),
                'inputWarningMessage' => $this->l('Please fill all required fields')
            ]);
        }
        $this->addCSS($this->module->getPathUri() . 'views/css/admin/addresses.css');
    }

    private function getFieldFormVars()
    {
        /** @var ShopRepository $shopRepository */
        $shopRepository = $this->module->getModuleContainer()->get('invertus.dpdbaltics.repository.shop_repository');
        $priceRuleShops = $shopRepository->getAddressTemplateShops($this->object->id);

        $shops = Shop::getShops(true);
        if (count($shops) > 1) {
            $allShopsSelected = false;

            // Checking if "All shops" option is selected
            foreach ($priceRuleShops as $priceRuleShop) {
                if ($priceRuleShop['all_selected_flag']) {
                    $allShopsSelected = true;
                    break;
                }
            }

            $searchBoxName = 'shops_select[]';

            /** @var SearchBoxBuilder $searchBoxBuilder */
            $searchBoxBuilder = $this->module->getModuleContainer()->get('invertus.dpdbaltics.builder.template.search_box_builder');
            $searchBoxPlugin = $searchBoxBuilder->createSearchBox(
                $priceRuleShops,
                $allShopsSelected,
                $searchBoxName
            );

            $this->addCSS($searchBoxPlugin->getCss());
            $this->addJS($searchBoxPlugin->getJs());
            Media::addJsDef($searchBoxPlugin->getJsVars());

            $this->fields_value['search_block_shops'] = $searchBoxPlugin->render();
        }
    }

    private function initForm()
    {
        $this->loadObject(true);
        $dpdCountries = Country::getCountries($this->context->language->id, false);

        $shops = Shop::getShops(true);
        $receiverShop = null;
        if (count($shops) > 1) {
            $receiverShop = [
                'label' => $this->l('Receiver shops'),
                'name' => 'search_block_shops',
                'type' => 'free',
                'form_group_class' => 'dpd-price-rule-shops',
            ];
        }
        $isEECountry = false;
        if (CountryUtility::isEstonia()) {
            $isEECountry = true;
        }

        $this->fields_form = [
            'legend' => [
                'title' => $this->l('Address template'),
            ],
            'input' => [
                [
                    'label' => $this->l('Name'),
                    'type' => 'text',
                    'name' => 'name',
                    'required' => true,
                    'class' => 'fixed-width-xxl',
                ],
                [
                    'label' => $this->l('Address type'),
                    'name' => 'type',
                    'type' => $isEECountry ? 'hidden' : 'radio',
                    'default_value' => DPDAddressTemplate::ADDRESS_TYPE_COLLECTION_REQUEST,
                    'values' => [
                        [
                            'id' => DPDAddressTemplate::ADDRESS_TYPE_COLLECTION_REQUEST,
                            'value' => DPDAddressTemplate::ADDRESS_TYPE_COLLECTION_REQUEST,
                            'label' => $this->l('Collection request'),
                        ],
                        [
                            'id' => DPDAddressTemplate::ADDRESS_TYPE_RETURN_SERVICE,
                            'value' => DPDAddressTemplate::ADDRESS_TYPE_RETURN_SERVICE,
                            'label' => $this->l('Return service'),
                        ],
                    ],
                ],
                [
                    'label' => $this->l('Full name/Company name'),
                    'name' => 'full_name',
                    'type' => 'text',
                    'class' => 'fixed-width-xxl',
                ],
                [
                    'label' => $this->l('Mobile phone'),
                    'name' => 'mobile_phone',
                    'type' => 'free',
                    'class' => 'fixed-width-xxl',
                ],
                [
                    'label' => $this->l('Email address'),
                    'name' => 'email',
                    'type' => 'text',
                    'class' => 'fixed-width-xxl',
                ],
                [
                    'label' => $this->l('Country'),
                    'name' => 'dpd_country_id',
                    'type' => 'select',
                    'class' => 'fixed-width-xxl chosen',
                    'options' => [
                        'id' => 'id_country',
                        'name' => 'name',
                        'query' => $dpdCountries,
                    ],
                ],
                [
                    'label' => $this->l('Zip code'),
                    'name' => 'zip_code',
                    'type' => 'text',
                    'class' => 'fixed-width-xxl',
                ],
                [
                    'label' => $this->l('City/Region'),
                    'name' => 'dpd_city_name',
                    'type' => 'text',
                    'class' => 'fixed-width-xxl',
                ],
                [
                    'label' => $this->l('Address'),
                    'name' => 'address',
                    'type' => 'text',
                    'class' => 'fixed-width-xxl',
                ],
                $receiverShop
            ],
            'submit' => [
                'title' => $this->l('Save'),
            ],
            'buttons' => [
                [
                    'title' => $this->l('Save & Stay'),
                    'icon' => 'process-icon-save',
                    'name' => 'submitAdddpd_address_templateAndStay',
                    'type' => 'submit',
                    'class' => 'pull-right',
                ],
            ],
        ];

        /** @var PhonePrefixRepository $phonePrefixRepository */
        $phonePrefixRepository = $this->module->getModuleContainer('invertus.dpdbaltics.repository.phone_prefix_repository');

        /** @var PhoneInputBuilder $phoneInputBuilder */
        $phoneInputBuilder = $this->module->getModuleContainer('invertus.dpdbaltics.builder.template.admin.phone_input_builder');

        if (Tools::getIsset('submitAdddpd_address_template')) {
            $phoneData['mobile_phone_code'] = Tools::getValue('mobile_phone_code');
            $phoneData['mobile_phone'] = Tools::getValue('mobile_phone');
        } elseif (Tools::getIsset('id_dpd_address_template')) {
            /** @var AddressRepository $addressRepo */
            $addressRepo = $this->module->getModuleContainer('invertus.dpdbaltics.repository.address_repository');
            $phoneData = $addressRepo->getAddressPhonesAndCodes(Tools::getValue('id_dpd_address_template'));
        }

        if (!isset($phoneData) || !$phoneData) {
            $callingCode =
                isset($dpdCountries[0]['countryCallingCode']) ? $dpdCountries[0]['countryCallingCode'] : null;

            $phoneData['mobile_phone_code'] = $callingCode;
            $phoneData['mobile_phone'] = null;
        }

        $phoneData['mobile_phone_code_list'] = $phonePrefixRepository->getCallPrefixes();

        $this->fields_value['mobile_phone'] = $phoneInputBuilder->renderPhoneInput(
            'mobile_phone',
            $phoneData['mobile_phone_code_list'],
            $phoneData['mobile_phone_code'],
            $phoneData['mobile_phone']
        );
    }

    private function initList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->fields_list['id_dpd_address_template'] = [
            'title' => $this->l('ID'),
            'type' => 'text',
        ];

        $this->fields_list['type'] = [
            'title' => $this->l('Type'),
            'type' => 'text',
        ];

        $this->fields_list['name'] = [
            'title' => $this->l('Name'),
            'type' => 'text',
        ];

        $this->fields_list['mobile_phone_code'] = [
            'title' => $this->l('GSM code'),
            'type' => 'text',
        ];

        $this->fields_list['mobile_phone'] = [
            'title' => $this->l('Mobile phone'),
            'type' => 'text',
        ];

        $this->fields_list['country_name'] = [
            'title' => $this->l('Country'),
            'type' => 'free'
        ];
    }

    public function getList($idLang, $orderBy = null, $orderWay = null, $start = 0, $limit = null, $idLangShop = false)
    {
        $this->_select = 'c.name as country_name';
        $this->_join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` c ON c.`id_country`= a.`dpd_country_id` 
        AND c.id_lang = ' . (int)$idLang;

        if (CountryUtility::isEstonia()) {
            $this->_where = 'AND `type` NOT LIKE "return_service"';
        }

        parent::getList($idLang, $orderBy, $orderWay, $start, $limit, $idLangShop);
    }

    public function afterAdd($object)
    {
        $selectedShops = Tools::getValue('shops_select');
        $this->addressTemplateService->addUpdateShops($object, $selectedShops);
        $this->errors = array_merge($this->errors, $this->addressTemplateService->getErrors());

        return parent::afterAdd($object);
    }

    public function afterUpdate($object)
    {
        $selectedShops = Tools::getValue('shops_select');
        $this->addressTemplateService->removeShops((int)$object->id);
        $this->addressTemplateService->addUpdateShops($object, $selectedShops);
        $this->errors = array_merge($this->errors, $this->addressTemplateService->getErrors());

        return parent::afterUpdate($object);
    }

    public function processDelete()
    {
        $addressTemplateId = (int)Tools::getValue('id_dpd_address_template');
        $this->addressTemplateService->removeShops($addressTemplateId);
        $this->errors = array_merge($this->errors, $this->addressTemplateService->getErrors());

        return parent::processDelete();
    }
}

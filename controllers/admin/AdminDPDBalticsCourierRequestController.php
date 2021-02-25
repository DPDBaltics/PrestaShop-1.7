<?php

use Invertus\dpdBaltics\Builder\Template\Admin\PhoneInputBuilder;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Controller\AbstractAdminController;
use Invertus\dpdBaltics\Converter\FormDataConverter;
use Invertus\dpdBaltics\DTO\CourierRequestData;
use Invertus\dpdBaltics\Repository\AddressRepository;
use Invertus\dpdBaltics\Repository\CourierRequestRepository;
use Invertus\dpdBaltics\Repository\PhonePrefixRepository;
use Invertus\dpdBaltics\Service\API\CourierRequestService;
use Invertus\dpdBaltics\Service\Exception\ExceptionService;
use Invertus\dpdBaltics\Util\TimeZoneUtility;
use Invertus\dpdBaltics\Validate\CourierRequest\CourierRequestValidator;
use Invertus\dpdBalticsApi\Exception\DPDBalticsAPIException;

require_once dirname(__DIR__).'/../vendor/autoload.php';

class AdminDPDBalticsCourierRequestController extends AbstractAdminController
{
    public function __construct()
    {
        $this->className = 'DPDCourierRequest';
        $this->table = DPDCourierRequest::$definition['table'];
        $this->identifier = DPDCourierRequest::$definition['primary'];

        parent::__construct();

        $this->loadObject(true);
    }

    public function init()
    {
        $this->content .= $this->context->smarty->fetch($this->getTemplatePath() . 'admin-error-message.tpl');

        if (!$this->isXmlHttpRequest()) {
            $this->initList();
            $this->initForm();
        }

        parent::init();
    }

    private function initForm()
    {
        $this->loadObject(true);
        $dpdCountries = Country::getCountries($this->context->language->id, false);

        $dpdPickupCountryId = null;
        $dpdReceiverCountryId = null;

        $this->multiple_fieldsets = true;

        $date = new DateTime($this->fields_value['shipment_date']);
        $this->fields_value['shipment_date'] = $date->format('Y-m-d');
        $this->fields_form = [
            [
                'form' => [
                    'legend' => [
                        'title' => $this->l('SENDERS` INFORMATION'),
                    ],
                    'input' => [
                        [
                            'label' => $this->l('Prefill with selected address'),
                            'name' => 'prefill_placeholder',
                            'type' => 'free',
                        ],
                        [
                            'label' => $this->l('Full name/Company name'),
                            'name' => 'sender_name',
                            'type' => 'text',
                            'class' => 'fixed-width-xxl',
                            'required' => true,
                        ],
                        [
                            'label' => $this->l('Mobile phone'),
                            'name' => 'sender_phone',
                            'type' => 'free',
                            'class' => 'fixed-width-xxl',
                            'required' => true,
                        ],
                        [
                            'label' => $this->l('Country'),
                            'name' => 'sender_id_ws_country',
                            'type' => 'select',
                            'class' => 'fixed-width-xxl chosen',
                            'required' => true,
                            'options' => [
                                'id' => 'id_country',
                                'name' => 'name',
                                'query' => $dpdCountries,
                            ],
                        ],
                        [
                            'label' => $this->l('Zip code'),
                            'name' => 'sender_postal_code',
                            'type' => 'text',
                            'class' => 'fixed-width-xxl',
                            'required' => true,
                        ],
                        [
                            'label' => $this->l('City/Region'),
                            'name' => 'sender_city',
                            'type' => 'text',
                            'class' => 'fixed-width-xxl',
                            'required' => true,
                        ],
                        [
                            'label' => $this->l('Address'),
                            'name' => 'sender_address',
                            'type' => 'text',
                            'class' => 'fixed-width-xxl',
                            'required' => true,
                        ],
                        [
                            'label' => $this->l('Additional address information'),
                            'name' => 'sender_additional_information',
                            'type' => 'text',
                            'class' => 'fixed-width-xxl',
                            'required' => false,
                        ],
                    ],
                ],
            ],
            [
                'form' => [
                    'legend' => [
                        'title' => $this->l('SHIPMENT INFORMATION'),
                    ],
                    'input' => [
                        [
                            'label' => $this->l('Order Nr'),
                            'name' => 'order_nr',
                            'type' => 'text',
                            'class' => 'fixed-width-xxl',
                            'required' => true,
                        ],
                        [
                            'label' => $this->l('Desired pick-up time'),
                            'name' => 'pick_up_time',
                            'type' => 'datetime',
                            'required' => true,
                        ],
                        [
                            'label' => $this->l('Last pick-up time'),
                            'name' => 'sender_work_until',
                            'type' => 'datetime',
                            'required' => true,
                        ],
                        [
                            'label' => $this->l('Weight'),
                            'name' => 'weight',
                            'type' => 'text',
                            'class' => 'fixed-width-xxl',
                            'required' => true,
                        ],
                        [
                            'label' => $this->l('Parcel(s) count'),
                            'name' => 'parcels_count',
                            'type' => 'text',
                            'class' => 'fixed-width-xxl',
                            'required' => true,
                        ],
                        [
                            'label' => $this->l('Pallet(s) count'),
                            'name' => 'pallets_count',
                            'type' => 'text',
                            'class' => 'fixed-width-xxl',
                            'required' => false,
                        ],
                        [
                            'label' => $this->l('Comment for courier'),
                            'name' => 'comment_for_courier',
                            'type' => 'text',
                            'class' => 'fixed-width-xxl',
                            'required' => false,
                        ],
                    ],
                ],
            ],
        ];
        /** @var PhonePrefixRepository $phonePrefixRepository */
        $phonePrefixRepository = $this->module->getModuleContainer(PhonePrefixRepository::class);

        /** @var PhoneInputBuilder $phoneInputBuilder */
        $phoneInputBuilder = $this->module->getModuleContainer(PhoneInputBuilder::class);

        $saveBtnClasses = 'btn btn-default pull-right js-col-request-save';

        if (!$this->object->id) {
            $this->fields_form[2]['form']['submit'] = [
                'title' => $this->l('Save'),
                'class' => $saveBtnClasses,
            ];
        }

        $this->fields_value['prefill_placeholder'] = $this->renderPrefillSelect('sender_');

        if (Tools::getIsset('submitAdddpd_courier_request')) {
            $phoneData['sender_phone_code'] = Tools::getValue('sender_phone_code');
            $phoneData['sender_phone'] = Tools::getValue('sender_phone');

        } elseif (Tools::getIsset('id_dpd_courier_request')) {

            /** @var CourierRequestRepository $courierRequestRepository */
            $courierRequestRepository = $this->module->getModuleContainer(CourierRequestRepository::class);
            $phoneData = $courierRequestRepository->getPhonesAndCodes(
                Tools::getValue('id_dpd_courier_request')
            );
        }

        if (!isset($phoneData) || !$phoneData) {
            $countryCalliungCode = null;

            $phoneData['sender_phone_code'] = $countryCalliungCode;
            $phoneData['sender_phone'] = null;
        }
        $phoneData['sender_phone_code_list'] = $phonePrefixRepository->getCallPrefixes();
        $this->fields_value['sender_phone'] = $phoneInputBuilder->renderPhoneInput(
            'sender_phone',
            $phoneData['sender_phone_code_list'],
            $phoneData['sender_phone_code'],
            $phoneData['sender_phone']
        );
        $this->fields_value['fix_phone'] = $phoneInputBuilder->renderPhoneInput(
            'fix_phone',
            $phoneData['sender_phone_code_list']
        );

        if (!Tools::getValue('pick_up_time') && !Tools::getValue('sender_work_until')) {
            $this->fields_value['pick_up_time'] = TimeZoneUtility::getCourierDefaultPickUpTime();
            $this->fields_value['sender_work_until'] = TimeZoneUtility::getCourierDefaultWorkUntil();
        }
        if (!Tools::getValue('order_nr')) {
            $this->fields_value['order_nr'] = (new DateTime())->getTimestamp();
        }
    }

    private function initList()
    {
        $this->addRowAction('viewCourierRequest');

        $this->fields_list['id_dpd_courier_request'] = [
            'title' => $this->l('ID'),
            'type' => 'text',
        ];

        $this->fields_list['order_nr'] = [
            'title' => $this->l('Order Nr'),
            'type' => 'text',
        ];

        $this->fields_list['pick_up_time'] = [
            'title' => $this->l('Desired pick-up time'),
            'type' => 'datetime',
        ];

        $this->fields_list['sender_work_until'] = [
            'title' => $this->l('Last pick-up time'),
            'type' => 'text',
        ];

        $this->fields_list['weight'] = [
            'title' => $this->l('Weight'),
            'type' => 'text',
        ];

        $this->fields_list['parcels_count'] = [
            'title' => $this->l('Parcel(s) count'),
            'type' => 'text',
        ];
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        //checks if its add or update or submit
        if (Tools::isSubmit('adddpd_courier_request')
            || Tools::isSubmit('updatedpd_courier_request')
            || Tools::isSubmit('submitAdddpd_courier_request')) {
            $this->addJS($this->module->getLocalPath() . 'views/js/admin/zip_code_hint.js');
            $this->addJS($this->module->getLocalPath() . 'views/js/admin/courier_request.js');

            Media::addJsDef([
                'dpdAjaxUrl' =>
                    $this->context->link->getAdminLink(DPDBaltics::ADMIN_AJAX_CONTROLLER),
            ]);
        }

        $this->addCSS($this->module->getPathUri() . 'views/css/admin/courier_request.css');
    }

    private function renderPrefillSelect($prefix)
    {
        /** @var AddressRepository $addressRepository */
        $addressRepository = $this->module->getModuleContainer(AddressRepository::class);
        $addresses = $addressRepository->findAllByShop();

        $this->context->smarty->assign('address_templates', $addresses);
        $this->context->smarty->assign('prefix', $prefix);

        return $this->context->smarty->fetch(
            $this->module->getLocalPath() . 'views/templates/admin/partials/address-select.tpl'
        );
    }

    //todo:: if Exception is caught it returns to list.
    public function postProcess()
    {
        if (Tools::isSubmit('submitAdddpd_courier_request')) {
            /** @var FormDataConverter $formDataConverter */
            /** @var CourierRequestValidator $courierRequestValidator */
            $formDataConverter = $this->module->getModuleContainer(FormDataConverter::class);
            $courierRequestValidator = $this->module->getModuleContainer(CourierRequestValidator::class);
            $data = Tools::getAllValues();

            /** @var CourierRequestData $courierRequestObj */
            $courierRequestObj = $formDataConverter->convertCourierRequestFormDataToCourierRequestObj($data);

            $countryIso = Configuration::get(Config::WEB_SERVICE_COUNTRY);
            if (!$courierRequestValidator->validate($courierRequestObj, $countryIso)) {
                $this->errors[] = sprintf(
                    $this->l('You can\'t create courier request because minimal time interval has to be at least %s minutes'),
                    Config::getMinimalTimeIntervalForCountry($countryIso)
                );
                return parent::postProcess();
            }

            /** @var CourierRequestService $courierRequestService */
            $courierRequestService = $this->module->getModuleContainer(CourierRequestService::class);
            try {
                $response = $courierRequestService->createCourierRequest($courierRequestObj);
            } catch (DPDBalticsAPIException $e) {
                /** @var ExceptionService $exceptionService */
                $exceptionService = $this->module->getModuleContainer(ExceptionService::class);
                $this->errors[] = $exceptionService->getErrorMessageForException(
                    $e,
                    $exceptionService->getAPIErrorMessages()
                );
                return parent::postProcess();
            } catch (Exception $e) {
                $this->errors[] = $e->getMessage();
                return parent::postProcess();
            }
            if ($response['status']) {
                $this->confirmations[] = $response['message'];
            } else {
                if ($response['message'] !== 'Pickup order to time wrong for Product 1') {
                    $this->errors[] = $response['message'];
                } else {
                    $this->errors[] = $this->l('Your selected time frame is incorrect, please select different datetime range.');
                }
            }
        }

        parent::postProcess();
    }

    public function displayViewCourierRequestLink($token, $idCourierRequest)
    {
        $courierRequestUrl =
            $this->context->link->getAdminLink(DPDBaltics::ADMIN_COURIER_REQUEST_CONTROLLER, false);
        $courierRequestUrl .=
            '&id_dpd_courier_request=' . $idCourierRequest . '&updatedpd_courier_request&token=' . $token;

        $params = [
            'href' => $courierRequestUrl,
            'action' => $this->l('View'),
            'icon' => 'icon-search-plus'
        ];

        return $this->renderListAction($params);
    }

    protected function renderListAction(array $params)
    {
        $this->context->smarty->assign($params);

        return $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/list-action.tpl');
    }
}

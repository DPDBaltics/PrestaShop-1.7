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


use Invertus\dpdBaltics\Repository\LogsRepository;
use Invertus\dpdBaltics\Service\LogsService;

class LogsServiceTest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider dataProvider
     */
    public function testHideUsernameAndPasswordFromRequest($request, $result)
    {
        $module = Module::getInstanceByName('dpdbaltics');
        $logsService = new LogsService(new LogsRepository(), $module);
        $newRequest = $logsService->hideUsernameAndPasswordFromRequest($request);

        $this->assertEquals($newRequest, $result);
    }

    public function dataProvider()
    {
        return [
            'case1' =>
                [
                    'request' => 'https://lv.integration.dpd.eo.pl/ws-mapper-rest/createShipment_?username=test1234&password=%24this-%3Epassword&name1=tes&street=belgie&city=Jonava&country=LT&pcode=50186&num_of_parcel=1&parcel_type=PS-COD&phone=%2B370123&fetchGsPUDOpoint=1&parcelshop_id=LT10096&name2&weight=10.000000&idm_sms_number=123&email=marius.gudauskis%40invertus.eu&order_number=97&order_number1=&order_number2=&order_number3=&parcel_number&remark&cod_amount=35.090000&cod_purpose&id_check_id&id_check_name&dnote_reference&predict&timeframe_from&timeframe_to&shipment_id ',
                    'result' => 'https://lv.integration.dpd.eo.pl/ws-mapper-rest/createShipment_?username=&password=&name1=tes&street=belgie&city=Jonava&country=LT&pcode=50186&num_of_parcel=1&parcel_type=PS-COD&phone=%2B370123&fetchGsPUDOpoint=1&parcelshop_id=LT10096&name2&weight=10.000000&idm_sms_number=123&email=marius.gudauskis%40invertus.eu&order_number=97&order_number1=&order_number2=&order_number3=&parcel_number&remark&cod_amount=35.090000&cod_purpose&id_check_id&id_check_name&dnote_reference&predict&timeframe_from&timeframe_to&shipment_id ',
                ],
            'case2' =>
                [
                    'request' => 'https://lv.integration.dpd.eo.pl/ws-mapper-rest/createShipment_?username=test1234&password=qwerty12&name1=tes&street=belgie&city=Jonava&country=%24this-%3Ecountry&pcode=50186&num_of_parcel=1&parcel_type=PS-COD&phone=%2B370123&fetchGsPUDOpoint=1&parcelshop_id=LT10096&name2&weight=10.000000&idm_sms_number=123&email=marius.gudauskis%40invertus.eu&order_number=97&order_number1=&order_number2=&order_number3=&parcel_number&remark&cod_amount=35.090000&cod_purpose&id_check_id&id_check_name&dnote_reference&predict&timeframe_from&timeframe_to&shipment_id ',
                    'result' => 'https://lv.integration.dpd.eo.pl/ws-mapper-rest/createShipment_?username=&password=&name1=tes&street=belgie&city=Jonava&country=%24this-%3Ecountry&pcode=50186&num_of_parcel=1&parcel_type=PS-COD&phone=%2B370123&fetchGsPUDOpoint=1&parcelshop_id=LT10096&name2&weight=10.000000&idm_sms_number=123&email=marius.gudauskis%40invertus.eu&order_number=97&order_number1=&order_number2=&order_number3=&parcel_number&remark&cod_amount=35.090000&cod_purpose&id_check_id&id_check_name&dnote_reference&predict&timeframe_from&timeframe_to&shipment_id ',
                ]
        ];
    }
}

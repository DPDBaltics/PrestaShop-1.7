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


use Invertus\dpdBaltics\Adapter\AddressAdapter;
use PHPUnit\Framework\TestCase;

class AddressAdapterTest extends TestCase
{
    /**
     * @dataProvider getPostCodeData
     */
    public function testFormatPostCodeByCountry($postCode, $isoCode, $expectedResult)
    {

        $addressAdapter = new AddressAdapter();
        $result = $addressAdapter->formatPostCodeByCountry($postCode, $isoCode);

        $this->assertEquals($expectedResult, $result);
    }

    public function getPostCodeData()
    {
        yield 'Post code Lithuania' => [
            'LT56120',
            'LT',
            '56120'
        ];
        yield 'Post code Ireland' => [
            'V42 A393',
            'IE',
            'V42A393'
        ];
        yield 'Post code Estonia' => [
            '10EE14A9',
            'EE',
            '10149'
        ];
        yield 'Post code Latvia' => [
            'LV-1050',
            'LV',
            '1050'
        ];

        yield 'Post code UK' => [
            'CH 65UZ',
            'GB',
            'CH65UZ'
        ];

        yield 'Post code NL' => [
            '1012 AB',
            'NL',
            '1012AB'
        ];
    }
}
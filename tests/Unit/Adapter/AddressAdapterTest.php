<?php

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
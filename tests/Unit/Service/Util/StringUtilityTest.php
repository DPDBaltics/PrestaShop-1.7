<?php

use Invertus\dpdBaltics\Util\StringUtility;

class StringUtilityTest extends PHPUnit_Framework_TestCase
{
    public function testTrimString(){
        $stringDataMock ="adĖsdĖsdsšsdasdasdasdasdasdasdasdasdasdasdasdasd";
        $stringUtility = new StringUtility();
        $trimmedTestString = $stringUtility->trimString($stringDataMock);
        $expectation = 'adĖsdĖsdsšsdasdasdasdasdasdasdasdasdasdas...';
        $this->assertSame($trimmedTestString, $expectation);
    }

    public function testRemoveSpecialCharacters() {
        $stringDataMock ="adĖsdĖsdsš";
        $stringUtility = new StringUtility();
        $formattedTestString = $stringUtility->removeSpecialCharacters($stringDataMock);
        $expectation = "adEsdEsdss";
        $this->assertEquals( $expectation, $formattedTestString);
    }

}

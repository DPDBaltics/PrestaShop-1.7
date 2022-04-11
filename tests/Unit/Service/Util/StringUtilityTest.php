<?php

use Invertus\dpdBaltics\Util\StringUtility;

class StringUtilityTest extends PHPUnit_Framework_TestCase
{
    public function testTrimString(){
        $stringDataMock ="Nam quis nulla. Integer malesuada. In in enim a arcu imperdiet malesuada. Sed vel lectus.";

        $expectation = "Nam quis nulla. Integer malesuada. In in eni...";

        $stringUtility = new StringUtility();
        $trimmedTestString = $stringUtility->trimString($stringDataMock);

        $this->assertSame($trimmedTestString, $expectation);
    }

    public function testRemoveSpecialCharacters() {
        $stringDataMock ="vėlyvųjų viduramžių standartas";
        $stringUtility = new StringUtility();
        $formattedTestString = $stringUtility->removeSpecialCharacters($stringDataMock);
        $expectation = "velyvuju viduramziu standartas";
        $this->assertEquals( $expectation, $formattedTestString);
    }

}

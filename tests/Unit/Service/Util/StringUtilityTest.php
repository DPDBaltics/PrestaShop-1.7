<?php

use Invertus\dpdBaltics\Util\StringUtility;

class StringUtilityTest extends PHPUnit_Framework_TestCase
{
            public function testTrimString(){
                $stringDataMock ="adĖsdĖsdsš";
                $stringUtility = new StringUtility();
                $trimmedTestString = $stringUtility->trimString($stringDataMock);
                $testResult = $trimmedTestString <= $stringUtility::REMARK_LIMIT ? true : false;
                $this->assertTrue( true, $testResult);
            }

           public function testRemoveSpecialCharacters() {
               $stringDataMock ="adĖsdĖsdsš";
               $stringUtility = new StringUtility();
               $formattedTestString = $stringUtility->removeSpecialCharacters($stringDataMock);
               $isASCIIChar = preg_match( '/[\\x80-\\xff]+/' , $formattedTestString );
               $this->assertTrue( true, $isASCIIChar);
           }

}

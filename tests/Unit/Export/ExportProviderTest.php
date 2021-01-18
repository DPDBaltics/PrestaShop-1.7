<?php

use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Service\Export\ExportProvider;

class ExportProviderTest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider dataProvider
     *
     * @param $exportOption
     */
    public function testExport($exportOption)
    {
        $module = Module::getInstanceByName('dpdbaltics');
        $exportProvider = new ExportProvider($module);
        $exportable = $exportProvider->returnExportable($exportOption);
        $this->assertNotEquals($exportable, '');
    }

    public function dataProvider()
    {
        return [
            'zones' => [
                'exportOption' => Config::IMPORT_EXPORT_OPTION_ZONES,
            ]
        ];
    }
}

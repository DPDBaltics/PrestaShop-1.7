<?php

use Invertus\dpdBaltics\DTO\ShipmentData;
use Invertus\dpdBaltics\Repository\LogsRepository;
use Invertus\dpdBaltics\Repository\ParcelShopRepository;
use Invertus\dpdBaltics\Repository\PudoRepository;
use Invertus\dpdBaltics\Service\LogsService;
use Invertus\dpdBaltics\Service\PudoService;

class PudoServiceTest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider dataProvider
     */
    public function testRepopulatePudoDataInShipment($shipmentDataMock, $dpdPudoDataMock, $resultData)
    {
        $dpdPudo = $this->getDpdPudoMock();
        $dpdPudo->pudo_id = $dpdPudoDataMock[0];
        $dpdPudo->country_code = $dpdPudoDataMock[1];
        $dpdPudo->city = $dpdPudoDataMock[2];
        $dpdPudo->street = $dpdPudoDataMock[3];

        $pudoRepository = $this->getPudoRepositoryMock();
        $pudoRepository->method('getIdByCart')->willReturn(1);
        $pudoRepository->method('getDPDPudo')->willReturn($dpdPudo);

        $shipmentData = new ShipmentData();
        $shipmentData->setSelectedPudoId($shipmentDataMock[0]);
        $shipmentData->setSelectedPudoIsoCode($shipmentDataMock[1]);
        $shipmentData->setCity($shipmentDataMock[2]);
        $shipmentData->setDpdStreet($shipmentDataMock[3]);


        $pudoService = new PudoService(
            $pudoRepository,
            $this->getMockBuilder('Smarty')->allowMockingUnknownTypes()->getMock(),
            $this->getMockBuilder('DPDbaltics')->allowMockingUnknownTypes()->getMock(),
            $this->createMock(\Invertus\dpdBaltics\Service\API\ParcelShopSearchApiService::class),
            $this->getMockBuilder('Language')->allowMockingUnknownTypes()->getMock(),
            $this->createMock(\Invertus\dpdBaltics\Service\GoogleApiService::class),
            $this->createMock(ParcelShopRepository::class),
            $this->createMock(\Invertus\dpdBaltics\Factory\ShopFactory::class)
        );

        $shipmentData = $pudoService->repopulatePudoDataInShipment($shipmentData, 1);
        $shipmentResult = [
            $shipmentData->getSelectedPudoId(),
            $shipmentData->getSelectedPudoIsoCode(),
            $shipmentData->getCity(),
            $shipmentData->getDpdStreet()
        ];

        $this->assertInstanceOf(ShipmentData::class, $shipmentData);
        $this->assertEquals($resultData, $shipmentResult);
    }

    private function getPudoRepositoryMock()
    {
        return $this->getMockBuilder(PudoRepository::class)
            ->setMethods([
                'getIdByCart',
                'getDPDPudo',
            ])
            ->disableOriginalConstructor()
            ->getMock();
    }


    private function getDpdPudoMock()
    {
        return $this->getMockBuilder(ObjectModel::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function dataProvider()
    {
        yield 'Valid data shipment full' => [
            ['LT12456', 'LT', 'Kaunas', 'Test Street'],
            ['LT12456', 'LT', 'Kaunasasas', 'Test address From object'],
            ['LT12456', 'LT', 'Kaunas', 'Test Street'],
        ];

        yield 'City changed' => [
            ['LT12456', 'LT', null, 'Test Street'],
            ['LT12456', 'LT', 'Kaunasasas', 'Test address From object'],
            ['LT12456', 'LT', 'Kaunasasas', 'Test Street'],
        ];
        yield 'Pudo ID changed' => [
            [null, 'LT', 'Kaunas', 'Test Street'],
            ['LT12456', 'LT', 'Kaunasasas', 'Test address From object'],
            ['LT12456', 'LT', 'Kaunas', 'Test Street'],
        ];
        yield 'Country changed' => [
            ['LT12456', null, 'Kaunas', 'Test Street'],
            ['LT12456', 'LT', 'Kaunasasas', 'Test address From object'],
            ['LT12456', 'LT', 'Kaunas', 'Test Street'],
        ];
        yield 'Address changed' => [
            ['LT12456', 'LT', 'Kaunas', null],
            ['LT12456', 'LT', 'Kaunasasas', 'Test address From object'],
            ['LT12456', 'LT', 'Kaunas', 'Test address From object'],
        ];
    }
}

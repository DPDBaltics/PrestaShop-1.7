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



namespace Invertus\dpdBaltics\Service\API;

use Exception;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Util\FileDownload;
use Invertus\dpdBalticsApi\Api\DTO\Request\ParcelPrintRequest;
use Invertus\dpdBalticsApi\Api\DTO\Response\ParcelPrintResponse;
use Invertus\dpdBalticsApi\Factory\APIRequest\ParcelPrintFactory;

class LabelApiService
{
    /**
     * @var ParcelPrintFactory
     */
    private $parcelPrintFactory;

    /**
     * ApiService constructor.
     * @param FileDownload $downloadService
     */
    public function __construct(FileDownload $downloadService, ParcelPrintFactory $parcelPrintFactory)
    {
        $this->downloadService = $downloadService;
        $this->parcelPrintFactory = $parcelPrintFactory;
    }

    public function printLabel($plNumbers, $format = false, $position = false, $isReturn = false)
    {
        //TODO setup sending logic here in this functionw
        $parcelPrintRequest = new ParcelPrintRequest(
            $plNumbers
        );

        $type = Config::DEFAULT_LABEL_TYPE;
        if ($format) {
            $separatedFormat = $this->separateLabelFormat($format);
            $parcelPrintRequest->setPrintFormat($separatedFormat['format']);
            $type = $separatedFormat['type'];
            $parcelPrintRequest->setPrintType($separatedFormat['type']);
        }
        if ($position) {
            $parcelPrintRequest->setPrintPosition($position);
        }
        $parcelPrinter = $this->parcelPrintFactory->makeParcelPrint();

        /** @var ParcelPrintResponse $parcelPrintResponse */
        $parcelPrintResponse = $parcelPrinter->printParcel($parcelPrintRequest);

        if ($parcelPrintResponse->getStatus() === Config::API_SUCCESS_STATUS) {
            $name = str_replace('|', '_', $plNumbers);
            $name = substr($name, 0, Config::MULTIPLE_LABEL_NAME_MAX_SIZE);
            $this->downloadService->dumpFile($parcelPrintResponse->getPdf(), $name, $type);
            return $parcelPrintResponse;
        }

        return $parcelPrintResponse;
    }

    private function separateLabelFormat($format)
    {
        $separatedFormat = explode('_', $format);
        if (count($separatedFormat) === 2) {
            return [
                'format' => $separatedFormat[0],
                'type' => $separatedFormat[1]
            ];
        }
        return [
            'format' => '',
            'type' => $separatedFormat[0]
        ];
    }
}

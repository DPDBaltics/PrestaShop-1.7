<?php


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

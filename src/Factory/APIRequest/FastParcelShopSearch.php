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

namespace Invertus\dpdBaltics\Factory\APIRequest;

use Exception;
use Invertus\dpdBalticsApi\Api\ApiRequest;
use Invertus\dpdBalticsApi\Api\DTO\Request\ParcelShopSearchRequest;
use Invertus\dpdBalticsApi\Api\DTO\Response\ParcelShopSearchResponse;
use Invertus\dpdBalticsApi\ApiConfig\ApiConfig;
use Invertus\dpdBalticsApi\Exception\DPDBalticsAPIException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Fast ParcelShopSearch that uses simple JSON parsing instead of slow Symfony serializer.
 * This can handle large responses (3000+ parcel shops) without timeout.
 */
class FastParcelShopSearch
{
    /**
     * @var ApiRequest
     */
    private $apiRequest;

    /**
     * @var FastParcelShopResponseParser
     */
    private $parser;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ApiRequest $apiRequest
     * @param LoggerInterface|null $logger
     */
    public function __construct(ApiRequest $apiRequest, $logger = null)
    {
        $this->apiRequest = $apiRequest;
        $this->parser = new FastParcelShopResponseParser();
        $this->logger = $logger instanceof LoggerInterface ? $logger : new NullLogger();
    }

    /**
     * Search for parcel shops using fast parsing.
     *
     * @param ParcelShopSearchRequest $request
     * @return ParcelShopSearchResponse
     * @throws DPDBalticsAPIException
     */
    public function parcelShopSearch(ParcelShopSearchRequest $request)
    {
        try {
            $response = $this->apiRequest->post(
                ApiConfig::SQ_PARCEL_SHOP_SEARCH,
                [
                    'query' => $request->jsonSerialize(),
                    'verify' => false,
                ]
            );
        } catch (Exception $e) {
            $this->logger->error(sprintf(
                '[FastParcelShopSearch] API call FAILED | Error: %s',
                $e->getMessage()
            ));

            throw new DPDBalticsAPIException(
                'An error occurred trying to search for parcel shops: ' . $e->getMessage(),
                DPDBalticsAPIException::PARCEL_SHOP_SEARCH,
                $e
            );
        }

        // Handle null or empty response
        if (empty($response)) {
            $this->logger->error('[FastParcelShopSearch] API returned empty response');
            $emptyResponse = new ParcelShopSearchResponse();
            $emptyResponse->setStatus('err');
            $emptyResponse->setErrLog('API returned empty response');
            $emptyResponse->setParcelShops(array());
            return $emptyResponse;
        }

        // Convert stdClass to array (Unirest returns decoded JSON as object)
        $responseData = json_decode(json_encode($response), true);

        // Handle JSON conversion failure
        if (!is_array($responseData)) {
            $this->logger->error('[FastParcelShopSearch] Failed to convert response to array');
            $errorResponse = new ParcelShopSearchResponse();
            $errorResponse->setStatus('err');
            $errorResponse->setErrLog('Failed to parse API response');
            $errorResponse->setParcelShops(array());
            return $errorResponse;
        }

        return $this->parser->parse($responseData);
    }
}

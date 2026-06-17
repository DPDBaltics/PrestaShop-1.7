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

use Invertus\dpdBalticsApi\Api\ApiRequest;
use Invertus\dpdBalticsApi\Factory\APIParamsFactoryInterface;
use Invertus\dpdBalticsApi\Factory\APIRequest\ParcelShopSearchFactory;
use Psr\Log\LoggerInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Extended factory for ParcelShopSearch that uses:
 * 1. Longer API timeout (120s instead of 20s)
 * 2. Fast response parser (bypasses slow Symfony serializer)
 *
 * This allows importing large countries like Poland (3000+ parcel shops)
 * even on servers with 30-second PHP timeout limits.
 */
class ExtendedParcelShopSearchFactory extends ParcelShopSearchFactory
{
    const PARCEL_SHOP_SEARCH_TIMEOUT = 120;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var APIParamsFactoryInterface
     */
    private $APIParamsFactory;

    public function __construct(LoggerInterface $logger, APIParamsFactoryInterface $APIParamsFactory)
    {
        parent::__construct($logger, $APIParamsFactory);
        $this->logger = $logger;
        $this->APIParamsFactory = $APIParamsFactory;
    }

    /**
     * Create FastParcelShopSearch with extended timeout and fast parser.
     *
     * @return FastParcelShopSearch
     */
    public function makeParcelShopSearch()
    {
        $httpClientFactory = new ExtendedApiClient(
            $this->APIParamsFactory->getUrl(),
            $this->APIParamsFactory->getUsername(),
            $this->APIParamsFactory->getPassword(),
            self::PARCEL_SHOP_SEARCH_TIMEOUT
        );

        $apiRequest = new ApiRequest(
            $httpClientFactory,
            $this->logger,
            $this->APIParamsFactory->getModuleVersion(),
            $this->APIParamsFactory->getPSVersion()
        );

        // Use FastParcelShopSearch with simple JSON parser instead of slow Symfony serializer
        return new FastParcelShopSearch($apiRequest, $this->logger);
    }
}

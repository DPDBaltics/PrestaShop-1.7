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

use Invertus\dpdBalticsApi\Factory\APIRequest\ApiClient;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Extended API client with configurable timeout.
 * Used to override the hardcoded 20-second timeout in the vendor package
 * for large API requests like Poland parcel shop imports.
 */
class ExtendedApiClient extends ApiClient
{
    /**
     * @var int
     */
    private $timeout;

    /**
     * @param string $url
     * @param string $username
     * @param string $password
     * @param int $timeout Timeout in seconds (default 120 for large imports)
     */
    public function __construct($url, $username, $password, $timeout = 120)
    {
        parent::__construct($url, $username, $password);
        $this->timeout = $timeout;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }
}

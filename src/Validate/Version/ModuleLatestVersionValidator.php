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

namespace Invertus\dpdBaltics\Validate\Version;

use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Infrastructure\Utility\ModuleVersionUtility;
use Invertus\dpdBaltics\Validate\ValidatorInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ModuleLatestVersionValidator implements ValidatorInterface
{
    const FILE_NAME = 'ModuleLatestVersionValidator';

    /** @var ModuleVersionUtility */
    private $moduleVersionUtility;

    public function __construct(ModuleVersionUtility $moduleVersionUtility)
    {
        $this->moduleVersionUtility = $moduleVersionUtility;
    }

    /**
     * Checks and validates if the module is the latest version from GitHub
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function validate(): bool
    {
        try {
            return $this->moduleVersionUtility->isVersionLatest($this->getLatestModuleVersionGithub());
        } catch (\Exception $e) {
            throw new \Exception(sprintf('%s - Unable to get the latest module version from GitHub', self::FILE_NAME));
        }
    }

    /**
     * Fetches the latest module version from the GitHub API.
     *
     * This function sends a request to the GitHub API to retrieve the latest release version.
     * It ensures proper error handling for cURL failures and JSON decoding errors.
     *
     * @throws \RuntimeException If the cURL request fails.
     * @throws \UnexpectedValueException If the API response is invalid or missing the expected data.
     *
     * @return string The latest module version (without the leading "v").
     */
    private function getLatestModuleVersionGithub(): string
    {
        $request = curl_init();

        curl_setopt_array($request, [
            CURLOPT_URL            => Config::DPD_GITHUB_REPO_RELEASE_LATEST_API_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT      => 'PrestaShop',
        ]);

        $response = curl_exec($request);

        if ($response === false) {
            $errorMessage = curl_error($request);
            curl_close($request);
            throw new \RuntimeException("cURL error: " . $errorMessage);
        }

        curl_close($request);

        $decodedResponse = json_decode($response);

        if (!isset($decodedResponse->tag_name)) {
            throw new \UnexpectedValueException("Invalid response from GitHub API.");
        }

        return preg_replace('/^v/', '', $decodedResponse->tag_name);
    }
}
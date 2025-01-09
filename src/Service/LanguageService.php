<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    INVERTUS, UAB www.invertus.eu <support@invertus.eu>
 * @copyright Copyright (c) permanent, INVERTUS, UAB
 * @license   Addons PrestaShop license limitation
 * @see       /LICENSE
 *
 *  International Registered Trademark & Property of INVERTUS, UAB
 */

namespace Invertus\dpdBaltics\Service;

use Language;

if (!defined('_PS_VERSION_')) {
    exit;
}

class LanguageService
{
    public function getShopLanguagesIsoCodes()
    {
        $languages = [];

        foreach (Language::getLanguages() as $language) {
            $languages[$language['iso_code']] = $language['iso_code'];
        }

        return $languages;
    }
}

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

namespace Invertus\dpdBaltics\Provider;

use Context;
use DPDBaltics;
use Invertus\dpdBaltics\Config\Config;

class ImportExportURLProvider
{
    private $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function getImportExportUrl()
    {
        return $this->context->link->getAdminLink(
            DPDBaltics::ADMIN_IMPORT_EXPORT_CONTROLLER,
            true
        );
    }
}

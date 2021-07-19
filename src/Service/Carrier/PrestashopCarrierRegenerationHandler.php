<?php

declare(strict_types=1);

namespace Invertus\dpdBaltics\Service\Carrier;

use Invertus\dpdBaltics\Exception\DpdCarrierException;
use Invertus\dpdBaltics\Install\Installer;

class PrestashopCarrierRegenerationHandler
{
    private $moduleInstaller;

    public function __construct(Installer $moduleInstaller)
    {
        $this->moduleInstaller = $moduleInstaller;
    }

    /**
     * @throws \PrestaShopException
     * @throws \PrestaShopDatabaseException
     * @throws DpdCarrierException
     */
    public function handle(): void
    {
        if (!$this->moduleInstaller->deleteModuleCarriers()) {
            throw new DpdCarrierException('There was an error while deleting carriers', 500);
        }

        if (!$this->moduleInstaller->createCarriers()) {
            throw new DpdCarrierException('There was an error while creating carriers', 500);
        }
    }
}

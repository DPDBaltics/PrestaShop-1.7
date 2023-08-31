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

namespace Invertus\dpdBaltics\Infrastructure\Bootstrap\Uninstall;

use Invertus\dpdBaltics\Core\Shared\Repository\TabRepositoryInterface;
use Invertus\dpdBaltics\Infrastructure\Bootstrap\Exception\CouldNotUninstallModule;
use Invertus\dpdBaltics\Infrastructure\Bootstrap\ModuleTabs;
use Tab as PrestashopTab;

class ModuleTabUninstaller implements UninstallerInterface
{
    private $moduleTabs;
    private $tabRepository;

    public function __construct(
        ModuleTabs $moduleTabs,
        TabRepositoryInterface $tabRepository
    ) {
        $this->moduleTabs = $moduleTabs;
        $this->tabRepository = $tabRepository;
    }

    public function init()
    {
        $tabs = $this->moduleTabs->getTabs();

        foreach ($tabs as $tab) {
            $this->uninstallTab($tab['class_name']);
        }
    }

    /**
     * @throws CouldNotUninstallModule
     */
    private function uninstallTab($className)
    {
        /** @var PrestashopTab|null $tab */
        $tab = $this->tabRepository->findOneBy([
            'class_name' => $className,
        ]);

        //NOTE: if it's already deleted we should just continue with our code.
        if (!$tab) {
            return;
        }

        try {
            $tab->delete();
        } catch (\Exception $exception) {
            throw CouldNotUninstallModule::failedToUninstallModuleTab($exception, $className);
        }
    }
}

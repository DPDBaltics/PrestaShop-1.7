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
namespace Invertus\dpdBaltics\Infrastructure\Bootstrap\Install;

use Invertus\dpdBaltics\Core\Shared\Repository\LanguageRepositoryInterface;
use Invertus\dpdBaltics\Core\Shared\Repository\TabRepositoryInterface;
use Invertus\dpdBaltics\Infrastructure\Adapter\ModuleFactory;
use Invertus\dpdBaltics\Infrastructure\Bootstrap\Exception\CouldNotInstallModule;
use Invertus\dpdBaltics\Infrastructure\Bootstrap\ModuleTabs;
use Invertus\dpdBaltics\Infrastructure\Utility\VersionUtility;
use Tab as PrestashopTab;

final class ModuleTabInstaller implements InstallerInterface
{
    private $module;
    private $moduleTabs;
    private $tabRepository;
    private $languageRepository;

    public function __construct(
        ModuleFactory $moduleFactory,
        ModuleTabs $moduleTabs,
        TabRepositoryInterface $tabRepository,
        LanguageRepositoryInterface $languageRepository
    ) {
        $this->module = $moduleFactory->getModule();
        $this->moduleTabs = $moduleTabs;
        $this->tabRepository = $tabRepository;
        $this->languageRepository = $languageRepository;
    }

    public function init()
    {
        $tabs = $this->moduleTabs->getTabs();

        foreach ($tabs as $tab) {
            $this->installTab(
                $tab['class_name'],
                $tab['parent_class_name'],
                $tab['name'],
                $tab['visible']
            );
        }
    }

    /**
     * @throws CouldNotInstallModule
     */
    private function installTab($className, $parentClassName, $names, $visible)
    {
        /** @var PrestashopTab|null $tab */
        $tab = $this->tabRepository->findOneBy([
            'class_name' => $className,
        ]);

        /** @var PrestashopTab|null $tab */
        $parentTab = $this->tabRepository->findOneBy([
            'class_name' => $parentClassName,
        ]);

        //NOTE: no need to create it again.
        if ($tab) {
            return;
        }

        $tabEntity = new PrestashopTab();
        $tabEntity->class_name = $className;
        $tabEntity->id_parent = $parentTab ? $parentTab->id : -1;
        $tabEntity->module = $this->module->name;
        $tabEntity->active = $visible;

        /** @var \Language[] $languages */
        $languages = $this->languageRepository->getAllActive();

        foreach ($languages as $language) {
            $tabEntity->name[$language->id] = isset($names[$language->iso_code]) ? pSQL($names[$language->iso_code]) : pSQL($names['en']);
        }

        try {
            $tabEntity->save();
        } catch (\Exception $exception) {
            throw CouldNotInstallModule::failedToInstallModuleTab($exception, $className);
        }
    }
}
